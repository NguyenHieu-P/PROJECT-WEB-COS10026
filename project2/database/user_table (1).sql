-- ----------------------------------------------------------
-- user_table.sql
-- Tạo bảng `user` để đăng nhập vào trang manage.php
-- Cách dùng: mở phpMyAdmin, chọn database lumina_db, tab "Import"
--            (hoặc tab "SQL", dán toàn bộ nội dung file này rồi bấm Go).
-- ----------------------------------------------------------

-- 1. Tạo bảng (nếu bảng đã có thì bỏ qua, không báo lỗi)
CREATE TABLE IF NOT EXISTS `user` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Thêm tài khoản mặc định cho người chấm: admin / admin
--    Mật khẩu KHÔNG lưu dạng chữ thường mà lưu dạng đã mã hoá (hash)
--    bằng hàm password_hash("admin", PASSWORD_DEFAULT) của PHP.
--    INSERT IGNORE: nếu admin đã tồn tại thì bỏ qua, không báo lỗi.
INSERT IGNORE INTO `user` (`username`, `password`) VALUES
('admin', '$2y$10$JLCo5scEp28EChnavRXJaOYtdspE9RvttXnxwcY5C8yr7tEEwp/cW');
