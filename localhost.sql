-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Darbinė stotis: localhost
-- Atlikimo laikas: 2013 m. Bal 23 d. 07:21
-- Serverio versija: 5.5.24-log
-- PHP versija: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Duomenų bazė: `chat`
--
CREATE DATABASE `chat` DEFAULT CHARACTER SET utf8 COLLATE utf8_lithuanian_ci;
USE `chat`;

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `talkers`
--

CREATE TABLE IF NOT EXISTS `talkers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `nickname` varchar(30) COLLATE utf8_lithuanian_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci AUTO_INCREMENT=3 ;

--
-- Sukurta duomenų kopija lentelei `talkers`
--

INSERT INTO `talkers` (`id`, `user_id`, `nickname`) VALUES
(1, 1, 'demo'),
(2, 2, 'chatter');

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `talks`
--

CREATE TABLE IF NOT EXISTS `talks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `talker_id` int(11) DEFAULT NULL,
  `what` varchar(255) COLLATE utf8_lithuanian_ci DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci AUTO_INCREMENT=39 ;

--
-- Sukurta duomenų kopija lentelei `talks`
--

INSERT INTO `talks` (`id`, `talker_id`, `what`, `created_on`) VALUES
(37, 1, 'hi', '2013-04-23 10:15:59'),
(38, 2, 'hi:)', '2013-04-23 10:19:41');

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) COLLATE utf8_lithuanian_ci DEFAULT NULL,
  `phash` varchar(32) COLLATE utf8_lithuanian_ci DEFAULT NULL,
  `sid` varchar(32) COLLATE utf8_lithuanian_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_lithuanian_ci DEFAULT NULL,
  `aid` varchar(32) COLLATE utf8_lithuanian_ci DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci AUTO_INCREMENT=3 ;

--
-- Sukurta duomenų kopija lentelei `users`
--

INSERT INTO `users` (`id`, `login`, `phash`, `sid`, `email`, `aid`, `active`) VALUES
(1, 'demo', '6c5ac7b4d3bd3311f033f971196cfa75', 'v24s8imns8rtn1ps2h619h6r04', 'demo@example.com', '', 1),
(2, 'chatter', '8ee048da57c7619685352266f04f748a', 'v8bp8dge274peh1kj02nvjr744', 'chatter@example.com', '', 1);
--
-- Duomenų bazė: `chat_test`
--
CREATE DATABASE `chat_test` DEFAULT CHARACTER SET utf8 COLLATE utf8_lithuanian_ci;
USE `chat_test`;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
