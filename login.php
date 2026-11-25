<?php
/*
 * login.php - HR manager login for manage.php.
 * Checks the username and password against the `user` table in lumina_db.
 * Default marker account (created automatically if it does not exist): admin / admin
 */
session_start();
require_once("settings.php");
mysqli_report(MYSQLI_REPORT_OFF);

// Already logged in: go straight to the manage page.
if (isset($_SESSION["manager"])) {
    header("Location: manage.php");
    exit();
}

$error    = "";
$username = "";

$conn = @mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) {
    $error = "Unable to connect to the database. Please try again later.";
} else {
    mysqli_set_charset($conn, "utf8mb4");

    // 1. Create the user table if it does not exist yet.
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `user` (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    )");

    // 2. Add the default account admin / admin if it is missing.
    $check = mysqli_query($conn, "SELECT id FROM `user` WHERE username = 'admin'");
    if ($check && mysqli_num_rows($check) === 0) {
        $hash = password_hash("admin", PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "INSERT INTO `user` (username, password) VALUES ('admin', ?)");
        mysqli_stmt_bind_param($stmt, "s", $hash);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // 3. Check the login form.
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = (isset($_POST["username"]) && is_string($_POST["username"])) ? trim($_POST["username"]) : "";
        $password = (isset($_POST["password"]) && is_string($_POST["password"])) ? $_POST["password"] : "";

        if ($username === "" || $password === "") {
            $error = "Please enter both your username and password.";
        } else {
            $stmt = mysqli_prepare($conn, "SELECT id, password FROM `user` WHERE username = ?");
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            $valid = false;
            if ($row) {
                $stored = $row["password"];
                if (password_get_info($stored)["algoName"] !== "unknown") {
                    // Normal case: the password is stored as a hash.
                    $valid = password_verify($password, $stored);
                } elseif (hash_equals($stored, $password)) {
                    // Password was typed into phpMyAdmin as plain text:
                    // accept it once, then replace it with a hash.
                    $valid = true;
                    $new_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = mysqli_prepare($conn, "UPDATE `user` SET password = ? WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "si", $new_hash, $row["id"]);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }

            if ($valid) {
                session_regenerate_id(true);   // new session id after login
                $_SESSION["manager"] = $username;
                mysqli_close($conn);
                header("Location: manage.php");
                exit();
            }
            $error = "Incorrect username or password.";
        }
    }
    mysqli_close($conn);
}

$notice = "";
$notice_class = "manage-alert";
if (isset($_GET["logged_out"])) {
    $notice = "You have logged out.";
    $notice_class = "manage-alert manage-alert-success";
} elseif (isset($_GET["required"])) {
    $notice = "Please log in to view the application management page.";
}

$page_title   = "Lumina Learn | Manager Login";
$body_class   = "manage-body";
$main_id      = "login-main";
$current_page = "login";
$page_style   = "";
include_once("header.inc");
include_once("nav.inc");
?>

<main class="manage-page" id="login-main" tabindex="-1">

    <section class="manage-hero">
        <div>
            <p class="manage-eyebrow">LUMINA LEARN / HR PORTAL</p>
            <h1>Manager login</h1>
            <p>Sign in to review and manage applications.</p>
        </div>
    </section>

    <section class="manage-content">
        <div class="manage-panel login-panel">

            <?php if ($error !== ""): ?>
                <p class="manage-alert manage-alert-error" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?></p>
            <?php elseif ($notice !== ""): ?>
                <p class="<?php echo $notice_class; ?>" role="status"><?php echo htmlspecialchars($notice, ENT_QUOTES, "UTF-8"); ?></p>
            <?php endif; ?>

            <form class="login-form" action="login.php" method="post">
                <div class="manage-filter-field">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" maxlength="50" autocomplete="username" required
                           value="<?php echo htmlspecialchars($username, ENT_QUOTES, "UTF-8"); ?>">
                </div>
                <div class="manage-filter-field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" autocomplete="current-password" required>
                </div>
                <div class="manage-filter-actions">
                    <button type="submit">Log in</button>
                </div>
            </form>

        </div>
    </section>

</main>

<?php include_once("footer.inc"); ?>
