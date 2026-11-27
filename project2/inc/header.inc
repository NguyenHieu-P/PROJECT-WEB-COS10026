<?php
/*
 * header.inc
 * ----------------------------------------------------------
 * Phần đầu của MỌI trang: từ <!DOCTYPE html> đến thẻ mở <body>.
 *
 * Trang nào muốn dùng file này thì khai báo 2 biến TRƯỚC khi include:
 *   $pageTitle  : tiêu đề hiện trên tab trình duyệt
 *   $bodyClass  : class CSS gắn vào thẻ <body> (ví dụ "home-page")
 *
 * Nếu trang quên khai báo, ta dùng giá trị mặc định bên dưới
 * để trang không bị lỗi.
 */

// isset() kiểm tra biến đã có giá trị chưa. Chưa có thì gán mặc định.
if (!isset($pageTitle)) {
    $pageTitle = "Lumina Learn";
}
if (!isset($bodyClass)) {
    $bodyClass = "";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#3933ad">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Lumina Learn">
    <?php // echo: in giá trị của biến PHP ra HTML ?>
    <meta property="og:title" content="<?php echo $pageTitle; ?>">
    <meta property="og:description" content="Join Lumina Learn and help make digital education accessible to everyone.">
    <title><?php echo $pageTitle; ?></title>
    <link rel="icon" href="images/favicon.ico" sizes="any">
    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
    <link rel="preload" as="image" href="images/hero-classroom.webp">
    <!--LINK TO style.css-->
    <link rel="stylesheet" href="styles.css">
</head>

<body class="<?php echo $bodyClass; ?>">
