-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2024 at 09:21 AM
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
-- Database: `digital_classroom`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE `announcement` (
  `ann_id` int(5) NOT NULL,
  `ann_text` varchar(200) NOT NULL,
  `ann_media` varchar(500) NOT NULL,
  `class_id` int(5) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcement`
--

INSERT INTO `announcement` (`ann_id`, `ann_text`, `ann_media`, `class_id`, `upload_date`) VALUES
(14, 'hello', '[\"..\\/images\\/file_66426f2802bb5_apple-touch-icon.png\",\"..\\/images\\/file_66426f2803437_CS24S68205537_AdmitCard.pdf\"]', 17, '2024-05-13 19:51:04'),
(15, '', '[\"..\\/images\\/file_664270b69f5fc_j.PNG\",\"..\\/images\\/file_664270b69fead_j41.PNG\",\"..\\/images\\/file_664270b6a009f_Office Order for Technical Events 2023 .pdf\"]', 17, '2024-05-13 19:57:42'),
(16, 'hello', '[]', 17, '2024-05-13 19:57:54'),
(18, 'I heard an announcement on the loudspeaker saying that the store was closing in 10 minutes. The company president made an announcement about the merger. He asked us to pay attention because he had an ', '[\"..\\/images\\/file_66456dbfccee2_Office Order for Technical Events 2023 .pdf\"]', 17, '2024-05-16 02:21:51');

-- --------------------------------------------------------

--
-- Table structure for table `assignmnet`
--

CREATE TABLE `assignmnet` (
  `ass_id` int(5) NOT NULL,
  `ass_name` varchar(40) NOT NULL,
  `ass_description` varchar(200) NOT NULL,
  `ass_media` varchar(500) NOT NULL,
  `due_date` datetime DEFAULT NULL,
  `upload_date` timestamp(5) NOT NULL DEFAULT current_timestamp(5) ON UPDATE current_timestamp(5),
  `marks` int(8) DEFAULT NULL,
  `class_id` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignmnet`
--

INSERT INTO `assignmnet` (`ass_id`, `ass_name`, `ass_description`, `ass_media`, `due_date`, `upload_date`, `marks`, `class_id`) VALUES
(5, 'PHP Check', 'helooo guys', '../images/j41.PNG', '2024-04-29 00:47:00', '2024-04-27 15:11:50.75128', 100, 20),
(8, 'dvds', '', '[\"..\\/images\\/file_66427cbd97a2f_apple-touch-icon.png\",\"..\\/images\\/file_66427cbd9830a_CS24S68205537_AdmitCard.pdf\"]', NULL, '2024-05-13 20:49:01.62512', NULL, 17);

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `att_id` int(11) NOT NULL,
  `date_time` datetime NOT NULL,
  `att_description` varchar(50) NOT NULL,
  `class_id` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`att_id`, `date_time`, `att_description`, `class_id`) VALUES
(39, '2024-05-10 18:08:00', 'hello class', 17),
(43, '2024-05-10 18:19:00', 'hello class', 17),
(44, '2024-05-10 18:19:00', '', 17),
(45, '2024-05-10 18:19:00', 'Class9', 17),
(46, '2024-05-08 18:22:00', 'hello students hello', 17),
(47, '2024-05-10 18:20:00', '', 17),
(48, '2024-05-10 18:20:00', '', 17),
(49, '2024-05-11 01:11:00', '', 20),
(50, '2024-05-11 01:11:00', '', 20);

-- --------------------------------------------------------

--
-- Table structure for table `attendance_details`
--

