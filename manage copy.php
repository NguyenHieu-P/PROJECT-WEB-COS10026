<?php
// Only a logged-in manager can open this page (login.php sets $_SESSION["manager"]).
session_start();
if (!isset($_SESSION["manager"])) {
    header("Location: login.php?required=1");
    exit();
}
?>
<link href="styles.css" rel="stylesheet">

<?php
/*
 * manage.php - HR manager page for Lumina Learn.
 * Queries supported on the eoi table:
 *   1. List all EOIs
 *   2. List EOIs for one position (job reference number)
 *   3. List EOIs for one applicant (first name, last name or both)
 *   4. Delete all EOIs with a given job reference number
 *   5. Change the Status of one EOI
 * All POST actions run BEFORE any HTML is sent, so header() redirects work.
 */

require_once("settings.php");

// Return false instead of throwing exceptions (PHP 8.1+ throws by default).
mysqli_report(MYSQLI_REPORT_OFF);

$job_titles     = ["FE101" => "Front-End Web Developer", "UX102" => "UX/UI Designer"];
$valid_statuses = ["New", "Current", "Final"];
$sort_columns   = [
    "eoi"    => "EOInumber",
    "job"    => "JobReferenceNumber",
    "first"  => "FirstName",
    "last"   => "LastName",
    "status" => "Status"
];
$sort_labels = [
    "eoi"    => "EOI number",
    "job"    => "Job reference",
    "first"  => "First name",
    "last"   => "Last name",
    "status" => "Status"
];
$filter_keys = ["job_ref", "first_name", "last_name", "status", "sort", "order"];

// Escape output. double_encode = false because process_eoi.php already
// stores values through htmlspecialchars(); this avoids showing "&amp;amp;".
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8", false);
}

// Read one text value from $_GET or $_POST safely (ignores arrays).
function read_text($source, $key, $max_length) {
    if (!isset($source[$key]) || !is_string($source[$key])) {
        return "";
    }
    return substr(trim($source[$key]), 0, $max_length);
}

// Build a query string that keeps the current filters (drops empty/default values).
function filters_to_query($filters, $extra = []) {
    $params = [];
    foreach ($filters as $key => $value) {
        if ($value === "" || ($key === "sort" && $value === "eoi") || ($key === "order" && $value === "desc")) {
            continue;
        }
        $params[$key] = $value;
    }
    return http_build_query(array_merge($params, $extra));
}

// Hidden inputs so that POST forms remember the current filters.
function keep_filter_inputs($filters) {
    $html = "";
    foreach ($filters as $key => $value) {
        if ($value !== "") {
            $html .= '<input type="hidden" name="keep[' . e($key) . ']" value="' . e($value) . '">';
        }
    }
    return $html;
}

/* ---------- 1. Read and validate the filters from the URL ---------- */
$filters = [
    "job_ref"    => strtoupper(read_text($_GET, "job_ref", 10)),
    "first_name" => read_text($_GET, "first_name", 20),
    "last_name"  => read_text($_GET, "last_name", 20),
    "status"     => read_text($_GET, "status", 10),
    "sort"       => read_text($_GET, "sort", 10),
    "order"      => read_text($_GET, "order", 4)
];
if (!preg_match("/^[A-Z0-9]{5}$/", $filters["job_ref"])) {
    $filters["job_ref"] = "";
}
if (!in_array($filters["status"], $valid_statuses, true)) {
    $filters["status"] = "";
}
if (!array_key_exists($filters["sort"], $sort_columns)) {
    $filters["sort"] = "eoi";
}
$filters["order"] = ($filters["order"] === "asc") ? "asc" : "desc";

