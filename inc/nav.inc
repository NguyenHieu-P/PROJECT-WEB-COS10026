<?php
/*
 * nav.inc
 * ----------------------------------------------------------
 * Thanh điều hướng (logo + menu + nút Apply), dùng chung cho mọi trang.
 *
 * Trang nào include file này thì khai báo trước biến:
 *   $currentPage : tên trang hiện tại, một trong các giá trị
 *                  "home", "jobs", "about", "apply"
 *
 * Menu của trang hiện tại sẽ có class "active" và aria-current="page"
 * để CSS tô sáng mục đó.
 */

if (!isset($currentPage)) {
    $currentPage = "";
}

// Bước 1: mặc định mọi mục menu chỉ có class "nav-link"
$homeLink  = 'class="nav-link"';
$jobsLink  = 'class="nav-link"';
$aboutLink = 'class="nav-link"';

// Bước 2: mục nào trùng với trang hiện tại thì thêm "active"
// và aria-current="page" (giúp trình đọc màn hình biết đang ở trang nào)
if ($currentPage == "home") {
    $homeLink = 'class="nav-link active" aria-current="page"';
}
if ($currentPage == "jobs") {
    $jobsLink = 'class="nav-link active" aria-current="page"';
}
if ($currentPage == "about") {
    $aboutLink = 'class="nav-link active" aria-current="page"';
}
?>
<!-- STICKY NAVIGATION-->
 <!-- LOGO -->
<header class="site-header">
    <a class="brand" href="index.php#home" aria-label="Lumina Learn home">
        <span class="brand-mark">L</span>
        <span class="brand-name">LUMINA<span>LEARN</span></span>
    </a>

    <!-- HOME -->
    <nav class="main-nav" aria-label="Main navigation">
        <div class="nav-dropdown">
            <!-- Bước 3: in các thuộc tính đã chuẩn bị ở trên vào thẻ <a> -->
            <a <?php echo $homeLink; ?> href="index.php#home">Home</a>
            <span class="caret" aria-hidden="true">⌄</span>

            <div class="dropdown-menu" role="group" aria-label="Home sections">
                <a href="index.php#our-impact">Our Impact</a>
                <a href="index.php#career">Career</a>
                <a href="index.php#why-join-us">Why Join Us?</a>
            </div>
        </div>

        <!-- JOBS -->
        <div class="nav-dropdown">
            <a <?php echo $jobsLink; ?> href="jobs.php">Jobs</a>
            <span class="caret" aria-hidden="true">⌄</span>

            <div class="dropdown-menu" role="group" aria-label="Jobs sections">
                <a href="jobs.php#frontend-developer">Web Developer</a>
                <a href="jobs.php#ux-ui-designer">UX/UI Designer</a>
            </div>
        </div>

        <!-- ABOUT US -->
        <div class="nav-dropdown">
            <a <?php echo $aboutLink; ?> href="about.php">About Us</a>
            <span class="caret" aria-hidden="true">⌄</span>

            <div class="dropdown-menu" aria-label="About Us sections">
                <a href="about.php#our-project">Our Project</a>
                <a href="about.php#group-info">Get to Know</a>
                <a href="about.php#team-members">Meet Our Team</a>
                <a href="about.php#site-footer">Contact Us</a>
            </div>
        </div>
    </nav>

    <!-- HEADER ACTIONS -->
    <div class="header-actions">
        <a class="apply-button" href="apply.php">Apply</a>

        <!-- Nút cho HR: chưa đăng nhập thì hiện "Log in",
             đã đăng nhập thì hiện "Manage" và "Log out" -->
        <?php if (isset($_SESSION["username"])): ?>
            <a class="nav-link" href="logout.php">Log out</a>
        <?php else: ?>
            <a class="nav-link" href="login.php">Log in</a>
        <?php endif; ?>
    </div>
</header>
