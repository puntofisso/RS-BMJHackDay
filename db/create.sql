-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 07, 2013 at 03:47 PM
-- Server version: 5.5.25
-- PHP Version: 5.4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `bmj`
--

-- --------------------------------------------------------

--
-- Table structure for table `answeredquestions`
--

DROP TABLE IF EXISTS `answeredquestions`;
CREATE TABLE IF NOT EXISTS `answeredquestions` (
  `username` varchar(10) NOT NULL,
  `questionid` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
CREATE TABLE IF NOT EXISTS `questions` (
  `QID` int(5) DEFAULT NULL,
  `FriendlyName` varchar(20) DEFAULT NULL,
  `QuestionTypeID_FK` int(1) DEFAULT NULL,
  `XML` varchar(6884) DEFAULT NULL,
  `TimeAllowed` int(3) DEFAULT NULL,
  `AVScore` decimal(11,10) DEFAULT NULL,
  `Attempts` int(4) DEFAULT NULL,
  `ExamID_FK` int(2) DEFAULT NULL,
  `KeyLearningPoints` varchar(296) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `username` varchar(10) NOT NULL,
  `fullname` varchar(40) NOT NULL,
  `points` int(11) NOT NULL,
  `percentcorrect` float NOT NULL,
  `level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