/* ---------- 2. Connect and make sure the eoi table exists ---------- */
$db_error = "";
// @ hides the raw PHP warning; the friendly message below is shown instead.
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) {
    $db_error = "Unable to connect to the database. Check settings.php and make sure MySQL is running "
              . "and the database \"" . $sql_db . "\" exists.";
} else {
    mysqli_set_charset($conn, "utf8mb4");

    // Same structure as process_eoi.php, so manage.php also works before the first application.
    $create_table_sql = "CREATE TABLE IF NOT EXISTS eoi (
        EOInumber INT AUTO_INCREMENT PRIMARY KEY,
        JobReferenceNumber VARCHAR(10) NOT NULL,
        FirstName VARCHAR(20) NOT NULL,
        LastName VARCHAR(20) NOT NULL,
        DateOfBirth VARCHAR(10) NOT NULL,
        Gender VARCHAR(20) NOT NULL,
        StreetAddress VARCHAR(40) NOT NULL,
        SuburbTown VARCHAR(40) NOT NULL,
        State VARCHAR(10) NOT NULL,
        Postcode VARCHAR(4) NOT NULL,
        Email VARCHAR(100) NOT NULL,
        Phone VARCHAR(15) NOT NULL,
        Skill1 VARCHAR(50),
        Skill2 VARCHAR(50),
        Skill3 VARCHAR(50),
        OtherSkills TEXT,
        Status ENUM('New', 'Current', 'Final') DEFAULT 'New'
    )";
    if (!mysqli_query($conn, $create_table_sql)) {
        $db_error = "Could not prepare the eoi table: " . mysqli_error($conn);
    }
}

