-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- 생성 시간: 25-12-12 06:31
-- 서버 버전: 10.4.32-MariaDB
-- PHP 버전: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 데이터베이스: `phpproject_db`
--

-- --------------------------------------------------------

--
-- 테이블 구조 `members`
--

CREATE TABLE `members` (
  `num` int(11) NOT NULL,
  `userid` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `userpw` varchar(255) NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `member_tbl`
--

CREATE TABLE `member_tbl` (
  `id` int(11) NOT NULL,
  `userid` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `userpw` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `gender` char(1) DEFAULT NULL,
  `addr` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 테이블의 덤프 데이터 `member_tbl`
--

INSERT INTO `member_tbl` (`id`, `userid`, `username`, `userpw`, `phone`, `gender`, `addr`) VALUES
(3, 'test', 'test01', 'test01', '010-1234-5678', 'M', '');

-- --------------------------------------------------------

--
-- 테이블 구조 `memo_tbl`
--

CREATE TABLE `memo_tbl` (
  `iMemo` int(11) NOT NULL,
  `sID` varchar(50) NOT NULL,
  `sTitle` varchar(100) NOT NULL,
  `sContent` text NOT NULL,
  `regDate` datetime DEFAULT current_timestamp(),
  `editDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 테이블의 덤프 데이터 `memo_tbl`
--

INSERT INTO `memo_tbl` (`iMemo`, `sID`, `sTitle`, `sContent`, `regDate`, `editDate`) VALUES
(4, 'test', '테스트', '메모 내용이 잘 들어가는지 테스트 목적으로 만든 메모장입니다.\r\n해당 프로젝트를 계속 개발할 예정입니다. \r\n앞으로의 또 어떠한 기능을 넣어볼지 고민중입니다.', '2025-12-12 12:49:58', NULL);

-- --------------------------------------------------------

--
-- 테이블 구조 `todo_tbl`
--

CREATE TABLE `todo_tbl` (
  `iTodo` int(11) NOT NULL,
  `sID` varchar(20) NOT NULL,
  `sTitle` varchar(100) NOT NULL,
  `sContent` text DEFAULT NULL,
  `isDone` tinyint(1) DEFAULT 0,
  `dueDate` date DEFAULT NULL,
  `regDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 테이블의 덤프 데이터 `todo_tbl`
--

INSERT INTO `todo_tbl` (`iTodo`, `sID`, `sTitle`, `sContent`, `isDone`, `dueDate`, `regDate`) VALUES
(8, 'test', '테스트', '제출하기 전까지 계속 테스트 돌려보는 것을 목표로 하기', 0, '2025-12-15', '2025-12-12 12:48:38');

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`num`),
  ADD UNIQUE KEY `userid` (`userid`);

--
-- 테이블의 인덱스 `member_tbl`
--
ALTER TABLE `member_tbl`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `memo_tbl`
--
ALTER TABLE `memo_tbl`
  ADD PRIMARY KEY (`iMemo`);

--
-- 테이블의 인덱스 `todo_tbl`
--
ALTER TABLE `todo_tbl`
  ADD PRIMARY KEY (`iTodo`);

--
-- 덤프된 테이블의 AUTO_INCREMENT
--

--
-- 테이블의 AUTO_INCREMENT `members`
--
ALTER TABLE `members`
  MODIFY `num` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 테이블의 AUTO_INCREMENT `member_tbl`
--
ALTER TABLE `member_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 테이블의 AUTO_INCREMENT `memo_tbl`
--
ALTER TABLE `memo_tbl`
  MODIFY `iMemo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 테이블의 AUTO_INCREMENT `todo_tbl`
--
ALTER TABLE `todo_tbl`
  MODIFY `iTodo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
