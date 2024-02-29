-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2019 at 06:23 PM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `myparker`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Admin_ID` int(50) NOT NULL AUTO_INCREMENT primary key,
  `Admin_Name` varchar(50) NOT NULL,
  `Admin_Username` varchar(20) NOT NULL,
  `Admin_Password` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `Location_ID` int(50) NOT NULL AUTO_INCREMENT primary key,
  `Location_Name` int(50) NOT NULL,
  `Charges` float(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `officer`
--

CREATE TABLE `officer` (
  `Officer_ID` int(50) NOT NULL AUTO_INCREMENT primary key,
  `Officer_Name` varchar(50) NOT NULL,
  `Officer_Address` varchar(100) NOT NULL,
  `Officer_Gender` varchar(10) NOT NULL,
  `Officer_Email` varchar(50) NOT NULL,
  `Officer_MobileNumber` int(15) NOT NULL,
  `Officer_Password` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `Payment_ID` int(50) NOT NULL AUTO_INCREMENT primary key,
  `User_ID` int(50) NOT NULL,
  `Vehicle_ID` int(50) NOT NULL,
  `Location_ID` int(50) NOT NULL,
  `Start_Time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Eng_Time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Charges` float(30) NOT NULL,
  foreign key (User_ID)
  References user(User_ID),
   foreign key (Vehicle_ID)
  References vehicle(Vehicle_ID),
   foreign key (Location_ID)
  References location(Location_ID)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `summon`
--

CREATE TABLE `summon` (
  `Summon_ID` int(50) NOT NULL AUTO_INCREMENT primary key,
  `User_ID` int(50) NOT NULL,
  `Vehicle_ID` int(50) NOT NULL,
  `Location_ID` int(50) NOT NULL,
  `Officer_ID` int(50) NOT NULL,
  `Charges` float(30) NOT NULL,
  `Descriptions` varchar(100) NOT NULL,
  foreign key (User_ID)
  References user(User_ID),
   foreign key (Vehicle_ID)
  References vehicle(Vehicle_ID),
   foreign key (Location_ID)
  References location(Location_ID),
   foreign key (Officer_ID)
  References officer(Officer_ID)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `top-up`
--

CREATE TABLE `top-up` (
  `Topup_ID` int(50) NOT NULL AUTO_INCREMENT primary key,
  `User_ID` int(50) NOT NULL,
  `Amount` float(20) NOT NULL,
  foreign key (User_ID)
  References user(User_ID)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `User_ID` int(50) NOT NULL AUTO_INCREMENT primary key,
  `User_Name` varchar(50) NOT NULL,
  `User_DOB` varchar (50) NOT NULL,
  `User_Email` varchar(50) NOT NULL,
  `User_Address` varchar(100) NOT NULL,
  `User_Gender` varchar(10) NOT NULL,
  `User_MobileNumber` int(15) NOT NULL,
   `encrypted_password` varchar(50) NOT NULL,
  `salt` varchar(50) NOT NULL,
   `created_at` date NOT NULL
 
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle`
--

CREATE TABLE `vehicle` (
  `Vehicle_ID` int(50) NOT NULL AUTO_INCREMENT primary key,
  `User_ID` int(50) NOT NULL,
  `Vehicle_Number` varchar(15) NOT NULL,
   foreign key (User_ID)
  References user(User_ID)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `balance` (
  `Balance_ID` int(50) NOT NULL AUTO_INCREMENT primary key,
  `User_ID` int(50) NOT NULL,
  `balance` float(15) NOT NULL,
   foreign key (User_ID)
  References user(User_ID)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


