-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 27, 2022 at 02:21 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webdev`
--

-- --------------------------------------------------------

--
-- Table structure for table `editorial`
--

CREATE TABLE `editorial` (
  `paper_name` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `headline` varchar(255) NOT NULL,
  `content` varchar(400) DEFAULT NULL,
  `author` varchar(50) NOT NULL,
  `page_num` int(255) DEFAULT NULL,
  `news_category` varchar(20) DEFAULT NULL,
  `supplement_circle` varchar(50) DEFAULT NULL,
  `special_supplement` int(1) DEFAULT NULL,
  `language` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `forgotpassword`
--

CREATE TABLE `forgotpassword` (
  `reg_num` varchar(8) NOT NULL,
  `que_num` int(255) NOT NULL,
  `answer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `forgotpassword`
--

INSERT INTO `forgotpassword` (`reg_num`, `que_num`, `answer`) VALUES
('2021SW01', 1, 'Boy'),
('2021SW39', 3, 'black');

-- --------------------------------------------------------

--
-- Table structure for table `register`
--

CREATE TABLE `register` (
  `sno` int(255) NOT NULL,
  `reg_num` varchar(8) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(15) NOT NULL,
  `last_name` varchar(15) NOT NULL,
  `email` varchar(30) NOT NULL,
  `mobile` varchar(11) NOT NULL,
  `gender` varchar(1) NOT NULL,
  `dob` datetime NOT NULL DEFAULT current_timestamp(),
  `user_type` varchar(10) NOT NULL DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `register`
--

INSERT INTO `register` (`sno`, `reg_num`, `password`, `first_name`, `last_name`, `email`, `mobile`, `gender`, `dob`, `user_type`) VALUES
(5, '2021SW39', '$2y$10$2SfNYTKtcE6xGQoohlSdWeo4DMcmAi6r4VV7hgjWAQGuDDDJn/OWe', 'Rahul', 'Chintawar', 'rahulchintawar18@gmail.com', '9834174896', 'M', '1998-05-10 00:00:00', 'admin'),
(6, '2021CS40', '$2y$10$iVvvb7TKcjzRLbhVLeN4l.KkU0Cjl2mcXDZJztLMoVv/5qU/Z/XCa', 'Sohel', 'Sheikh', 'sohel@sohel.com', '1111111111', 'M', '2022-01-26 00:00:00', 'student'),
(8, '2021IS33', '$2y$10$/pM4vNPuHOHe4REx8dwVmOLOVI3m9iQz2mMUr4rxnBGFopx.5gAQC', 'Rahul', 'Chintawar', 'rahul.2021IS33@mnnit.ac.in', '9552342006', 'M', '2022-01-26 00:00:00', 'student'),
(9, '2021SW01', '$2y$10$8DpxHU8bh.2lfkzOWaVtOuPdDmP1RWONKD88cZL6TFffQJZHkQTtK', 'John', 'Wick', 'john.wick@continental.com', '8888888888', 'M', '1983-05-10 00:00:00', 'student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `editorial`
--
ALTER TABLE `editorial`
  ADD PRIMARY KEY (`paper_name`,`date`,`headline`);

--
-- Indexes for table `forgotpassword`
--
ALTER TABLE `forgotpassword`
  ADD PRIMARY KEY (`reg_num`);

--
-- Indexes for table `register`
--
ALTER TABLE `register`
  ADD PRIMARY KEY (`sno`,`reg_num`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `register`
--
ALTER TABLE `register`
  MODIFY `sno` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