/* ---------- 3. Handle POST actions (delete, change status) ---------- */
if ($conn && $db_error === "" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $action = read_text($_POST, "action", 20);
    $result_params = ["msg" => "invalid_action"];

    // Filters to restore after the redirect.
    $keep = [];
    if (isset($_POST["keep"]) && is_array($_POST["keep"])) {
        foreach ($filter_keys as $key) {
            $keep[$key] = read_text($_POST["keep"], $key, 20);
        }
    }

    if ($action === "update_status") {
        $eoi_number = filter_var(read_text($_POST, "eoi_number", 11), FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        $new_status = read_text($_POST, "new_status", 10);

        if ($eoi_number === false || !in_array($new_status, $valid_statuses, true)) {
            $result_params = ["msg" => "invalid_status"];
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE eoi SET Status = ? WHERE EOInumber = ?");
            mysqli_stmt_bind_param($stmt, "si", $new_status, $eoi_number);
            if (mysqli_stmt_execute($stmt)) {
                $result_params = ["msg" => "status_updated", "eoi" => $eoi_number, "to" => $new_status];
            } else {
                $result_params = ["msg" => "db_error"];
            }
            mysqli_stmt_close($stmt);
        }
    } elseif ($action === "delete_job") {
        $delete_ref = strtoupper(read_text($_POST, "delete_job_ref", 10));
        $confirmed  = read_text($_POST, "confirm_delete", 3) === "yes";

        if (!preg_match("/^[A-Z0-9]{5}$/", $delete_ref)) {
            $result_params = ["msg" => "invalid_ref"];
        } elseif (!$confirmed) {
            $result_params = ["msg" => "confirm_required", "ref" => $delete_ref];
        } else {
            $stmt = mysqli_prepare($conn, "DELETE FROM eoi WHERE JobReferenceNumber = ?");
            mysqli_stmt_bind_param($stmt, "s", $delete_ref);
            if (mysqli_stmt_execute($stmt)) {
                $result_params = ["msg" => "deleted", "ref" => $delete_ref, "n" => mysqli_stmt_affected_rows($stmt)];
            } else {
                $result_params = ["msg" => "db_error"];
            }
            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($conn);
    // Post/Redirect/Get: refreshing the page will not repeat the action.
    header("Location: manage.php?" . filters_to_query($keep, $result_params));
    exit();
}

/* ---------- 4. Message shown after a redirect ---------- */
$notice = null;
$msg = read_text($_GET, "msg", 20);
if ($msg !== "") {
    $msg_ref = strtoupper(read_text($_GET, "ref", 10));
    switch ($msg) {
        case "status_updated":
            $notice = ["success", "EOI #" . (int)read_text($_GET, "eoi", 11) . " is now marked as "
                . (in_array(read_text($_GET, "to", 10), $valid_statuses, true) ? read_text($_GET, "to", 10) : "updated") . "."];
            break;
        case "deleted":
            $count = (int)read_text($_GET, "n", 11);
            $notice = $count > 0
                ? ["success", "Deleted " . $count . " EOI" . ($count === 1 ? "" : "s") . " for job reference " . $msg_ref . "."]
                : ["info", "No EOIs found for job reference " . $msg_ref . ". Nothing was deleted."];
            break;
        case "confirm_required":
            $notice = ["error", "Tick the confirmation box before deleting EOIs for " . $msg_ref . "."];
            break;
        case "invalid_ref":
            $notice = ["error", "Enter a job reference of exactly 5 letters or numbers, for example FE101."];
            break;
        case "invalid_status":
            $notice = ["error", "The status change was not valid. Choose New, Current or Final."];
            break;
        case "db_error":
            $notice = ["error", "The database could not complete the request. Please try again."];
            break;
        default:
            $notice = ["error", "Unknown action."];
    }
}

/* ---------- 5. Read data for the page ---------- */
$stats    = ["Total" => 0, "New" => 0, "Current" => 0, "Final" => 0];
$job_refs = array_keys($job_titles);
$eois     = [];

if ($conn && $db_error === "") {
    // Overview cards
    $result = mysqli_query($conn, "SELECT Status, COUNT(*) AS total FROM eoi GROUP BY Status");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $stats["Total"] += (int)$row["total"];
            if (isset($stats[$row["Status"]])) {
                $stats[$row["Status"]] = (int)$row["total"];
            }
        }
        mysqli_free_result($result);
    }

    // Job references that already exist in the table (for the dropdowns)
    $result = mysqli_query($conn, "SELECT DISTINCT JobReferenceNumber FROM eoi ORDER BY JobReferenceNumber");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $ref = strtoupper($row["JobReferenceNumber"]);
            if (!in_array($ref, $job_refs, true)) {
                $job_refs[] = $ref;
            }
        }
        mysqli_free_result($result);
    }

    // Main list with optional filters (prepared statement)
    $conditions = [];
    $types = "";
    $params = [];

    if ($filters["job_ref"] !== "") {
        $conditions[] = "JobReferenceNumber = ?";
        $types .= "s";
        $params[] = $filters["job_ref"];
    }
    if ($filters["first_name"] !== "") {
        $conditions[] = "FirstName LIKE ?";
        $types .= "s";
        $params[] = "%" . addcslashes($filters["first_name"], "%_\\") . "%";
    }
    if ($filters["last_name"] !== "") {
        $conditions[] = "LastName LIKE ?";
        $types .= "s";
        $params[] = "%" . addcslashes($filters["last_name"], "%_\\") . "%";
    }
    if ($filters["status"] !== "") {
        $conditions[] = "Status = ?";
        $types .= "s";
        $params[] = $filters["status"];
    }

    $sql = "SELECT * FROM eoi";
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    // Column names come from a whitelist, never directly from the user.
    $sql .= " ORDER BY " . $sort_columns[$filters["sort"]] . " " . strtoupper($filters["order"]);
    if ($filters["sort"] !== "eoi") {
        $sql .= ", EOInumber ASC";
    }

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $eois[] = $row;
        }
        mysqli_stmt_close($stmt);
    } else {
        $db_error = "Could not read applications: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}

$is_filtered = $filters["job_ref"] !== "" || $filters["first_name"] !== ""
            || $filters["last_name"] !== "" || $filters["status"] !== "";

/* ---------- 6. Output ---------- */
$page_title = "Lumina Learn | Application Management";
$body_class = "manage-body";
$main_id    = "manage-main";
$current_page = "manage";   // no menu item is highlighted on this page
$page_style = "";
include_once("header.inc");
include_once("nav.inc");
?>

<main class="manage-page" id="manage-main" tabindex="-1">

    <section class="manage-hero">
        <div>
            <p class="manage-eyebrow">LUMINA LEARN / HR PORTAL · SIGNED IN AS <?php echo e(strtoupper($_SESSION["manager"])); ?> · <a href="logout.php">LOG OUT</a></p>
            <h1>Application Management</h1>
            <p>
                Manage and review candidate applications
                submitted to Lumina Learn.
            </p>
        </div>
    </section>

    <section class="manage-content">

        <?php if ($notice !== null): ?>
            <p class="manage-alert manage-alert-<?php echo e($notice[0]); ?>" role="status">
                <?php echo e($notice[1]); ?>
            </p>
        <?php endif; ?>

        <?php if ($db_error !== ""): ?>
            <section class="manage-panel">
                <h2>Database not ready</h2>
                <p class="manage-alert manage-alert-error"><?php echo e($db_error); ?></p>
            </section>
        <?php else: ?>

        <!-- Dashboard overview -->
        <div class="manage-heading">
            <div>
                <h2>Overview</h2>
                <p>Track your recruitment applications.</p>
            </div>
        </div>

        <div class="manage-stats">
            <article class="stat-card">
                <p>Total Applications</p>
                <h3><?php echo $stats["Total"]; ?></h3>
                <span>All submitted applications</span>
            </article>
            <article class="stat-card">
                <p>New Applications</p>
                <h3><?php echo $stats["New"]; ?></h3>
                <span>Waiting for review</span>
            </article>
            <article class="stat-card">
                <p>Current Applications</p>
                <h3><?php echo $stats["Current"]; ?></h3>
                <span>Currently being reviewed</span>
            </article>
            <article class="stat-card">
                <p>Final Applications</p>
                <h3><?php echo $stats["Final"]; ?></h3>
                <span>Review completed</span>
            </article>
        </div>

        <!-- Search applications -->
        <section class="manage-panel" aria-labelledby="applications-title">

            <div class="manage-heading">
                <div>
                    <h2 id="applications-title">Applications</h2>
                    <p>Leave every field empty and press Search to list all EOIs.</p>
                </div>
            </div>

            <form class="manage-filter" action="manage.php" method="get">
                <div class="manage-filter-field">
                    <label for="filter-job">Job reference</label>
                    <select id="filter-job" name="job_ref">
                        <option value="">All positions</option>
                        <?php foreach ($job_refs as $ref): ?>
                            <option value="<?php echo e($ref); ?>" <?php echo $filters["job_ref"] === $ref ? "selected" : ""; ?>>
                                <?php echo e($ref . (isset($job_titles[$ref]) ? " · " . $job_titles[$ref] : "")); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="manage-filter-field">
                    <label for="filter-first">First name</label>
                    <input type="text" id="filter-first" name="first_name" maxlength="20"
                           value="<?php echo e($filters["first_name"]); ?>" placeholder="e.g. Anna">
                </div>

                <div class="manage-filter-field">
                    <label for="filter-last">Last name</label>
                    <input type="text" id="filter-last" name="last_name" maxlength="20"
                           value="<?php echo e($filters["last_name"]); ?>" placeholder="e.g. Nguyen">
                </div>

                <div class="manage-filter-field">
                    <label for="filter-status">Status</label>
                    <select id="filter-status" name="status">
                        <option value="">Any status</option>
                        <?php foreach ($valid_statuses as $status): ?>
                            <option value="<?php echo $status; ?>" <?php echo $filters["status"] === $status ? "selected" : ""; ?>><?php echo $status; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="manage-filter-field">
                    <label for="filter-sort">Sort by</label>
                    <select id="filter-sort" name="sort">
                        <?php foreach ($sort_labels as $key => $label): ?>
                            <option value="<?php echo $key; ?>" <?php echo $filters["sort"] === $key ? "selected" : ""; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="manage-filter-field">
                    <label for="filter-order">Order</label>
                    <select id="filter-order" name="order">
                        <option value="desc" <?php echo $filters["order"] === "desc" ? "selected" : ""; ?>>Descending</option>
                        <option value="asc" <?php echo $filters["order"] === "asc" ? "selected" : ""; ?>>Ascending</option>
                    </select>
                </div>

                <div class="manage-filter-actions">
                    <button type="submit">Search</button>
                    <a class="manage-link-button" href="manage.php">List all EOIs</a>
                </div>
            </form>

            <p class="manage-result-count">
                Showing <strong><?php echo count($eois); ?></strong>
                <?php echo count($eois) === 1 ? "application" : "applications"; ?>
                <?php echo $is_filtered ? "matching your search" : "in total"; ?>.
            </p>

            <!-- Application table -->
            <div class="manage-table-wrapper">
                <table class="manage-table">
                    <thead>
                        <tr>
                            <th scope="col">EOI Number</th>
                            <th scope="col">Job Reference</th>
                            <th scope="col">Applicant Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Job Title</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($eois)): ?>
                        <tr>
                            <td colspan="7" class="empty-state">
                                <strong>No applications to display</strong>
                                <p>
                                    <?php echo $is_filtered
                                        ? "No EOIs match your search. Try another name or job reference."
                                        : "Submitted applications will appear here after a candidate applies through apply.php."; ?>
                                </p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($eois as $row):
                            $id    = (int)$row["EOInumber"];
                            $ref   = strtoupper($row["JobReferenceNumber"]);
                            $skills = array_filter([$row["Skill1"], $row["Skill2"], $row["Skill3"]], function ($skill) {
                                return $skill !== null && $skill !== "";
                            });
                        ?>
                        <tr>
                            <td>#<?php echo $id; ?></td>
                            <td class="manage-ref"><?php echo e($ref); ?></td>
                            <td>
                                <details class="manage-details">
                                    <summary><?php echo e($row["FirstName"] . " " . $row["LastName"]); ?></summary>
                                    <dl>
                                        <dt>Date of birth</dt><dd><?php echo e($row["DateOfBirth"]); ?></dd>
                                        <dt>Gender</dt><dd><?php echo e($row["Gender"]); ?></dd>
                                        <dt>Phone</dt><dd><?php echo e($row["Phone"]); ?></dd>
                                        <dt>Address</dt>
                                        <dd><?php echo e($row["StreetAddress"] . ", " . $row["SuburbTown"] . " " . $row["State"] . " " . $row["Postcode"]); ?></dd>
                                        <dt>Core skills</dt><dd><?php echo empty($skills) ? "None selected" : e(implode(", ", $skills)); ?></dd>
                                        <dt>Other skills</dt><dd><?php echo $row["OtherSkills"] === "" || $row["OtherSkills"] === null ? "Not provided" : nl2br(e($row["OtherSkills"])); ?></dd>
                                    </dl>
                                </details>
                            </td>
                            <td><a href="mailto:<?php echo e($row["Email"]); ?>"><?php echo e($row["Email"]); ?></a></td>
                            <td><?php echo e(isset($job_titles[$ref]) ? $job_titles[$ref] : "Unknown role"); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo e(strtolower($row["Status"])); ?>">
                                    <?php echo e($row["Status"]); ?>
                                </span>
                            </td>
                            <td>
                                <form class="manage-status-form" action="manage.php" method="post">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="eoi_number" value="<?php echo $id; ?>">
                                    <?php echo keep_filter_inputs($filters); ?>
                                    <label class="manage-visually-hidden" for="status-<?php echo $id; ?>">New status for EOI #<?php echo $id; ?></label>
                                    <select id="status-<?php echo $id; ?>" name="new_status">
                                        <?php foreach ($valid_statuses as $status): ?>
                                            <option value="<?php echo $status; ?>" <?php echo $row["Status"] === $status ? "selected" : ""; ?>><?php echo $status; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit">Update</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Delete EOIs by job reference -->
        <section class="manage-panel manage-danger-panel" aria-labelledby="delete-title">
            <div class="manage-heading">
                <div>
                    <h2 id="delete-title">Delete EOIs by job reference</h2>
                    <p>This permanently removes every application for the chosen position.</p>
                </div>
            </div>

            <form class="manage-delete-form" action="manage.php" method="post">
                <input type="hidden" name="action" value="delete_job">
                <?php echo keep_filter_inputs($filters); ?>

                <div class="manage-filter-field">
                    <label for="delete-ref">Job reference</label>
                    <select id="delete-ref" name="delete_job_ref" required>
                        <option value="" selected disabled>Choose a job reference</option>
                        <?php foreach ($job_refs as $ref): ?>
                            <option value="<?php echo e($ref); ?>"><?php echo e($ref . (isset($job_titles[$ref]) ? " · " . $job_titles[$ref] : "")); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <label class="manage-confirm" for="confirm-delete">
                    <input type="checkbox" id="confirm-delete" name="confirm_delete" value="yes">
                    <span>I understand these EOIs cannot be recovered.</span>
                </label>

                <button class="manage-danger-button" type="submit">Delete EOIs</button>
            </form>
        </section>

        <?php endif; ?>

    </section>

</main>

<?php
include_once("footer.inc");
?>
