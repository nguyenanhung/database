-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 22, 2021 at 02:38 AM
-- Server version: 10.0.38-MariaDB
-- PHP Version: 8.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `my_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_news_version_2_config`
--

CREATE TABLE `config` (
  `id` varchar(128) CHARACTER SET utf8 NOT NULL,
  `value` longtext CHARACTER SET utf8,
  `label` varchar(127) CHARACTER SET utf8 DEFAULT NULL,
  `type` tinyint(7) DEFAULT '0' COMMENT '0: string, 1: number, 2: json'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `data_news_version_2_config`
--

INSERT INTO `config` (`id`, `value`, `label`, `type`) VALUES
('hungng_site_author', '3', 'Nguyen An Hung', 2),
('hungng_site_portfolio', '2', 'https://nguyenanhung.com', 2),
('hungng_site_resume', '1', 'https://nguyenanhung.com/resume', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_news_version_2_config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`) USING BTREE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
