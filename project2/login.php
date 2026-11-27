<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
/*
 * login.php - Trang đăng nhập cho HR Manager
 * ----------------------------------------------------------
 * Luồng xử lý:
 *   1. Nếu đã đăng nhập rồi thì chuyển thẳng sang manage.php.
 *   2. Nếu người dùng bấm "Log in" (form gửi bằng POST):
 *        - lấy username, password
 *        - tìm username trong bảng `user`
 *        - so sánh mật khẩu gõ vào với mật khẩu lưu trong DB (dạng chữ thường)
 *        - đúng: lưu tên vào $_SESSION rồi chuyển sang manage.php
 *        - sai : hiện thông báo lỗi
 *   3. Hiện form đăng nhập.
 * Tài khoản mặc định cho người chấm: admin / admin
 */

// session_start() phải gọi TRƯỚC mọi HTML. Session giúp "nhớ" người
// dùng đã đăng nhập khi họ chuyển qua lại giữa các trang.
session_start();


// Bước 1: đã đăng nhập thì không cần hiện form nữa
if (isset($_SESSION["username"])) {
    header("Location: manage.php");
    exit();
}

$error = "";      // thông báo lỗi (rỗng = không có lỗi)
$username = "";   // giữ lại username đã gõ để hiện lại trong ô nhập

// Bước 2: xử lý khi form được gửi
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";

    if ($username === "" || $password === "") {
        $error = "Please enter both username and password.";
    } else {
        // Kết nối database bằng thông tin trong settings.php
        require_once("settings.php");
        $conn = @mysqli_connect($host, $user, $pwd, $sql_db);

        if (!$conn) {
            $error = "Unable to connect to the database.";
        } else {
            // 2b. Tìm người dùng theo username (prepared statement chống SQL injection)
            $stmt = mysqli_prepare($conn, "SELECT password FROM `user` WHERE username = ?");
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);   // null nếu không tìm thấy
            mysqli_stmt_close($stmt);
            mysqli_close($conn);

            // 2c. So sánh mật khẩu người dùng gõ với mật khẩu trong DB
            if ($row && $password === $row["password"]) {   // so sánh mật khẩu chữ thường
                session_regenerate_id(true);           // đổi mã session sau khi đăng nhập (an toàn hơn)
                $_SESSION["username"] = $username;     // đánh dấu "đã đăng nhập"
                header("Location: manage.php");
                exit();
            } else {
                $error = "Incorrect username or password.";
            }
        }
    }
}

// Bước 3: hiện trang (dùng lại header, nav, footer chung)
$pageTitle   = "Lumina Learn | HR Login";
$bodyClass   = "apply-page";   // mượn style form của trang Apply
$currentPage = "";
include "inc/header.inc";
include "inc/nav.inc";
?>

<main id="login-main" style="max-width: 480px; margin: 48px auto; padding: 0 16px;">
    <section class="apply-form-panel">
        <div class="apply-form-heading">
            <h2>HR Manager Login</h2>
            <p>Please log in to manage Expressions of Interest (EOIs).</p>
        </div>

        <?php if ($error !== ""): ?>
            <p style="color: #b91c1c; font-weight: 700;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="post" action="login.php">
            <div class="apply-field" style="margin-bottom: 18px;">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required
                       value="<?php echo htmlspecialchars($username); ?>">
            </div>
            <div class="apply-field" style="margin-bottom: 24px;">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="button button-primary">Log in</button>
        </form>
    </section>
</main>

<?php include "inc/footer.inc"; ?>
