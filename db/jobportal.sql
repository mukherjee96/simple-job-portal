-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2019 at 07:30 AM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 7.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jobportal`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(6) NOT NULL,
  `name` varchar(200) NOT NULL,
  `username` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `username`, `password`) VALUES
(1, 'admin', 'admin', '123');

-- --------------------------------------------------------

--
-- Table structure for table `employer`
--

CREATE TABLE `employer` (
  `id` int(5) NOT NULL,
  `cname` varchar(300) NOT NULL,
  `rname` varchar(100) NOT NULL,
  `sector` varchar(100) NOT NULL,
  `formed` varchar(4) NOT NULL,
  `pan` varchar(10) NOT NULL,
  `type` varchar(10) NOT NULL,
  `address` varchar(500) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `remail` varchar(100) NOT NULL,
  `cemail` varchar(100) NOT NULL,
  `website` varchar(100) NOT NULL,
  `no_of_emp` varchar(6) NOT NULL,
  `password` varchar(255) NOT NULL,
  `logo` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `employer`
--

INSERT INTO `employer` (`id`, `cname`, `rname`, `sector`, `formed`, `pan`, `type`, `address`, `phone`, `remail`, `cemail`, `website`, `no_of_emp`, `password`, `logo`) VALUES
(1, 'Infosys', 'John Doe', 'IT', '2018', 'ABCD1234F', 'LLP', '1234 Main St', '9876543210', 'john.doe@gmail.com', 'abc@gmail.com', 'http://abc.com', '50', '$2y$10$AMlpp82o2Ti4SUhD1JEEQ.YPSI3ihiF0qfjDKIjC0oIEqQP00kiSm', 'e87b6157d8c224e9054c1e0506d5c2a0.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(10) NOT NULL,
  `emp_id` int(6) NOT NULL,
  `title` text NOT NULL,
  `designation` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `salary` varchar(7) NOT NULL,
  `experience` varchar(2) NOT NULL,
  `location` varchar(300) NOT NULL,
  `highlighted` varchar(5) NOT NULL,
  `available` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `emp_id`, `title`, `designation`, `description`, `salary`, `experience`, `location`, `highlighted`, `available`) VALUES
(1, 1, 'Urgent Requirement for Solution Architect', 'Solution Architect', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras interdum mauris sit amet rhoncus pretium. Vivamus et rhoncus diam. Cras in tincidunt quam. Donec varius ex ac massa semper, semper consequat nunc dapibus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Phasellus varius finibus convallis. Duis ut cursus ipsum. Nulla facilisi.', '4', '3', 'Delhi', 'true', 'false'),
(2, 1, 'Full Stack Web Developer Required', 'Web Developer', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras interdum mauris sit amet rhoncus pretium. Vivamus et rhoncus diam. Cras in tincidunt quam. Donec varius ex ac massa semper.', '10', '5', 'Kolkata', 'true', 'true');

-- --------------------------------------------------------

--
-- Table structure for table `jobseeker`
--

CREATE TABLE `jobseeker` (
  `id` varchar(6) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `address` varchar(500) NOT NULL,
  `fresher` varchar(5) NOT NULL,
  `present_company` varchar(200) NOT NULL,
  `designation` varchar(500) NOT NULL,
  `salary` varchar(6) NOT NULL,
  `experience` varchar(2) NOT NULL,
  `cv` varchar(100) NOT NULL,
  `verified` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `jobseeker`
--

INSERT INTO `jobseeker` (`id`, `name`, `email`, `password`, `phone`, `address`, `fresher`, `present_company`, `designation`, `salary`, `experience`, `cv`, `verified`) VALUES
('24ar96', 'Aritra Mukherjee', 'aritramukherjee100@gmail.com', '$2y$10$I9d8zweAroOfCqygGjgNV.gv4iTiS12GkWFVHGWsCVepUp.n7WPLi', '9674303832', 'Jadavpur', 'false', 'ABCD', 'Web Developer', '700000', '3', 'eae3ffd61ce099162b52f8986bbc2f23.pdf', 'true'),
('50558e', 'Adrita Roy', 'adrita.roy2104@gmail.com', '$2y$10$ySZBbc4kIqz9k91PwjDVeekcvs6h6q1RDWQ/.Z6HtVL4i7qNWyzky', '', '', '', '', '', '', '', '', 'false');

-- --------------------------------------------------------

--
-- Table structure for table `jobtech`
--

CREATE TABLE `jobtech` (
  `id` int(10) NOT NULL,
  `job_id` int(10) NOT NULL,
  `technology` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `jobtech`
--

INSERT INTO `jobtech` (`id`, `job_id`, `technology`) VALUES
(11, 2, 'Angular'),
(12, 2, 'Firebase'),
(13, 2, 'Node.js'),
(18, 1, 'Big Data'),
(19, 1, 'Cloud');

-- --------------------------------------------------------

--
-- Table structure for table `jsskills`
--

CREATE TABLE `jsskills` (
  `id` int(6) NOT NULL,
  `jsid` varchar(6) NOT NULL,
  `skill` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `jsskills`
--

INSERT INTO `jsskills` (`id`, `jsid`, `skill`) VALUES
(166, '24ar96', 'html'),
(167, '24ar96', 'css'),
(168, '24ar96', 'php');

-- --------------------------------------------------------

--
-- Table structure for table `jstenth`
--

CREATE TABLE `jstenth` (
  `id` int(6) NOT NULL,
  `jsid` varchar(6) NOT NULL,
  `board` varchar(500) NOT NULL,
  `yop` varchar(4) NOT NULL,
  `marks` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `jstenth`
--

INSERT INTO `jstenth` (`id`, `jsid`, `board`, `yop`, `marks`) VALUES
(1, '24ar96', 'WBBSE', '2013', '80'),
(7, '208dd0', '', '', ''),
(8, '50558e', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `jstwelveth`
--

CREATE TABLE `jstwelveth` (
  `id` int(6) NOT NULL,
  `jsid` varchar(6) NOT NULL,
  `board` varchar(500) NOT NULL,
  `stream` varchar(200) NOT NULL,
  `yop` varchar(4) NOT NULL,
  `marks` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `jstwelveth`
--

INSERT INTO `jstwelveth` (`id`, `jsid`, `board`, `stream`, `yop`, `marks`) VALUES
(1, '24ar96', 'WBBHSE', 'Science', '2015', '70'),
(7, '208dd0', '', '', '', ''),
(8, '50558e', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `jsug`
--

CREATE TABLE `jsug` (
  `id` int(6) NOT NULL,
  `jsid` varchar(6) NOT NULL,
  `university` varchar(500) NOT NULL,
  `dept` varchar(200) NOT NULL,
  `yop` varchar(4) NOT NULL,
  `marks` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `jsug`
--

INSERT INTO `jsug` (`id`, `jsid`, `university`, `dept`, `yop`, `marks`) VALUES
(1, '24ar96', 'WBUT', 'CSE', '2020', '8'),
(7, '208dd0', '', '', '', ''),
(8, '50558e', '', '', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employer`
--
ALTER TABLE `employer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobseeker`
--
ALTER TABLE `jobseeker`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobtech`
--
ALTER TABLE `jobtech`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jsskills`
--
ALTER TABLE `jsskills`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jstenth`
--
ALTER TABLE `jstenth`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jstwelveth`
--
ALTER TABLE `jstwelveth`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jsug`
--
ALTER TABLE `jsug`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employer`
--
ALTER TABLE `employer`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobtech`
--
ALTER TABLE `jobtech`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `jsskills`
--
ALTER TABLE `jsskills`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT for table `jstenth`
--
ALTER TABLE `jstenth`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `jstwelveth`
--
ALTER TABLE `jstwelveth`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `jsug`
--
ALTER TABLE `jsug`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
