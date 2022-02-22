-- phpMyAdmin SQL Dump
-- https://www.phpmyadmin.net/
--
-- Server version: 10.4.22-MariaDB-log-cll-lve
-- Generate this database description as follows:
-- phpMyAdmin -> Export -> Custom:
-- Untick 'Data' column, Tick 'View output as text', Go.
--
-- Save in file, first inappropriate headers & the lines:
-- Database: `xxxxxx`
-- statement SET time_zon = "...";
-- statement SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
--
-- Remove superfluous per-column-collation & Engine=InnnoDB with:
-- sed -ri 's/( ENGINE=InnoDB| COLLATE utf8mb4_unicode_ci)//'

START TRANSACTION;

-- --------------------------------------------------------

--
-- Table structure for table `blogposts`
--

CREATE TABLE `blogposts` (
  `Title` varchar(200) NOT NULL,
  `SubTitle` varchar(200) NOT NULL,
  `DateTime` datetime NOT NULL,
  `PostTag` varchar(20) NOT NULL,
  `PostNumber` int(11) NOT NULL,
  `Body` mediumtext NOT NULL,
  `Tags` varchar(30) NOT NULL,
  `Public` tinyint(1) NOT NULL,
  `Comments` tinyint(1) NOT NULL,
  `Views` int(11) NOT NULL
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `Comment_ID` int(11) NOT NULL,
  `PostNumber` int(11) NOT NULL,
  `DateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `Confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `Public` tinyint(1) NOT NULL DEFAULT 0,
  `Email` varchar(100) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `CommentText` mediumtext NOT NULL
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments_uuids`
--

CREATE TABLE `comments_uuids` (
  `Comment_ID` int(11) NOT NULL,
  `UUID` char(36) NOT NULL
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `form_captchas`
--

CREATE TABLE `form_captchas` (
  `FormID` int(11) NOT NULL,
  `CaptchaCode` smallint(6) NOT NULL,
  `CaptchaSeed` int(11) NOT NULL,
  `PostNumber` int(11) NOT NULL,
  `TimeStamp` timestamp NOT NULL DEFAULT current_timestamp()
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `ID` int(11) NOT NULL,
  `Name` varchar(200) NOT NULL,
  `Body` mediumtext NOT NULL,
  `LastModified` datetime NOT NULL DEFAULT current_timestamp()
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blogposts`
--
ALTER TABLE `blogposts`
  ADD UNIQUE KEY `PostTag` (`PostTag`),
  ADD UNIQUE KEY `PostNumber` (`PostNumber`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`Comment_ID`);

--
-- Indexes for table `comments_uuids`
--
ALTER TABLE `comments_uuids`
  ADD PRIMARY KEY (`Comment_ID`);

--
-- Indexes for table `form_captchas`
--
ALTER TABLE `form_captchas`
  ADD PRIMARY KEY (`FormID`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blogposts`
--
ALTER TABLE `blogposts`
  MODIFY `PostNumber` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `Comment_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments_uuids`
--
ALTER TABLE `comments_uuids`
  ADD CONSTRAINT `comments_uuids_ibfk_1` FOREIGN KEY (`Comment_ID`) REFERENCES `comments` (`Comment_ID`) ON DELETE CASCADE;
COMMIT;

