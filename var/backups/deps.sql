-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Апр 17 2022 г., 08:35
-- Версия сервера: 10.7.3-MariaDB
-- Версия PHP: 7.4.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `cscart`
--

-- --------------------------------------------------------

--
-- Структура таблицы `cscart_departments`
--

CREATE TABLE `cscart_departments` (
  `department_id` int(11) UNSIGNED NOT NULL,
  `position` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `timestamp` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `upd_timestamp` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `cscart_departments`
--

INSERT INTO `cscart_departments` (`department_id`, `position`, `status`, `timestamp`, `upd_timestamp`, `user_id`) VALUES
(1, 3, 'A', 1649883600, 0, 4),
(5, 5, 'A', 1649970000, 0, 4),
(6, 11, 'A', 1649365200, 0, 4);

-- --------------------------------------------------------

--
-- Структура таблицы `cscart_department_description`
--

CREATE TABLE `cscart_department_description` (
  `department_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `lang_code` char(2) NOT NULL DEFAULT '',
  `department` varchar(255) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `cscart_department_description`
--

INSERT INTO `cscart_department_description` (`department_id`, `lang_code`, `department`, `description`) VALUES
(0, 'ru', '', NULL),
(1, 'ru', 'first dep', ''),
(5, 'ru', 'one more dep', '<p><strong>Fast </strong>and <b>furious</b></p>'),
(6, 'ru', 'best dep', '');

-- --------------------------------------------------------

--
-- Структура таблицы `cscart_department_links`
--

CREATE TABLE `cscart_department_links` (
  `department_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `member_user_id` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `cscart_department_links`
--

INSERT INTO `cscart_department_links` (`department_id`, `member_user_id`) VALUES
(1, 3),
(1, 5),
(5, 3),
(5, 4),
(5, 5),
(6, 3),
(6, 5);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `cscart_departments`
--
ALTER TABLE `cscart_departments`
  ADD PRIMARY KEY (`department_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `cscart_department_description`
--
ALTER TABLE `cscart_department_description`
  ADD PRIMARY KEY (`department_id`,`lang_code`);

--
-- Индексы таблицы `cscart_department_links`
--
ALTER TABLE `cscart_department_links`
  ADD PRIMARY KEY (`department_id`,`member_user_id`),
  ADD KEY `member_user_id` (`member_user_id`),
  ADD KEY `department_id` (`department_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `cscart_departments`
--
ALTER TABLE `cscart_departments`
  MODIFY `department_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;