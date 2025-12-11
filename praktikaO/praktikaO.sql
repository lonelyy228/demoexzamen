-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Дек 11 2025 г., 11:02
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `praktikaO`
--

-- --------------------------------------------------------

--
-- Структура таблицы `pay_type`
--

CREATE TABLE `pay_type` (
  `id_pay_type` int(1) NOT NULL,
  `name_pay` varchar(17) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `pay_type`
--

INSERT INTO `pay_type` (`id_pay_type`, `name_pay`) VALUES
(1, 'наличные'),
(2, 'банковской картой');

-- --------------------------------------------------------

--
-- Структура таблицы `practice_reports`
--

CREATE TABLE `practice_reports` (
  `id_report` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `fio` varchar(255) NOT NULL,
  `group_name` varchar(50) NOT NULL,
  `specialty` varchar(255) NOT NULL,
  `practice_start_date` date NOT NULL,
  `practice_end_date` date NOT NULL,
  `organization_name` varchar(255) NOT NULL,
  `organization_address` text NOT NULL,
  `supervisor_info` text NOT NULL,
  `work_description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `teacher_comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `practice_reports`
--

INSERT INTO `practice_reports` (`id_report`, `student_id`, `fio`, `group_name`, `specialty`, `practice_start_date`, `practice_end_date`, `organization_name`, `organization_address`, `supervisor_info`, `work_description`, `created_at`, `status`, `teacher_comment`) VALUES
(1, 7, 'test test 2', 'isv', 'isv23', '2025-12-10', '2025-12-14', 'додо пицца', 'дзержинка', 'Юлия Витальевна', 'ну я работал работал и все', '2025-12-11 09:30:19', 'pending', NULL),
(2, 7, 'test test 2', 'isv', 'веб систем ы', '2025-12-24', '2026-01-11', 'ай4', 'п5524р2рй26р', 'Юлия Витальевна', '351265р4йро624йо62йой', '2025-12-11 09:31:08', 'rejected', 'денис даун'),
(3, 6, 'test test test', 'isv', 'веб системы', '2025-12-01', '2025-12-31', 'дворник', 'дзержинка', 'Юлия Витальевна', 'мать дениса красивая, а мать артема еще красивее', '2025-12-11 09:35:43', 'approved', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `service`
--

CREATE TABLE `service` (
  `id_service` int(1) NOT NULL,
  `address` varchar(50) NOT NULL,
  `user_id` int(1) NOT NULL,
  `service_type_id` int(1) NOT NULL,
  `data` date NOT NULL,
  `time` time(6) NOT NULL,
  `pay_type_id` int(1) NOT NULL,
  `status_id` int(1) NOT NULL,
  `reason_cancel` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `service_type`
--

CREATE TABLE `service_type` (
  `id_service_type` int(1) NOT NULL,
  `name_service` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `service_type`
--

INSERT INTO `service_type` (`id_service_type`, `name_service`) VALUES
(1, 'общий клининг'),
(2, 'генеральная уборка'),
(3, 'послестроительная уборка'),
(4, 'химчистка ковров и мебели');

-- --------------------------------------------------------

--
-- Структура таблицы `status`
--

CREATE TABLE `status` (
  `id_status` int(1) NOT NULL,
  `name_status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `status`
--

INSERT INTO `status` (`id_status`, `name_status`) VALUES
(1, 'На проверке'),
(2, 'Принято'),
(3, 'На доработку');

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id_user` int(1) NOT NULL,
  `user_type_id` int(1) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `otchestvo` varchar(255) NOT NULL,
  `gruppa` varchar(255) NOT NULL,
  `number` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id_user`, `user_type_id`, `surname`, `name`, `otchestvo`, `gruppa`, `number`, `email`, `username`, `password`) VALUES
(1, 2, 'Шаимов', 'Расул', 'Хасанович', '0', 'айфон 13', 'rs9985228@gmail.com', 'teacher', '2c8ade1dca7c5fa01cbceaf1e6bd654b'),
(3, 1, '3', '3', '3', '3', NULL, '3@3', '3', '3'),
(6, 1, 'test', 'test', 'test', 'isv', '12345', 'test@test', '1', 'c4ca4238a0b923820dcc509a6f75849b'),
(7, 1, 'test', 'test', '2', 'isv', '12345', 'rs9985228@gmail.com', '4', 'a87ff679a2f3e71d9181a67b7542122c'),
(8, 1, '6', '6', '6', '6', '6', 'a@a.ed', '6', '1679091c5a880faf6fb5e6087eb1b2dc');

-- --------------------------------------------------------

--
-- Структура таблицы `user_type`
--

CREATE TABLE `user_type` (
  `id_user_type` int(1) NOT NULL,
  `name_user` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `user_type`
--

INSERT INTO `user_type` (`id_user_type`, `name_user`) VALUES
(1, 'пользователь'),
(2, 'админ');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `pay_type`
--
ALTER TABLE `pay_type`
  ADD PRIMARY KEY (`id_pay_type`);

--
-- Индексы таблицы `practice_reports`
--
ALTER TABLE `practice_reports`
  ADD PRIMARY KEY (`id_report`),
  ADD KEY `student_id` (`student_id`);

--
-- Индексы таблицы `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id_service`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_type_id` (`service_type_id`),
  ADD KEY `pay_type_id` (`pay_type_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Индексы таблицы `service_type`
--
ALTER TABLE `service_type`
  ADD PRIMARY KEY (`id_service_type`);

--
-- Индексы таблицы `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id_status`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `user_type_id` (`user_type_id`);

--
-- Индексы таблицы `user_type`
--
ALTER TABLE `user_type`
  ADD PRIMARY KEY (`id_user_type`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `practice_reports`
--
ALTER TABLE `practice_reports`
  MODIFY `id_report` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `service`
--
ALTER TABLE `service`
  MODIFY `id_service` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `practice_reports`
--
ALTER TABLE `practice_reports`
  ADD CONSTRAINT `practice_reports_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `service_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `service_ibfk_2` FOREIGN KEY (`pay_type_id`) REFERENCES `pay_type` (`id_pay_type`),
  ADD CONSTRAINT `service_ibfk_3` FOREIGN KEY (`service_type_id`) REFERENCES `service_type` (`id_service_type`),
  ADD CONSTRAINT `service_ibfk_4` FOREIGN KEY (`status_id`) REFERENCES `status` (`id_status`);

--
-- Ограничения внешнего ключа таблицы `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`user_type_id`) REFERENCES `user_type` (`id_user_type`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