CREATE TABLE `attendance_details` (
  `att_id` int(10) NOT NULL,
  `std_id` varchar(20) NOT NULL,
  `attendance` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_details`
--

INSERT INTO `attendance_details` (`att_id`, `std_id`, `attendance`) VALUES
(39, '20010205044', 1),
(39, '20010203023', 1),
(39, '20010203008', 1),
(43, '20010205044', 1),
(43, '20010203023', 0),
(43, '20010203008', 1),
(44, '20010205044', 1),
(44, '20010203023', 1),
(44, '20010203008', 1),
(45, '20010205044', 1),
(45, '20010203023', 0),
(45, '20010203008', 1),
(46, '20010205044', 1),
(46, '20010203023', 1),
(46, '20010203008', 1),
(47, '20010205044', 1),
(47, '20010203023', 1),
(47, '20010203008', 0),
(48, '20010205044', 1),
(48, '20010203023', 1),
(48, '20010203008', 1),
(49, '20010203023', 1),
(50, '20010203023', 1);

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `course_id` int(5) NOT NULL,
  `course_name` varchar(50) NOT NULL,
  `dpt_id` int(5) NOT NULL,
  `semester` int(3) NOT NULL,
  `teacher_id` int(5) NOT NULL,
  `class_code` varchar(7) NOT NULL,
  `total_taken_classes` int(5) NOT NULL DEFAULT 0,
  `description` varchar(150) NOT NULL,
  `background` varchar(30) NOT NULL,
  `archive` int(3) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`course_id`, `course_name`, `dpt_id`, `semester`, `teacher_id`, `class_code`, `total_taken_classes`, `description`, `background`, `archive`) VALUES
(17, 'Test Class 1', 1, 3, 4, '0hmk3qh', 7, 'This is my first class .I hope this will be great experience for all.', '#427AA1', 0),
(20, 'Test Class 2', 1, 3, 4, '3jg0xno', 2, 'This is my second class .I hope this will be great experience for all.', '#A5BE00', 0),
(21, 'Test Class 4', 1, 2, 4, 'pvon9uv', 0, 'This is my fourth class .I hope this will be great experience for all.', '#427AA1', 1),
(23, 'Test Class 5', 1, 5, 4, 'phwdnub', 0, 'This is my fifth class .I hope this will be great experience for all.', '#679436', 0);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `dpt_id` int(5) NOT NULL,
  `dpt_name` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`dpt_id`, `dpt_name`) VALUES
(1, 'EE'),
(2, 'ECE'),
(3, 'ME'),
(4, 'CSE'),
(6, 'CE');

-- --------------------------------------------------------

--
-- Table structure for table `join_class`
--

CREATE TABLE `join_class` (
  `std_id` varchar(20) NOT NULL,
  `class_id` int(5) NOT NULL,
  `att_percentage` float NOT NULL DEFAULT 100,
  `total_attendance` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `join_class`
--

INSERT INTO `join_class` (`std_id`, `class_id`, `att_percentage`, `total_attendance`) VALUES
('20010205044', 17, 100, 7),
('20010203023', 17, 71.4286, 5),
('20010203023', 20, 100, 2),
('20010203008', 17, 85.7143, 6);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_rollno` varchar(20) NOT NULL,
  `student_name` varchar(40) NOT NULL,
  `student_email` varchar(40) NOT NULL,
  `student_password` varchar(30) NOT NULL,
  `dpt_id` int(5) NOT NULL,
  `semester` int(5) NOT NULL,
  `phone_number` bigint(15) NOT NULL,
  `Role` int(2) NOT NULL DEFAULT 3
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_rollno`, `student_name`, `student_email`, `student_password`, `dpt_id`, `semester`, `phone_number`, `Role`) VALUES
('20010203008', 'Ankit Sharma', 'ankit@gmail.com', '#Ankit123', 1, 3, 9863364543, 3),
('20010203023', 'Jatin Vardhan', 'jatin071103@gmail.com', '#Jv071103', 1, 3, 7876635520, 3),
('20010203024', 'karan', 'karan@gmail.com', '@Karan123', 1, 3, 5677654323, 3),
('20010203025', 'kriti sharma', 'kriti@gmail.com', 'kriti', 1, 3, 9863364543, 3),
('20010203026', 'Priya', 'priya@gmail.com', '$Priya123', 1, 5, 7876669518, 3),
('20010205025', 'Jatin ', 'jatin@gmail.com', 'Jatin', 1, 5, 7876635520, 3),
('20010205035', 'Palak Bharti', 'bhartipalak18@gmail.com', 'palak', 1, 3, 7876635520, 3),
('20010205038', 'Jatin', 'vardhanjatin1@gmail.com', '783212', 1, 5, 8967532744, 3),
('2001020503i7', 'jatin', 'shi143shi@gmail.com', '@jSrw13145', 4, 7, 9863364543, 3),
('20010205044', 'Shivam Gupta', 'shivam@gmail.com', 'shivam', 1, 3, 7676526762, 3),
('20010205063', 'Yash Chauhan', 'yash0412@gmail.com', '%Yash123', 1, 3, 9863364543, 3),
('20010205069', 'Reshma', 'resh@gmail.com', '#Resh1234', 1, 1, 7876635520, 3),
('21002020121', 'Rohit', 'rohit@gmail.com', 'rohit', 3, 1, 8726652525, 3);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(5) NOT NULL,
  `teacher_name` varchar(30) NOT NULL,
  `teacher_email` varchar(40) NOT NULL,
  `phone_number` bigint(15) NOT NULL,
  `teacher_password` varchar(30) NOT NULL,
  `dpt_id` int(5) DEFAULT NULL,
  `role` int(2) NOT NULL DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `teacher_name`, `teacher_email`, `phone_number`, `teacher_password`, `dpt_id`, `role`) VALUES
(1, 'Anurag Sharma', 'anurag_sharma@gmail.com', 7976643242, '@Admin123', 1, 1),
(2, 'Jeet Thakur', 'jeet@gmail.com', 9967654526, '@jeet123', 2, 1),
(4, 'Sumit Sharma', 'sumit123@gmail.com', 8967532744, 'sumit', 1, 2),
(11, 'Simran Thakur', 'simtha@gmail.com', 9863364543, '74576', 1, 2),
(16, 'Sandeep Sharma', 'sandeep@gmail.com', 8967532744, '097837', 2, 2),
(17, 'Manish Kumar', 'manish@gmail.com', 9876732122, 'muni', 2, 2),
(18, 'Rohit', 'rohit@gmail.com', 5677654323, '#Rohit@123', 9999, 2);

-- --------------------------------------------------------

--
-- Table structure for table `upload_work`
--

CREATE TABLE `upload_work` (
  `ass_id` int(5) NOT NULL,
  `std_id` varchar(20) NOT NULL,
  `ass_file` varchar(50) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `marks_given` int(3) NOT NULL DEFAULT -1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `upload_work`
--

INSERT INTO `upload_work` (`ass_id`, `std_id`, `ass_file`, `upload_date`, `marks_given`) VALUES
(8, '20010203023', '[\"..\\/images\\/file_66434473ba867_000.PNG\",\"..\\/ima', '2024-05-16 06:17:42', 76),
(8, '20010203008', '[\"..\\/images\\/file_6645a31f3e581_file.png\",\"..\\/im', '2024-05-16 06:17:42', 67);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`ann_id`),
  ADD KEY `class_check2` (`class_id`);

--
-- Indexes for table `assignmnet`
--
ALTER TABLE `assignmnet`
  ADD PRIMARY KEY (`ass_id`),
  ADD KEY `class_check` (`class_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`att_id`),
  ADD KEY `att_check` (`class_id`);

