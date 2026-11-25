<?php
/*
 * manage.php - HR Management Portal
 * ----------------------------------------------------------
 * Chỉ HR Manager đã đăng nhập mới vào được trang này.
 * Các chức năng:
 *   A. Xem tất cả EOI, hoặc lọc theo Job reference
 *   B. Tìm ứng viên theo First name, Last name, hoặc cả 2
 *   C. Sắp xếp kết quả theo cột người dùng chọn
 *   D. Cập nhật Status của 1 EOI (New, Current, Final)
 *   E. Xoá tất cả EOI của 1 Job reference
 *
 * Quy ước: form XEM/TÌM/SẮP XẾP gửi bằng GET (chỉ đọc dữ liệu),
 *          form SỬA/XOÁ gửi bằng POST (thay đổi dữ liệu).
 */
 
// ==========================================================
// BƯỚC 1. KIỂM TRA ĐĂNG NHẬP
// ==========================================================
session_start();
if (!isset($_SESSION["username"])) {
    // Chưa đăng nhập thì đẩy về trang login
    header("Location: login.php");
    exit();
}
 
// ==========================================================
// BƯỚC 2. KẾT NỐI DATABASE
// ==========================================================
require_once("settings.php");
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);
if (!$conn) {
    die("<p>Unable to connect to the database. Please check settings.php.</p>");
}
 
// ==========================================================
// BƯỚC 3. CÁC "DANH SÁCH TRẮNG" (whitelist)
// Chỉ chấp nhận giá trị nằm trong các danh sách này.
// ==========================================================
 
// 3a. Các giá trị Status hợp lệ
$valid_statuses = ["New", "Current", "Final"];
 
// 3b. Các cột được phép sắp xếp: "tên cột trong DB" => "chữ hiện trên màn hình"
//     Tên cột KHÔNG BAO GIỜ lấy trực tiếp từ người dùng ghép vào SQL,
//     mà phải kiểm tra có nằm trong danh sách này hay không.
$sort_columns = [
    "EOInumber"          => "EOI number",
    "JobReferenceNumber" => "Job reference",
    "FirstName"          => "First name",
    "LastName"           => "Last name",
    "State"              => "State",
    "Status"             => "Status"
];
 
// Hàm nhỏ giúp in dữ liệu ra HTML an toàn (chống chèn mã HTML/JS)
function e($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, "UTF-8");
}
 
$message = "";   // thông báo kết quả sau khi sửa/xoá
 
// ==========================================================
// BƯỚC 4. XỬ LÝ CÁC THAO TÁC THAY ĐỔI DỮ LIỆU (POST)
// Mỗi form POST có 1 ô ẩn tên "action" để biết người dùng muốn làm gì.
// ==========================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = isset($_POST["action"]) ? $_POST["action"] : "";
 
    // ---- D. Cập nhật Status của 1 EOI ----
    if ($action === "update_status") {
        $eoi_number = isset($_POST["eoi_number"]) ? (int)$_POST["eoi_number"] : 0;
        $new_status = isset($_POST["status"]) ? $_POST["status"] : "";
 
        if ($eoi_number > 0 && in_array($new_status, $valid_statuses)) {
            $stmt = mysqli_prepare($conn, "UPDATE eoi SET Status = ? WHERE EOInumber = ?");
            mysqli_stmt_bind_param($stmt, "si", $new_status, $eoi_number);   // s = chuỗi, i = số nguyên
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $message = "EOI #" . $eoi_number . " status updated to " . $new_status . ".";
        } else {
            $message = "Invalid EOI number or status.";
        }
    }
 
    // ---- E. Xoá tất cả EOI của 1 Job reference ----
    if ($action === "delete_job") {
        $delete_ref = isset($_POST["delete_ref"]) ? trim($_POST["delete_ref"]) : "";
 
        if ($delete_ref === "") {
            $message = "Please choose a job reference to delete.";
        } else {
            $stmt = mysqli_prepare($conn, "DELETE FROM eoi WHERE JobReferenceNumber = ?");
            mysqli_stmt_bind_param($stmt, "s", $delete_ref);
            mysqli_stmt_execute($stmt);
            $deleted = mysqli_stmt_affected_rows($stmt);   // số dòng đã bị xoá
            mysqli_stmt_close($stmt);
            $message = $deleted . " EOI(s) with job reference " . $delete_ref . " deleted.";
        }
    }
}
 
// ==========================================================
// BƯỚC 5. ĐỌC CÁC LỰA CHỌN LỌC / TÌM / SẮP XẾP (GET)
// ==========================================================
$job_ref    = isset($_GET["job_ref"])    ? trim($_GET["job_ref"])    : "";
$first_name = isset($_GET["first_name"]) ? trim($_GET["first_name"]) : "";
$last_name  = isset($_GET["last_name"])  ? trim($_GET["last_name"])  : "";
$sort       = isset($_GET["sort"])       ? $_GET["sort"]             : "EOInumber";
$order      = isset($_GET["order"])      ? $_GET["order"]            : "ASC";
 
