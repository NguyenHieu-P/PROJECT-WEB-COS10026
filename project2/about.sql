SET NAMES utf8mb4;
CREATE DATABASE IF NOT EXISTS lumina_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lumina_db;

CREATE TABLE IF NOT EXISTS about (
    student_id CHAR(9) NOT NULL PRIMARY KEY,
    part1 TEXT NOT NULL,
    part2 TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO about (student_id, part1, part2) VALUES
('106208949', 'Develops index.html, coordinates the shared styles.css and coordinates masterplan, design UI layout.', NULL),
('105027509', 'Develops jobs.html, coordinates the web’s html structure, sets up and manages the Git repository and GitHub Pages.', NULL),
('105709742', 'Develops apply.html, lays out the application form using CSS Grid and checks its HTML validation patterns.', NULL),
('105714199', 'Developed the About page and its CSS, prepared the group photo and checked the page with the HTML validator.', 'Converted About to PHP with shared includes and loaded member contributions from MySQL.')
ON DUPLICATE KEY UPDATE student_id = VALUES(student_id);
