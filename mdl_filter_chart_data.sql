-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 24, 2014 at 08:05 AM
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
-- Table structure for table `mdl_filter_chart_data`
--

CREATE TABLE IF NOT EXISTS `mdl_filter_chart_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chartid` int(11) NOT NULL,
  `x1` varchar(55) NOT NULL,
  `y1` varchar(55) NOT NULL,
  `x2` varchar(55) NOT NULL,
  `y2` varchar(55) NOT NULL,
  `x3` varchar(55) NOT NULL,
  `y3` varchar(55) NOT NULL,
  `x4` varchar(55) NOT NULL,
  `y4` varchar(55) NOT NULL,
  `x5` varchar(55) NOT NULL,
  `y5` varchar(55) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `mdl_filter_chart_data`
--

INSERT INTO `mdl_filter_chart_data` (`id`, `chartid`, `x1`, `y1`, `x2`, `y2`, `x3`, `y3`, `x4`, `y4`, `x5`, `y5`) VALUES
(1, 1, '1', '1', '2', '3', '3', '9', '', '', '', ''),
(2, 1, '1', '3', '2', '5', '3', '11', '', '', '', ''),
(3, 1, '2', '4', '3', '8', '5', '8', '', '', '', ''),
(4, 14, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1'),
(5, 15, '', '', '', '', '', '', '', '', '', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
