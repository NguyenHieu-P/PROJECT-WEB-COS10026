<?php
/*
 * logout.php - Đăng xuất
 * ----------------------------------------------------------
 * Xoá toàn bộ dữ liệu session rồi quay về trang đăng nhập.
 */
session_start();      // phải mở session thì mới xoá được
$_SESSION = [];       // xoá mọi biến trong session
session_destroy();    // huỷ session trên server
header("Location: login.php");
exit();