// Kiểm tra cột sắp xếp và chiều sắp xếp theo danh sách trắng.
// array_key_exists: $sort có phải là 1 "khoá" trong $sort_columns không?
if (!array_key_exists($sort, $sort_columns)) {
    $sort = "EOInumber";
}
if ($order !== "ASC" && $order !== "DESC") {
    $order = "ASC";
}
 
// ==========================================================
// BƯỚC 6. TRUY VẤN DANH SÁCH EOI
// Mẹo: 1 câu SQL cố định dùng cho mọi trường hợp.
//   (? = '' OR JobReferenceNumber = ?)
//   nghĩa là: nếu ô Job reference để trống thì điều kiện luôn đúng
//   (không lọc), còn nếu có giá trị thì chỉ lấy dòng có job ref đó.
// Tìm tên dùng LIKE '%chữ%' để gõ 1 phần tên vẫn tìm được.
// ==========================================================
$first_like = "%" . $first_name . "%";
$last_like  = "%" . $last_name . "%";
 
// $sort và $order đã qua danh sách trắng ở Bước 5 nên ghép vào SQL được an toàn.
$sql = "SELECT * FROM eoi
        WHERE (? = '' OR JobReferenceNumber = ?)
          AND (? = '' OR FirstName LIKE ?)
          AND (? = '' OR LastName LIKE ?)
        ORDER BY " . $sort . " " . $order;
 
$stmt = mysqli_prepare($conn, $sql);
// 6 dấu ? nên cần 6 chữ "s" và 6 biến, đúng thứ tự
mysqli_stmt_bind_param($stmt, "ssssss",
    $job_ref, $job_ref,
    $first_name, $first_like,
    $last_name, $last_like
);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$eois = mysqli_fetch_all($result, MYSQLI_ASSOC);   // lấy tất cả dòng thành 1 mảng
mysqli_stmt_close($stmt);
 
// Lấy danh sách các Job reference đang có trong bảng (cho 2 ô chọn)
$job_refs = [];
$ref_result = mysqli_query($conn, "SELECT DISTINCT JobReferenceNumber FROM eoi ORDER BY JobReferenceNumber");
while ($row = mysqli_fetch_assoc($ref_result)) {
    $job_refs[] = $row["JobReferenceNumber"];
}
 
mysqli_close($conn);
 
// ==========================================================
// BƯỚC 7. HIỂN THỊ TRANG
// ==========================================================
$pageTitle   = "Lumina Learn | HR Management";
$bodyClass   = "apply-page";   // mượn style form của trang Apply
$currentPage = "";
include "header.inc";
include "nav.inc";
?>
<!-- Nạp CSS riêng của trang manage (file manage.css nằm cùng thư mục) -->
<link rel="stylesheet" href="styles.css">
 
<?php
?>

<!-- ===== NỘI DUNG CHÍNH ===== -->
 
