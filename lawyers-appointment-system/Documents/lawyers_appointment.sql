-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2025 at 03:20 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lawyers_appointment`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `lawyer_id` int(11) DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL,
  `case_details` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `customer_id`, `lawyer_id`, `appointment_date`, `appointment_time`, `case_details`, `status`, `created_at`) VALUES
(5, 251, 239, '2026-01-07', '20:46:00', 'Murder case defense needed for accused in Karachi High Court', 'pending', '2025-12-01 10:44:58');

-- --------------------------------------------------------

--
-- Table structure for table `lawyer_details`
--

CREATE TABLE `lawyer_details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `experience` int(11) DEFAULT NULL,
  `working_hours` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `services` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lawyer_details`
--

INSERT INTO `lawyer_details` (`id`, `user_id`, `specialization`, `location`, `experience`, `working_hours`, `description`, `services`, `image_path`) VALUES
(41, 239, 'Criminal Law', 'Karachi, Sindh', 8, '9', 'Expert criminal defense lawyer with 8+ years experience in high-profile criminal cases. Specialized in criminal litigation, bail applications, and appeals.\r\n', 'Criminal defense, Bail applications, Trial representation, Appeals', 'assets/images/1764583028_lawyer.jpg'),
(42, 240, 'Corporate Law', 'Lahore, Punjab', 10, '10', ' Corporate law specialist with expertise in business contracts, company formation, and regulatory compliance. Experienced in mergers and acquisitions.\r\n', 'Corporate contracts, Mergers & Acquisitions, Compliance, Company formation\r\n', 'assets/images/1764583216_lawyer1.jpg'),
(43, 241, 'Family Law', 'Islamabad', 6, '8', 'Compassionate family lawyer specializing in divorce, child custody, and family dispute resolution. Focus on amicable settlements.\r\n', 'Services: Divorce cases, Child custody, Marriage contracts, Family disputes\r\n', 'assets/images/1764583440_lawyer2.jpg'),
(44, 242, 'Property Law', 'Rawalpindi', 7, '9', ' Property law expert with extensive experience in real estate transactions, land disputes, and property registration.\r\n', 'Property registration, Land disputes, Title verification, Real estate transactions\r\n', 'assets/images/1764583622_lawyer4.jpg'),
(45, 243, 'Tax Law', 'Karachi , Sindh', 9, '8', 'Chartered accountant and tax lawyer specialized in tax planning, FBR representation, and tax appeals. Expert in tax optimization.\r\n', 'Services: Tax planning, FBR representation, Tax appeals, Tax optimization\r\n', 'assets/images/1764583794_lawyer5.jpg'),
(46, 244, 'Immigration Law', 'Lahore, Punjab', 6, '10', ' Immigration law specialist helping clients with visa processing, immigration appeals, and citizenship applications.\r\n', 'Visa applications, Immigration appeals, Citizenship, Work permits\r\n', 'assets/images/1764583974_lawyer6.jpg'),
(47, 247, 'Labr Law', 'Faislabad', 5, '8', ' Labor rights advocate specializing in employment law, workplace disputes, and labor rights protection.\r\n', ' Employment disputes, Labor rights, Termination cases, Workplace harassment\r\n', 'assets/images/1764584399_lawyer7.jpg'),
(48, 248, 'Intellectual Property Law', 'Islamabad', 6, '8:00 AM - 4:00 PM', 'IP law expert with focus on trademark registration, copyright protection, and intellectual property rights.\r\n', 'Trademark registration, Copyright, Patent law, IP protection\r\n', 'assets/images/1764585178_lawyer9.jpg'),
(49, 249, 'Cyber Law', 'Karachi, Sindh', 4, '11', 'Tech-savvy lawyer specializing in cyber law, data protection, digital rights, and cyber crime cases.\r\n', 'Cyber crime cases, Data protection, Digital rights, Online fraud\r\n', 'assets/images/1764584724_lawyer8.jpg'),
(50, 250, 'Banking & Finance Law', 'Lahore, Punjab', 9, '9:30 AM - 5:30 PM', 'banking lawyer with expertise in financial regulations, banking disputes, and loan agreements.\r\n', ' Banking disputes, Loan agreements, Financial regulations, Investment laws\r\n', 'assets/images/1764584962_lawyer10.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('lawyer','customer','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `user_type`, `created_at`) VALUES
(239, 'ahmed khan', 'ahmedkhan@gmail.com', '$2y$10$F/mK9v80LjBXyuI76AJUL.HzcM2JNY80EsHY7vgUPzvd5WPj96xrS', 'lawyer', '2025-12-01 09:53:02'),
(240, 'ali raza', 'aliraza@gmail.com', '$2y$10$1ilSv3NwaIjYz1amoVsOHeBiN1Tkhz5bLwHSu1boDdlhbDdu8xmA.', 'lawyer', '2025-12-01 09:58:16'),
(241, 'Usman Ahmed', 'usmanahmed@gmail.com', '$2y$10$563tzgBBpooZC6RsbQMe4.19s7RXG5epcYx2gWdiH79X3mVi1gBxe', 'lawyer', '2025-12-01 10:01:41'),
(242, 'Bilal Shah', 'bilalshah@gmail.com', '$2y$10$dfuElLYw2OBCiV4vUCI6HeNyZ9ogJfG40ncNs0xoFJ33pvfgOc2pu', 'lawyer', '2025-12-01 10:04:47'),
(243, 'Taha Mahmood', 'tahamahmood@gmail.com', '$2y$10$7Jp2Uc8U.u1t.AMc1.uxLOBrkvYnEDykAy0WaCiyIBN7a3drduaPS', 'lawyer', '2025-12-01 10:08:03'),
(244, 'Hassan Ali', 'hassanali@gmail.com', '$2y$10$lwH894We4XD.SKCxOeHHxemxgRlLe14l1r5rUPUepEAJi8BMXNmfy', 'lawyer', '2025-12-01 10:11:01'),
(245, 'Umar Farroq', 'umarfarooq@gmail.com', '$2y$10$l472NAPuclGK4kwn3IaPIeGpr91WqyyXHY45Td9Zd0Lb2AxLuB5Ee', 'lawyer', '2025-12-01 10:14:14'),
(247, 'Fahad', 'fahad@gmail.com', '$2y$10$jSGHq89WggzliAHAFVmqV.EIrNd/J3o9eyifO47TUHI45bojl8hh.', 'lawyer', '2025-12-01 10:17:04'),
(248, 'Zain Abid', 'zainabid@gmail.com', '$2y$10$qzziuU2GVj/.KkZxGUwTRuDXzlbwZOqQMD7WJ1f.msM6ym/L/f3qe', 'lawyer', '2025-12-01 10:21:08'),
(249, 'Saad Qureshi', 'saadqureshi@gmail.com', '$2y$10$qnXTPaQhfdT5eeMybgmD5ukmpONoP2itV8bM5mQigfL3zuAmaH/G6', 'lawyer', '2025-12-01 10:23:34'),
(250, 'Sara Malik', 'saramalik@gmail.com', '$2y$10$Wm1LCFaqkRwncb31q1fQz.6yDFj4cKCocecJbeZkxgerQKzvceWSW', 'lawyer', '2025-12-01 10:26:38'),
(251, 'Reaz', 'reaz@gmail.com', '$2y$10$pBzdwMCLrrDIA1aU6gJeyOfihtyAyHDN9AsrF6h4TTeWJ9FhtpIbm', 'customer', '2025-12-01 10:30:24'),
(255, 'admin', 'admin@legalconnect.com', '$2y$10$EgzgeWyajaHRi/SGiv0igOcfDaMxk0HjAUXZkpQylvCCcrr42s2K.', 'admin', '2025-12-01 11:08:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `lawyer_id` (`lawyer_id`);

--
-- Indexes for table `lawyer_details`
--
ALTER TABLE `lawyer_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lawyer_details`
--
ALTER TABLE `lawyer_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=258;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`lawyer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `lawyer_details`
--
ALTER TABLE `lawyer_details`
  ADD CONSTRAINT `lawyer_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
