-- Lumina Learn - manager accounts for login.php
-- Optional: login.php creates this table and the admin account automatically.
USE lumina_db;

CREATE TABLE IF NOT EXISTS `user` (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50)  NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL   -- stored as a password_hash() value, never plain text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default marker account: admin / admin (the long string is the hash of "admin")
INSERT IGNORE INTO `user` (username, password)
VALUES ('admin', '$2y$10$103wh757ABNFkRIc2OtbK.ihM.UZttAIFhzCCJNXLN6dNk3HSLxji');
