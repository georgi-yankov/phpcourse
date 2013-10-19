-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 19, 2013 at 02:24 PM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `books_users`
--
CREATE DATABASE `books_users` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `books_users`;

-- --------------------------------------------------------

--
-- Table structure for table `access_levels`
--

CREATE TABLE IF NOT EXISTS `access_levels` (
  `access_lvl` tinyint(4) NOT NULL AUTO_INCREMENT,
  `access_name` varchar(50) NOT NULL,
  PRIMARY KEY (`access_lvl`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `access_levels`
--

INSERT INTO `access_levels` (`access_lvl`, `access_name`) VALUES
(1, 'User'),
(2, 'Moderator'),
(3, 'Administrator');

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE IF NOT EXISTS `authors` (
  `author_id` int(11) NOT NULL AUTO_INCREMENT,
  `author_name` varchar(250) NOT NULL,
  PRIMARY KEY (`author_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`author_id`, `author_name`) VALUES
(9, 'Yann Le Scouarnec'),
(8, 'Jason Gerner'),
(7, 'Elizabeth Naramore'),
(6, 'Timothy Boronczyk'),
(4, 'Anupom Syam'),
(3, 'Ahsanul Bari'),
(2, 'Dirk Merkel'),
(1, 'Hasin Hayder'),
(10, 'Jeremy Stolz');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE IF NOT EXISTS `books` (
  `book_id` int(11) NOT NULL AUTO_INCREMENT,
  `book_title` varchar(250) NOT NULL,
  PRIMARY KEY (`book_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `book_title`) VALUES
(3, 'CakePHP Application Development'),
(1, 'Object-Oriented Programming with PHP5'),
(2, 'Expert PHP 5 Tools'),
(6, 'Beginning PHP 6, Apache, MySQL 6 Web Development');

-- --------------------------------------------------------

--
-- Table structure for table `books_authors`
--

CREATE TABLE IF NOT EXISTS `books_authors` (
  `book_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  KEY `book_id` (`book_id`),
  KEY `author_id` (`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `books_authors`
--

INSERT INTO `books_authors` (`book_id`, `author_id`) VALUES
(3, 3),
(1, 1),
(6, 9),
(6, 6),
(6, 10),
(6, 7),
(6, 8),
(3, 4),
(2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_book_id` int(11) NOT NULL,
  `comment_user_id` int(11) NOT NULL,
  `comment_content` varchar(500) NOT NULL,
  `comment_date` datetime NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `passwd` varchar(50) NOT NULL,
  `access_lvl` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `passwd`, `access_lvl`) VALUES
(1, 'Atanas', 'Atanas', 3),
(2, 'Martin', 'Martin', 2),
(3, 'Stamat', 'Stamat', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
