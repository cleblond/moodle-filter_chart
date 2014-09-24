-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 24, 2014 at 08:04 AM
-- Server version: 5.5.38-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `moodle_eolms`
--

-- --------------------------------------------------------

--
-- Table structure for table `mdl_filter_chart`
--

CREATE TABLE IF NOT EXISTS `mdl_filter_chart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `type` varchar(55) NOT NULL,
  `title` varchar(55) NOT NULL,
  `xaxistitle` varchar(55) NOT NULL,
  `yaxistitle` varchar(55) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `mdl_filter_chart`
--

INSERT INTO `mdl_filter_chart` (`id`, `userid`, `type`, `title`, `xaxistitle`, `yaxistitle`) VALUES
(1, 2, 'scatter', 'Sometitle', 'Some X Title', 'Some Y Title'),
(4, 2, 'bar', '0', '0', '0'),
(5, 2, 'scatter', '', '%P', 'RI'),
(13, 2, 'scatter', 'dfsdg', 'gjhjk', 'uklyulyl'),
(14, 2, 'scatter', 'kjhjkhjk', 'hjkhjk', 'hjkhjkhjk'),
(15, 2, 'scatter', 'fafa', 'asfasf', 'asfasf');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