<main id="manage-main" style="max-width: 1200px; margin: 40px auto; padding: 0 50px;">
 
    <!-- Tiêu đề + nút đăng xuất -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 12px;">
        <h1 style="color: var(--purple); margin: 0;">HR Management Portal</h1>
        <p>
            Logged in as <strong><?php echo e($_SESSION["username"]); ?></strong>
        </p>
    </div>
 
    <!-- Thông báo sau khi sửa/xoá -->
    <?php if ($message !== ""): ?>
        <p style="margin: 20px 0; padding: 12px 16px; border-radius: 9px; background: #d1fae5; font-weight: 700;">
            <?php echo e($message); ?>
        </p>
    <?php endif; ?>
 
    <!-- ===== FORM A + B + C: LỌC, TÌM, SẮP XẾP (GET) ===== -->
    <form method="get" action="manage.php" class="apply-fieldset" style="margin-top: 24px;">
        <fieldset style="border: 0; margin: 0; padding: 0;">
            <legend style="font-weight: 700; color: var(--purple-dark); font-size: 1.125rem;">Search EOIs</legend>
            <div class="apply-form-grid" style="margin-top: 12px;">
                <div class="apply-field">
                    <label for="job_ref">Job reference</label>
                    <select id="job_ref" name="job_ref">
                        <option value="">All job references</option>
                        <?php foreach ($job_refs as $ref): ?>
                            <option value="<?php echo e($ref); ?>" <?php if ($ref === $job_ref) echo "selected"; ?>>
                                <?php echo e($ref); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="apply-field">
                    <label for="first_name">First name</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo e($first_name); ?>">
                </div>
                <div class="apply-field">
                    <label for="last_name">Last name</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo e($last_name); ?>">
                </div>
                <div class="apply-field">
                    <label for="sort">Sort by</label>
                    <select id="sort" name="sort">
                        <?php foreach ($sort_columns as $column => $label): ?>
                            <option value="<?php echo $column; ?>" <?php if ($column === $sort) echo "selected"; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="apply-field">
                    <label for="order">Order</label>
                    <select id="order" name="order">
                        <option value="ASC" <?php if ($order === "ASC") echo "selected"; ?>>Ascending (A to Z, 1 to 9)</option>
                        <option value="DESC" <?php if ($order === "DESC") echo "selected"; ?>>Descending (Z to A, 9 to 1)</option>
                    </select>
                </div>
            </div>
            <p style="margin-top: 18px;">
                <button type="submit" class="button button-primary">Search</button>
                <!-- Link về manage.php không kèm tham số = xem tất cả EOI -->
                <a class="button button-secondary" href="manage.php">Show all EOIs</a>
            </p>
        </fieldset>
    </form>
 
    <!-- ===== KẾT QUẢ + FORM D: CẬP NHẬT STATUS ===== -->
    <h2 style="color: var(--purple);"><?php echo count($eois); ?> EOI(s) found</h2>
 
    <?php if (count($eois) === 0): ?>
        <p>No EOIs match your search.</p>
    <?php else: ?>
        <!-- style nowrap: giữ các ô ngắn (mã, ngày, số điện thoại) trên 1 dòng -->
        <div class="table-wrap" style="max-width: none;">
            <table class="career-table">
                <thead>
                    <tr>
                        <th style="white-space: nowrap;">EOI #</th>
                        <th style="white-space: nowrap;">Job ref</th>
                        <th>First name</th>
                        <th>Last name</th>
                        <th>Date of birth</th>
                        <th>Gender</th>
                        <th>Address</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Skills</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($eois as $eoi): ?>
                        <tr>
                            <td style="white-space: nowrap;"><?php echo e($eoi["EOInumber"]); ?></td>
                            <td style="white-space: nowrap;"><?php echo e($eoi["JobReferenceNumber"]); ?></td>
                            <td><?php echo e($eoi["FirstName"]); ?></td>
                            <td><?php echo e($eoi["LastName"]); ?></td>
                            <td style="white-space: nowrap;"><?php echo e($eoi["DateOfBirth"]); ?></td>
                            <td style="white-space: nowrap;"><?php echo e($eoi["Gender"]); ?></td>
                            <td>
                                <?php echo e($eoi["StreetAddress"]); ?>,
                                <?php echo e($eoi["SuburbTown"]); ?>
                                <?php echo e($eoi["State"]); ?>
                                <?php echo e($eoi["Postcode"]); ?>
                            </td>
                            <td><?php echo e($eoi["Email"]); ?></td>
                            <td style="white-space: nowrap;"><?php echo e($eoi["Phone"]); ?></td>
                            <td>
                                <?php
                                // Gộp các kỹ năng không rỗng, cách nhau bởi dấu phẩy
                                $skills = array_filter([$eoi["Skill1"], $eoi["Skill2"], $eoi["Skill3"], $eoi["OtherSkills"]]);
                                echo e(implode(", ", $skills));
                                ?>
                            </td>
                            <td>
                                <!-- Mỗi dòng có 1 form nhỏ để đổi Status.
                                     Không ghi action: form gửi về đúng địa chỉ hiện tại
                                     (giữ nguyên bộ lọc đang dùng). -->
                                <form method="post">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="eoi_number" value="<?php echo e($eoi["EOInumber"]); ?>">
                                    <label for="status-<?php echo e($eoi["EOInumber"]); ?>" style="display: none;">Status</label>
                                    <select id="status-<?php echo e($eoi["EOInumber"]); ?>" name="status">
                                        <?php foreach ($valid_statuses as $status): ?>
                                            <option value="<?php echo $status; ?>" <?php if ($status === $eoi["Status"]) echo "selected"; ?>>
                                                <?php echo $status; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
 
    <!-- ===== FORM E: XOÁ TẤT CẢ EOI THEO JOB REFERENCE (POST) ===== -->
    <form method="post" action="manage.php" class="apply-fieldset" style="margin-top: 32px;">
        <fieldset style="border: 0; margin: 0; padding: 0;">
            <legend style="font-weight: 700; color: #b91c1c; font-size: 1.125rem;">Delete all EOIs for a job reference</legend>
            <input type="hidden" name="action" value="delete_job">
            <div class="apply-field" style="max-width: 320px; margin-top: 12px;">
                <label for="delete_ref">Job reference</label>
                <select id="delete_ref" name="delete_ref" required>
                    <option value="">Choose a job reference</option>
                    <?php foreach ($job_refs as $ref): ?>
                        <option value="<?php echo e($ref); ?>"><?php echo e($ref); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Ô xác nhận bắt buộc tick, tránh bấm nhầm -->
            <p style="margin-top: 12px;">
                <input type="checkbox" id="confirm_delete" name="confirm_delete" required>
                <label for="confirm_delete">I understand this cannot be undone.</label>
            </p>
            <button type="submit" class="button button-primary" style="background: #b91c1c;">Delete EOIs</button>
        </fieldset>
    </form>
 
</main>
 
<?php include "footer.inc"; ?>
 