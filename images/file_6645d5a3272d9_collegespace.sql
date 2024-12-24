-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2024 at 09:20 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `collegespace`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'test@gmail.com', '12345678');

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `content` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `subject_name`, `content`, `location`) VALUES
(1, 'AI', 'What is Artificial Intelligence?, Goals of Artificial Intelligence,What Comprises to Artificial Intelligence? ,Advantages of Artificial Intelligence,Disadvantages of Artificial Intelligence and more', '../upload/AI_POONAM/1.pdf'),
(2, 'NLP', 'What is NLP?, History, How its work.', '../upload/NLP _MEENA/Lecture1.pdf'),
(3, 'AI', 'PEAS Represent, Agent', '../upload/AI_POONAM/2.pdf'),
(4, 'AI', 'Search Algorithms in Artificial Intelligence', '../upload/AI_POONAM/2.1.pdf'),
(5, 'AI', 'Search Algorithms in Artificial Intelligence Part-2', '../upload/AI_POONAM/2.2.pdf'),
(6, 'AI', 'Knowledge-Based Agent in Artificial intelligence', '../upload/AI_POONAM/3.1.pdf'),
(7, 'NLP', 'Components of NLP', '../upload/NLP _MEENA/Lecture2.pdf'),
(8, 'NLP', 'How to implement NLP', '../upload/NLP _MEENA/Lecture3.pdf'),
(9, 'NLP', 'Components of NLP', '../upload/NLP _MEENA/Lecture4.pdf'),
(10, 'NLP', 'Why is NLP Hard?', '../upload/NLP _MEENA/Notes5.pdf'),
(11, 'NLP', 'Corpus, Element of corpus', '../upload/NLP _MEENA/Note6.pdf'),
(12, 'NLP', 'Example of Corpus', '../upload/NLP _MEENA/Note7.pdf'),
(13, 'NLP', 'Regular expressions', '../upload/NLP _MEENA/Note8.pdf'),
(14, 'NLP', 'Finite State Automata', '../upload/NLP _MEENA/Note9.pdf'),
(15, 'NLP', 'Concept of Parser', '../upload/NLP _MEENA/Note10.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `notice`
--

CREATE TABLE `notice` (
  `id` int(11) NOT NULL,
  `post_by` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `notice`
--

INSERT INTO `notice` (`id`, `post_by`, `title`, `description`) VALUES
(1, 'admin', 'fees pending', 'pay fees before 28 oct'),
(3, 'test@gmail.com', 'send fees slip', 'who have submitted their fees send their fees slip to their respected class CR'),
(4, 'test@gmail.com', 'hello', 'hello hello');

-- --------------------------------------------------------

--
-- Table structure for table `sysllabus`
--

CREATE TABLE `sysllabus` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `year` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sysllabus`
--

INSERT INTO `sysllabus` (`id`, `subject_name`, `year`, `location`) VALUES
(1, 'C', '2017', '../upload/Sysllabus/c.pdf'),
(2, 'C', '2019', 'null');

-- --------------------------------------------------------

--
-- Table structure for table `top_contributor`
--

CREATE TABLE `top_contributor` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `top_contributor`
--

INSERT INTO `top_contributor` (`id`, `username`) VALUES
(1, 'Jaspreet Singh'),
(2, 'Adam');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notice`
--
ALTER TABLE `notice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sysllabus`
--
ALTER TABLE `sysllabus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `top_contributor`
--
ALTER TABLE `top_contributor`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `notice`
--
ALTER TABLE `notice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sysllabus`
--
ALTER TABLE `sysllabus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `top_contributor`
--
ALTER TABLE `top_contributor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
