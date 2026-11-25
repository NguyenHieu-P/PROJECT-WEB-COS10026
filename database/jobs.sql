-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2026 at 03:08 PM
-- Server version: 10.11.19-MariaDB
-- PHP Version: 8.5.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `recruitment_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `job_reference` varchar(10) NOT NULL,
  `job_title` varchar(100) NOT NULL,
  `job_slug` varchar(50) NOT NULL,
  `department` varchar(50) NOT NULL,
  `location` varchar(100) NOT NULL,
  `work_arrangement` varchar(50) NOT NULL,
  `salary` varchar(100) NOT NULL,
  `salary_note` varchar(100) DEFAULT NULL,
  `reports_to` varchar(100) DEFAULT NULL,
  `employment_type` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `responsibilities` text DEFAULT NULL,
  `essential_requirements` text DEFAULT NULL,
  `preferable_requirements` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`job_reference`, `job_title`, `job_slug`, `department`, `location`, `work_arrangement`, `salary`, `salary_note`, `reports_to`, `employment_type`, `description`, `responsibilities`, `essential_requirements`, `preferable_requirements`) VALUES
('FE101', 'Front-End Web Developer', 'frontend-developer', 'Engineering', 'Melbourne, Australia', 'Hybrid · Full-time', 'AUD $80,000–$100,000 / year', 'Plus superannuation', 'Lead Front-End Developer', 'Full-time', 'Build responsive, accessible interfaces for our learning management platform. Help students and educators manage courses, learning resources, assignments and progress through clear, reliable web experiences.', 'Develop and maintain responsive course, assignment and learner dashboard interfaces.\r\nTranslate approved designs into semantic HTML, reusable CSS and well-structured front-end code.\r\nImprove keyboard navigation, screen reader support and the accessibility of forms.\r\nTest interfaces across browsers and screen sizes, and resolve usability issues.\r\nCollaborate with designers, educators and back-end developers through code reviews and Git workflows.', 'Degree or diploma in IT, software development or a related field, or equivalent practical experience.\r\nWorking knowledge of HTML5, CSS3, JavaScript and responsive web design.\r\nUnderstanding of accessible web development, including labels, headings and keyboard interactions.\r\nExperience using Git and a portfolio or project demonstrating front-end development skills.\r\nClear communication, problem-solving skills and an interest in inclusive education.', 'Experience with a component-based framework such as React or Vue.\r\nFamiliarity with learning management systems and educational web applications.\r\nExperience with accessibility testing and front-end performance optimisation.'),
('UX102', 'UX/UI Designer', 'ux-ui-designer', 'Design & UX', 'Melbourne, Australia', 'Hybrid · Full-time', 'AUD $75,000–$95,000 / year', 'Plus superannuation', 'Product Design Lead', 'Full-time', 'Design intuitive learning experiences for students and educators. Turn user research into accessible interfaces that make finding resources, submitting assignments and understanding progress easier for diverse learners.', 'Research learner and educator needs through interviews and usability testing.\r\nCreate user flows, wireframes and interactive prototypes for key learning tasks.\r\nDesign clear navigation, readable layouts and accessible interaction states.\r\nMaintain a consistent design system and document components for developers.\r\nRefine designs using feedback from users, educators and the product team.', 'Qualification in interaction design, digital design or a related field, or equivalent practical experience.\r\nA portfolio explaining your design process, decisions and response to user feedback.\r\nAbility to produce wireframes, prototypes and interface designs using Figma or a similar tool.\r\nUnderstanding of user-centred design, responsive layouts and accessible visual design.\r\nStrong collaboration skills and the ability to communicate design ideas clearly.', 'Experience designing educational products or learning management systems.\r\nFamiliarity with HTML and CSS to support collaboration with developers.\r\nExperience conducting research with people with diverse access needs.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`job_reference`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
