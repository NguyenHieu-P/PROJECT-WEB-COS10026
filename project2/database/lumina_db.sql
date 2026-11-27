-- Lumina Learn - database and eoi table
-- Same structure as process_eoi.php and manage.php use.
-- Run in phpMyAdmin: SQL tab -> paste -> Go (or Import this file).

CREATE DATABASE IF NOT EXISTS lumina_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE lumina_db;

-- Uncomment the next line ONLY if you want to delete the old table and all its data.
-- DROP TABLE IF EXISTS eoi;

CREATE TABLE IF NOT EXISTS eoi (
  EOInumber          INT AUTO_INCREMENT PRIMARY KEY,
  JobReferenceNumber VARCHAR(10)  NOT NULL,
  FirstName          VARCHAR(20)  NOT NULL,
  LastName           VARCHAR(20)  NOT NULL,
  DateOfBirth        VARCHAR(10)  NOT NULL,   -- stored as dd/mm/yyyy
  Gender             VARCHAR(20)  NOT NULL,
  StreetAddress      VARCHAR(40)  NOT NULL,
  SuburbTown         VARCHAR(40)  NOT NULL,
  State              VARCHAR(10)  NOT NULL,
  Postcode           VARCHAR(4)   NOT NULL,
  Email              VARCHAR(100) NOT NULL,
  Phone              VARCHAR(15)  NOT NULL,
  Skill1             VARCHAR(50),
  Skill2             VARCHAR(50),
  Skill3             VARCHAR(50),
  OtherSkills        TEXT,
  Status             ENUM('New', 'Current', 'Final') DEFAULT 'New'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Optional: 2 fictional test rows
INSERT INTO eoi (JobReferenceNumber, FirstName, LastName, DateOfBirth, Gender,
                 StreetAddress, SuburbTown, State, Postcode, Email, Phone,
                 Skill1, Skill2, Skill3, OtherSkills, Status)
VALUES
  ('FE101', 'Anna', 'Nguyen', '21/08/2005', 'Female',
   '12 Main St', 'Hawthorn', 'VIC', '3122', 'anna@example.com', '0412345678',
   'Communication and teamwork', 'Problem solving', '', 'HTML, CSS, JavaScript', 'New'),
  ('UX102', 'Minh', 'Tran', '01/02/2000', 'Male',
   '5 King St', 'Sydney', 'NSW', '2000', 'minh@example.com', '0498765432',
   'Problem solving', 'Accessible and user-centred design', '', 'Figma', 'Current');