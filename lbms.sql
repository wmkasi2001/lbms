-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2024 at 01:12 AM
-- Server version: 10.4.13-MariaDB
-- PHP Version: 7.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lbms`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `isbn` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `accession_number` varchar(50) NOT NULL,
  `added_by` varchar(255) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `barcode` varchar(255) NOT NULL,
  `available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`isbn`, `author`, `title`, `category`, `accession_number`, `added_by`, `date_added`, `barcode`, `available`) VALUES
('4891028164517', 'Ikupu Vanuawaru', 'Vita', 'The Arts (700 - 799)', '148/M69', 'Remona Lapan', '2024-10-08 14:00:00', '4891028164517', 1),
('8904238301392', 'Joe Blow', 'Fogg Master Body Spray', 'Natural Sciences & Mathematics (500 - 599)', '147/M68', 'Remona Lapan', '2024-10-08 14:00:00', '8904238301392', 1),
('9300675030298', 'Carlos', 'Natures Own', 'Geography (900 - 999)', '147/M66', 'Remona Lapan', '2024-10-08 14:00:00', '9300675030298', 1),
('9780074603703', 'Subrahmanyam Vedam', 'Electric Drives - Concepts and Appications', 'Philosophy & Psychology (100 - 199)', '8562', 'Ms Rosemary', '2024-10-08 14:00:00', '9780074603703', 1);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `feedback` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `name`, `feedback`, `timestamp`) VALUES
(2, 'Wayne Kasi', 'This is an awesome website.!!!! All in all its a 10/10 for me!!!', '2024-08-07 23:56:05'),
(3, 'Fred Kembu', 'All in all its a 10 for me. Best website and it has served its purpose.', '2024-08-07 23:56:54');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `request_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `return_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `student_id`, `message`, `request_id`, `created_at`, `return_date`) VALUES
(1, '211051', 'Your request for the book has been approved. Return date: 2024-10-11.', 22, '2024-10-07 04:43:20', NULL),
(2, '211051', 'Your request for the book has been approved. Return date: 2024-10-14.', 23, '2024-10-07 04:49:16', '0000-00-00'),
(3, '211053', 'Your request for the book has been approved. Return date: 2024-10-10.', 24, '2024-10-07 04:53:14', NULL),
(4, '211051', 'Your request for the book has been approved. Return date: 2024-10-14.', 25, '2024-10-07 04:54:21', NULL),
(11, '211053', 'Your request for the book has been approved. Return date: 2024-10-16.', 7, '2024-10-09 06:28:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `user_id` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`user_id`, `token`, `expires`) VALUES
('204022', '3614f9df8201e2aee7b7d2279554a4323f66bdc0ff8c04f2a68faf95cd7a3849', '2024-09-17 08:57:52'),
('204022', '97d7ec10bdf992c55ccd80f81315c5d4459c559da2b3a6720c89362e3af9a41b', '2024-09-17 08:45:41'),
('211051', 'c00904ee5e276117899244e264d489d8ce1ed2b2a0d14f4ca020960a4a3ac485', '2024-09-17 09:05:04'),
('204022', 'd9364ba22ff2a704245c4d80c2be289b5923bfd071a0f895dba886763faa2ea0', '2024-09-17 08:51:30'),
('204022', 'ef00d9a35fec1204bf666c99033302d66ce62ea350db201c10595716e22e8940', '2024-09-17 09:04:56');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `book_id` varchar(255) NOT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `return_date` date NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `student_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `student_name`, `book_id`, `request_date`, `return_date`, `status`, `student_id`) VALUES
(7, '211053', '9780074603703', '2024-10-09 06:27:48', '2024-10-09', '', '211053');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `Fname` text NOT NULL,
  `Lname` text NOT NULL,
  `U_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`Fname`, `Lname`, `U_name`, `password`, `email`, `is_admin`, `reset_token`, `token_expiry`) VALUES
('Melissa', 'Moliyola', '204022', 'melissa12345', 'mkmoliyola@gmail.com', 0, NULL, NULL),
('Roy', 'Baki', '211015', 'roybaki2024', 'roybaki@gmail.com', 0, NULL, NULL),
('Wayne', 'Kasi', '211051', 'test@12345', 'wkasi@gmail.com', 0, NULL, NULL),
('Fred', 'Kembu', '211053', 'password12345', 'fkembu@gmail.com', 0, NULL, NULL),
('Remona', 'Lapan', '211063', 'test12345', 'rlapan@gmail.com', 1, NULL, NULL),
('Anthony', 'Niniku', '213018', 'password12345', 'ninikuanthony@gmail.com', 1, NULL, NULL),
('Rosemary ', 'Kase', 'rkase', 's22024', 'rkase@dbti.ac.pg', 1, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`isbn`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`token`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `fk_student_username` (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`U_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `fk_student_username` FOREIGN KEY (`student_id`) REFERENCES `users` (`U_name`),
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`isbn`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
