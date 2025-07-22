-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июл 22 2025 г., 14:14
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
-- База данных: `convertor`
--

-- --------------------------------------------------------

--
-- Структура таблицы `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `admins`
--

INSERT INTO `admins` (`id`, `login`, `password_hash`, `created_at`) VALUES
(1, 'razemsb', '$2y$10$yAkwhwYcnx3IytgWXik/VOSRNhA11LJDibKb0CP6UaNF5smtlp1p6', '2025-07-21 20:29:23');

-- --------------------------------------------------------

--
-- Структура таблицы `conversions`
--

CREATE TABLE `conversions` (
  `id` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL COMMENT 'IP пользователя',
  `original_name` varchar(255) NOT NULL COMMENT 'Исходное имя файла',
  `new_name` varchar(255) DEFAULT NULL COMMENT 'Новое имя файла (если успех)',
  `original_format` varchar(10) NOT NULL COMMENT 'Исходный формат (jpg/png/gif)',
  `new_format` varchar(10) DEFAULT NULL COMMENT 'Целевой формат (webp/avif/etc)',
  `original_size` int(11) DEFAULT NULL COMMENT 'Размер исходного файла (в байтах)',
  `new_size` int(11) DEFAULT NULL COMMENT 'Размер нового файла (в байтах)',
  `quality` tinyint(4) DEFAULT NULL COMMENT 'Качество (1-100)',
  `status` enum('success','error') NOT NULL COMMENT 'Успех или ошибка',
  `error_message` text DEFAULT NULL COMMENT 'Текст ошибки (если status=error)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Дата и время операции'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Логи конвертаций изображений';

--
-- Дамп данных таблицы `conversions`
--

INSERT INTO `conversions` (`id`, `ip`, `original_name`, `new_name`, `original_format`, `new_format`, `original_size`, `new_size`, `quality`, `status`, `error_message`, `created_at`) VALUES
(1, '127.0.0.1', 'TkkX1f1P.jpg', '687f73494494f.webp', 'jpg', 'webp', 37923, 19476, 80, 'success', NULL, '2025-07-22 11:17:29'),
(2, '127.0.0.1', 'HglbmK_1.jpg', '687f7675c4b80.avif', 'jpg', 'avif', 80512, 58979, 80, 'success', NULL, '2025-07-22 11:31:03'),
(3, '127.0.0.1', '1056960_14.jpg', '687f767db003e.avif', 'jpg', 'avif', 653852, 456049, 80, 'success', NULL, '2025-07-22 11:31:13'),
(4, '127.0.0.1', 'Снимок экрана 2025-07-04 201051.png', '687f768587c2b.png', 'png', 'png', 12552, 12662, 80, 'success', NULL, '2025-07-22 11:31:17'),
(5, '127.0.0.1', 'Снимок экрана 2025-07-18 231927.png', '687f76890a672.png', 'png', 'png', 303531, 349700, 80, 'success', NULL, '2025-07-22 11:31:21'),
(6, '127.0.0.1', 'converted-1753014825889.jpg', '687f768f2a0e8.webp', 'jpg', 'webp', 10832, 6442, 80, 'success', NULL, '2025-07-22 11:31:27'),
(7, '127.0.0.1', 'Снимок экрана 2025-07-20 235240.png', '687f768f4bde2.webp', 'png', 'webp', 14084, 13660, 80, 'success', NULL, '2025-07-22 11:31:27'),
(8, '127.0.0.1', 'Снимок экрана 2025-07-20 182511.png', '687f768f4c6ec.webp', 'png', 'webp', 13795, 8122, 80, 'success', NULL, '2025-07-22 11:31:27'),
(9, '127.0.0.1', 'Снимок экрана 2025-07-20 151110.png', '687f768f4c356.webp', 'png', 'webp', 11770, 6260, 80, 'success', NULL, '2025-07-22 11:31:27'),
(10, '127.0.0.1', 'converted-1753014950454.jpg', '687f768f47e22.webp', 'jpg', 'webp', 59495, 32826, 80, 'success', NULL, '2025-07-22 11:31:27'),
(11, '127.0.0.1', 'Снимок экрана 2025-07-18 231927.png', '687f768f4e420.webp', 'png', 'webp', 303531, 32086, 80, 'success', NULL, '2025-07-22 11:31:27'),
(12, '127.0.0.1', 'Снимок экрана 2025-07-18 231903.png', '687f768f5e361.webp', 'png', 'webp', 1531249, 104750, 80, 'success', NULL, '2025-07-22 11:31:27'),
(13, '127.0.0.1', 'converted-1753014825889.jpg', '687f7b81ab629.webp', 'jpg', 'webp', 10832, 6442, 80, 'success', NULL, '2025-07-22 11:52:33'),
(14, '127.0.0.1', 'Снимок экрана 2025-07-20 151110.png', '687f7b81ad821.webp', 'png', 'webp', 11770, 6260, 80, 'success', NULL, '2025-07-22 11:52:33'),
(15, '127.0.0.1', 'Снимок экрана 2025-07-20 235240.png', '687f7b81add1e.webp', 'png', 'webp', 14084, 13660, 80, 'success', NULL, '2025-07-22 11:52:33'),
(16, '127.0.0.1', 'Снимок экрана 2025-07-20 182511.png', '687f7b81adc86.webp', 'png', 'webp', 13795, 8122, 80, 'success', NULL, '2025-07-22 11:52:33'),
(17, '127.0.0.1', '_.jpg', '687f7b81aeb62.webp', 'jpg', 'webp', 14236, 9906, 80, 'success', NULL, '2025-07-22 11:52:33'),
(18, '127.0.0.1', 'Снимок экрана 2025-07-02 201429.png', '687f7b81b66c5.webp', 'png', 'webp', 5120, 1496, 80, 'success', NULL, '2025-07-22 11:52:33'),
(19, '127.0.0.1', 'Снимок экрана 2025-07-01 234718.png', '687f7b81b621f.webp', 'png', 'webp', 31849, 5094, 80, 'success', NULL, '2025-07-22 11:52:33'),
(20, '127.0.0.1', 'Снимок экрана 2025-07-02 001655.png', '687f7b81b63a1.webp', 'png', 'webp', 41393, 6688, 80, 'success', NULL, '2025-07-22 11:52:33'),
(21, '127.0.0.1', 'Снимок экрана 2025-07-04 201051.png', '687f7b81bc94e.webp', 'png', 'webp', 12552, 3362, 80, 'success', NULL, '2025-07-22 11:52:33'),
(22, '127.0.0.1', 'Снимок экрана 2025-07-02 021004.png', '687f7b81bcf29.webp', 'png', 'webp', 85197, 5592, 80, 'success', NULL, '2025-07-22 11:52:33'),
(23, '127.0.0.1', 'converted-1753014950454.jpg', '687f7b81aee9b.webp', 'jpg', 'webp', 59495, 32826, 80, 'success', NULL, '2025-07-22 11:52:33'),
(24, '127.0.0.1', 'Снимок экрана 2025-07-05 143203.png', '687f7b81beb9d.webp', 'png', 'webp', 40480, 9996, 80, 'success', NULL, '2025-07-22 11:52:33'),
(25, '127.0.0.1', 'TkkX1f1P.jpg', '687f7b81b628a.webp', 'jpg', 'webp', 37923, 19476, 80, 'success', NULL, '2025-07-22 11:52:33'),
(26, '127.0.0.1', 'Снимок экрана 2025-07-05 142122.png', '687f7b81c3d61.webp', 'png', 'webp', 110767, 23258, 80, 'success', NULL, '2025-07-22 11:52:33'),
(27, '127.0.0.1', 'Снимок экрана 2025-07-06 145106.png', '687f7b81caf6f.webp', 'png', 'webp', 3476, 1464, 80, 'success', NULL, '2025-07-22 11:52:33'),
(28, '127.0.0.1', 'Снимок экрана 2025-07-05 154719.png', '687f7b81cb5a8.webp', 'png', 'webp', 32776, 4602, 80, 'success', NULL, '2025-07-22 11:52:33'),
(29, '127.0.0.1', 'Снимок экрана 2025-07-05 142128.png', '687f7b81c8325.webp', 'png', 'webp', 183683, 40518, 80, 'success', NULL, '2025-07-22 11:52:33'),
(30, '127.0.0.1', 'Снимок экрана 2025-07-07 025541.png', '687f7b81d0b8b.webp', 'png', 'webp', 47225, 9662, 80, 'success', NULL, '2025-07-22 11:52:33'),
(31, '127.0.0.1', 'Снимок экрана 2025-07-02 021010.png', '687f7b81cf63f.webp', 'png', 'webp', 298535, 16746, 80, 'success', NULL, '2025-07-22 11:52:33'),
(32, '127.0.0.1', 'HglbmK_1.jpg', '687f7b81ba991.webp', 'jpg', 'webp', 80512, 47444, 80, 'success', NULL, '2025-07-22 11:52:33'),
(33, '127.0.0.1', 'Снимок экрана 2025-07-18 231927.png', '687f7b81c74b6.webp', 'png', 'webp', 303531, 32086, 80, 'success', NULL, '2025-07-22 11:52:33'),
(34, '127.0.0.1', 'Снимок экрана 2025-07-05 162320.png', '687f7b81d5537.webp', 'png', 'webp', 313867, 43114, 80, 'success', NULL, '2025-07-22 11:52:33'),
(35, '127.0.0.1', 'Снимок экрана 2025-07-02 031025.png', '687f7b81dc553.webp', 'png', 'webp', 632930, 63154, 80, 'success', NULL, '2025-07-22 11:52:34'),
(36, '127.0.0.1', 'Снимок экрана 2025-07-02 014847.png', '687f7b81e66d8.webp', 'png', 'webp', 1129771, 66836, 80, 'success', NULL, '2025-07-22 11:52:34'),
(37, '127.0.0.1', 'Снимок экрана 2025-07-18 231903.png', '687f7b81ecc40.webp', 'png', 'webp', 1531249, 104750, 80, 'success', NULL, '2025-07-22 11:52:34'),
(38, '127.0.0.1', 'Снимок экрана 2025-07-06 150131.png', '687f7b81e328a.webp', 'png', 'webp', 829160, 84880, 80, 'success', NULL, '2025-07-22 11:52:34'),
(39, '127.0.0.1', 'Снимок экрана 2025-07-21 212410.png', '687f8069cac25.webp', 'png', 'webp', 137734, 33274, 80, 'success', NULL, '2025-07-22 12:13:29');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Индексы таблицы `conversions`
--
ALTER TABLE `conversions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_ip` (`ip`),
  ADD KEY `idx_date` (`created_at`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `conversions`
--
ALTER TABLE `conversions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