--
-- Indexes for table `attendance_details`
--
ALTER TABLE `attendance_details`
  ADD KEY `atten_check` (`att_id`),
  ADD KEY `stu_check` (`std_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `teacher_check` (`teacher_id`),
  ADD KEY `did2_check` (`dpt_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`dpt_id`);

--
-- Indexes for table `join_class`
--
ALTER TABLE `join_class`
  ADD KEY `std_check` (`std_id`),
  ADD KEY `cid_check` (`class_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_rollno`),
  ADD KEY `did_check` (`dpt_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`),
  ADD KEY `did1_check` (`dpt_id`);

--
-- Indexes for table `upload_work`
--
ALTER TABLE `upload_work`
  ADD KEY `std1_check` (`std_id`),
  ADD KEY `ass_check` (`ass_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `ann_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `assignmnet`
--
ALTER TABLE `assignmnet`
  MODIFY `ass_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `att_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `course_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `dpt_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10004;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcement`
--
ALTER TABLE `announcement`
  ADD CONSTRAINT `class_check2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`course_id`);

--
-- Constraints for table `assignmnet`
--
ALTER TABLE `assignmnet`
  ADD CONSTRAINT `class_check` FOREIGN KEY (`class_id`) REFERENCES `classes` (`course_id`);

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `att_check` FOREIGN KEY (`class_id`) REFERENCES `classes` (`course_id`);

--
-- Constraints for table `attendance_details`
--
ALTER TABLE `attendance_details`
  ADD CONSTRAINT `atten_check` FOREIGN KEY (`att_id`) REFERENCES `attendance` (`att_id`),
  ADD CONSTRAINT `stu_check` FOREIGN KEY (`std_id`) REFERENCES `students` (`student_rollno`);

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `did2_check` FOREIGN KEY (`dpt_id`) REFERENCES `departments` (`dpt_id`),
  ADD CONSTRAINT `teacher_check` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`);

--
-- Constraints for table `join_class`
--
ALTER TABLE `join_class`
  ADD CONSTRAINT `cid_check` FOREIGN KEY (`class_id`) REFERENCES `classes` (`course_id`),
  ADD CONSTRAINT `std_check` FOREIGN KEY (`std_id`) REFERENCES `students` (`student_rollno`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `did_check` FOREIGN KEY (`dpt_id`) REFERENCES `departments` (`dpt_id`);

--
-- Constraints for table `upload_work`
--
ALTER TABLE `upload_work`
  ADD CONSTRAINT `ass_check` FOREIGN KEY (`ass_id`) REFERENCES `assignmnet` (`ass_id`),
  ADD CONSTRAINT `std1_check` FOREIGN KEY (`std_id`) REFERENCES `students` (`student_rollno`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
