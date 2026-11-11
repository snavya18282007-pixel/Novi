-- NOVI - Personal Study Companion Database Schema & Demo Data
-- Target Database: novi

CREATE DATABASE IF NOT EXISTS `novi` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `novi`;

-- Drop tables in reverse order of foreign key dependencies
DROP TABLE IF EXISTS `tasks`;
DROP TABLE IF EXISTS `study_goals`;
DROP TABLE IF EXISTS `subjects`;

-- 1. Subjects Table
CREATE TABLE `subjects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `color` VARCHAR(20) DEFAULT '#4f46e5',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tasks Table
CREATE TABLE `tasks` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `subject_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `priority` ENUM('Low', 'Medium', 'High') DEFAULT 'Medium',
  `due_date` DATE NOT NULL,
  `status` ENUM('Pending', 'Completed') DEFAULT 'Pending',
  `completed_at` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Study Goals Table
CREATE TABLE `study_goals` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `goal_date` DATE UNIQUE NOT NULL,
  `target_tasks` INT NOT NULL DEFAULT 5,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Demo Data Insertion (Uses CURDATE() relative dates for instant validity)
-- --------------------------------------------------------

-- Insert Sample Subjects
INSERT INTO `subjects` (`id`, `name`, `description`, `color`) VALUES
(1, 'Python Programming', 'Core Python concepts, OOP, file handling, and scripting libraries', '#3b82f6'),
(2, 'DBMS & SQL', 'Relational database management systems, SQL queries, normalization, and indexing', '#10b981'),
(3, 'Machine Learning', 'Supervised and unsupervised learning, regression, classification, and neural nets', '#8b5cf6'),
(4, 'Operating Systems', 'Processes, CPU scheduling, memory management, and file systems', '#f59e0b'),
(5, 'Data Structures', 'Arrays, linked lists, stacks, queues, trees, graphs, and algorithm analysis', '#ec4899');

-- Insert Sample Tasks (Relative dates around CURDATE())
INSERT INTO `tasks` (`id`, `subject_id`, `title`, `description`, `priority`, `due_date`, `status`, `completed_at`) VALUES
(1, 1, 'Solve 5 LeetCode Python Problems', 'Focus on string manipulation and hash map problems', 'High', CURDATE(), 'Completed', NOW()),
(2, 2, 'Revise SQL Joins & Subqueries', 'Practice INNER JOIN, LEFT JOIN, and correlated subqueries', 'Medium', CURDATE(), 'Pending', NULL),
(3, 3, 'Complete ML Linear Regression Assignment', 'Implement gradient descent algorithm in Python from scratch', 'High', CURDATE(), 'Pending', NULL),
(4, 4, 'Study OS CPU Scheduling Algorithms', 'Review FCFS, SJF, Round Robin, and Priority scheduling algorithms', 'High', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Pending', NULL),
(5, 5, 'Implement Binary Search Tree in Python', 'Write insertion, deletion, and traversal methods (in-order, pre-order, post-order)', 'Medium', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'Pending', NULL),
(6, 1, 'Write Python Flask REST API Documentation', 'Document endpoints for user authentication and task endpoints', 'Low', DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'Pending', NULL),
(7, 2, 'Practice ER Diagrams & Normalization', 'Convert 3NF schema to BCNF and solve sample exam questions', 'High', DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'Pending', NULL),
(8, 3, 'Read CNN Research Paper', 'Take notes on convolution layers, pooling, and feature extraction', 'Low', DATE_ADD(CURDATE(), INTERVAL 4 DAY), 'Pending', NULL),
(9, 4, 'Understand Deadlock Handling Mechanisms', 'Study deadlock prevention, avoidance (Banker algorithm), and detection', 'Medium', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'Completed', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(10, 5, 'Review Heap Sort & Priority Queues', 'Implement max heapify and min heapify algorithms', 'Medium', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Completed', DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Insert Today\'s Study Goal
INSERT INTO `study_goals` (`goal_date`, `target_tasks`) VALUES
(CURDATE(), 5);
