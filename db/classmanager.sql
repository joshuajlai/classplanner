-- phpMyAdmin SQL Dump
-- version 2.11.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 10, 2008 at 02:09 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `classmanager`
--

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE IF NOT EXISTS `class` (
  `id` int(11) NOT NULL auto_increment,
  `symbol` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `userid` int(11) default NULL,
  `universityid` int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniqueclass` (`userid`,`universityid`,`symbol`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=58 ;

-- --------------------------------------------------------

--
-- Table structure for table `classoffering`
--

CREATE TABLE IF NOT EXISTS `classoffering` (
  `classid` int(11) NOT NULL,
  `termsequence` int(11) NOT NULL,
  `available` tinyint(4) NOT NULL default '0',
  `year` int(11) default NULL,
  PRIMARY KEY  (`classid`,`termsequence`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `curriculum`
--

CREATE TABLE IF NOT EXISTS `curriculum` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `universityid` int(11) default NULL,
  `degreeid` int(11) default NULL,
  `userid` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

--
-- Table structure for table `curriculumassociation`
--

CREATE TABLE IF NOT EXISTS `curriculumassociation` (
  `curriculumid` int(11) NOT NULL,
  `classid` int(11) NOT NULL,
  PRIMARY KEY  (`curriculumid`,`classid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `degree`
--

CREATE TABLE IF NOT EXISTS `degree` (
  `id` int(11) NOT NULL auto_increment,
  `universityid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`,`universityid`,`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `prerequisite`
--

CREATE TABLE IF NOT EXISTS `prerequisite` (
  `classid` int(11) NOT NULL,
  `prerequisite` int(11) NOT NULL,
  UNIQUE KEY `class` (`classid`,`prerequisite`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `storedclass`
--

CREATE TABLE IF NOT EXISTS `storedclass` (
  `userid` int(11) NOT NULL,
  `classid` int(11) NOT NULL,
  `termsequence` tinyint(11) NOT NULL,
  `year` int(11) default NULL,
  `movable` tinyint(4) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `term`
--

CREATE TABLE IF NOT EXISTS `term` (
  `universityid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY  (`universityid`,`sequence`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `university`
--

CREATE TABLE IF NOT EXISTS `university` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `universitysetting`
--

CREATE TABLE IF NOT EXISTS `universitysetting` (
  `universityid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  (`universityid`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `userclassoffering`
--

CREATE TABLE IF NOT EXISTS `userclassoffering` (
  `classid` int(11) NOT NULL,
  `termsequence` int(11) NOT NULL,
  `available` tinyint(4) NOT NULL default '0',
  `year` int(11) default NULL,
  `userid` int(11) NOT NULL,
  PRIMARY KEY  (`classid`,`termsequence`,`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `usersetting`
--

CREATE TABLE IF NOT EXISTS `usersetting` (
  `userid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  UNIQUE KEY `userid` (`userid`,`name`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `userterm`
--

CREATE TABLE IF NOT EXISTS `userterm` (
  `userid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY  (`userid`,`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
