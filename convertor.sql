-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июл 22 2025 г., 20:24
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
(1, 'razemsb', '$2y$10$Q9E6ImS4ADniKgLe6Qny6.kaD3oeOPihJ7dVaARM3XIDIGBHIBNA6', '2025-07-21 20:29:23');

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
(39, '127.0.0.1', 'Снимок экрана 2025-07-21 212410.png', '687f8069cac25.webp', 'png', 'webp', 137734, 33274, 80, 'success', NULL, '2025-07-22 12:13:29'),
(40, '127.0.0.1', 'Снимок экрана 2025-07-22 160849.png', '687fc409f40c0.webp', 'png', 'webp', 27943, 14716, 80, 'success', NULL, '2025-07-22 17:02:02'),
(41, '::1', 'Снимок экрана 2025-07-22 205907.png', '687fd6ffa262a.webp', 'png', 'webp', 54853, 23218, 80, 'success', NULL, '2025-07-22 18:22:55'),
(42, '::1', 'Снимок экрана 2025-07-22 180624.png', '687fd6ffa6d68.webp', 'png', 'webp', 6342, 1972, 80, 'success', NULL, '2025-07-22 18:22:55'),
(43, '::1', 'converted-1753014825889.jpg', '687fd6ffaa3bc.webp', 'jpg', 'webp', 10832, 6442, 80, 'success', NULL, '2025-07-22 18:22:55'),
(44, '::1', '_.jpg', '687fd6ffb0dd8.webp', 'jpg', 'webp', 14236, 9906, 80, 'success', NULL, '2025-07-22 18:22:55'),
(45, '::1', 'Снимок экрана 2025-07-01 234718.png', '687fd6ffb302c.webp', 'png', 'webp', 31849, 5094, 80, 'success', NULL, '2025-07-22 18:22:55'),
(46, '::1', 'Снимок экрана 2025-07-20 151110.png', '687fd6ffb589d.webp', 'png', 'webp', 11770, 6260, 80, 'success', NULL, '2025-07-22 18:22:55'),
(47, '::1', 'Снимок экрана 2025-07-20 235240.png', '687fd6ffbaf1f.webp', 'png', 'webp', 14084, 13660, 80, 'success', NULL, '2025-07-22 18:22:55'),
(48, '::1', 'Снимок экрана 2025-07-22 123448.png', '687fd6ffbba36.webp', 'png', 'webp', 12611, 3260, 80, 'success', NULL, '2025-07-22 18:22:55'),
(49, '::1', 'converted-1753014950454.jpg', '687fd6ffad6ca.webp', 'jpg', 'webp', 59495, 32826, 80, 'success', NULL, '2025-07-22 18:22:55'),
(50, '::1', 'Снимок экрана 2025-07-22 160749.png', '687fd6ffc19b3.webp', 'png', 'webp', 13817, 5878, 80, 'success', NULL, '2025-07-22 18:22:55'),
(51, '::1', 'Снимок экрана 2025-07-02 201429.png', '687fd6ffc56ac.webp', 'png', 'webp', 5120, 1496, 80, 'success', NULL, '2025-07-22 18:22:55'),
(52, '::1', 'Снимок экрана 2025-07-02 001655.png', '687fd6ffc472e.webp', 'png', 'webp', 41393, 6688, 80, 'success', NULL, '2025-07-22 18:22:55'),
(53, '::1', 'Снимок экрана 2025-07-22 160849.png', '687fd6ffc5519.webp', 'png', 'webp', 27943, 14716, 80, 'success', NULL, '2025-07-22 18:22:55'),
(54, '::1', 'Снимок экрана 2025-07-05 154719.png', '687fd6ffc6efb.webp', 'png', 'webp', 32776, 4602, 80, 'success', NULL, '2025-07-22 18:22:55'),
(55, '::1', 'Снимок экрана 2025-07-06 145106.png', '687fd6ffc85b8.webp', 'png', 'webp', 3476, 1464, 80, 'success', NULL, '2025-07-22 18:22:55'),
(56, '::1', 'Снимок экрана 2025-07-07 025541.png', '687fd6ffc9122.webp', 'png', 'webp', 47225, 9662, 80, 'success', NULL, '2025-07-22 18:22:55'),
(57, '::1', 'Снимок экрана 2025-07-04 201051.png', '687fd6ffce934.webp', 'png', 'webp', 12552, 3362, 80, 'success', NULL, '2025-07-22 18:22:55'),
(58, '::1', '768768768768766876.jpg', '687fd6ffcc4bc.webp', 'jpg', 'webp', 40562, 30900, 80, 'success', NULL, '2025-07-22 18:22:55'),
(59, '::1', 'Снимок экрана 2025-07-20 182511.png', '687fd6ffcad2c.webp', 'png', 'webp', 13795, 8122, 80, 'success', NULL, '2025-07-22 18:22:55'),
(60, '::1', 'Снимок экрана 2025-07-05 143203.png', '687fd6ffca530.webp', 'png', 'webp', 40480, 9996, 80, 'success', NULL, '2025-07-22 18:22:55'),
(61, '::1', 'TkkX1f1P.jpg', '687fd6ffc002d.webp', 'jpg', 'webp', 37923, 19476, 80, 'success', NULL, '2025-07-22 18:22:55'),
(62, '::1', 'figma-psd-export-20250601_170831 1(1).png', '687fd6ffcec8e.webp', 'png', 'webp', 4194, 2012, 80, 'success', NULL, '2025-07-22 18:22:55'),
(63, '::1', 'photo_2025-05-23_10-15-25.jpg', '687fd6ffd57f0.webp', 'jpg', 'webp', 34067, 25248, 80, 'success', NULL, '2025-07-22 18:22:55'),
(64, '::1', 'figma-psd-export-20250601_170831 1.png', '687fd6ffd3cd5.webp', 'png', 'webp', 4194, 2012, 80, 'success', NULL, '2025-07-22 18:22:55'),
(65, '::1', 'photo_2024-02-15_19-06-00.jpg', '687fd6ffd8d19.webp', 'jpg', 'webp', 36247, 16876, 80, 'success', NULL, '2025-07-22 18:22:55'),
(66, '::1', 'photo_2025-06-13_00-18-27.jpg', '687fd6ffd7bd0.webp', 'jpg', 'webp', 51627, 15642, 80, 'success', NULL, '2025-07-22 18:22:55'),
(67, '::1', 'Снимок экрана 2025-07-02 021004.png', '687fd6ffe7d43.webp', 'png', 'webp', 85197, 5592, 80, 'success', NULL, '2025-07-22 18:22:55'),
(68, '::1', 'Снимок экрана 2025-07-05 142122.png', '687fd6ffef76d.webp', 'png', 'webp', 110767, 23258, 80, 'success', NULL, '2025-07-22 18:22:56'),
(69, '::1', 'photo_2024-10-31_22-06-45.jpg', '687fd6ffd87d5.webp', 'jpg', 'webp', 47829, 24214, 80, 'success', NULL, '2025-07-22 18:22:56'),
(70, '::1', 'photo_2025-06-06_19-21-46.jpg', '687fd6ffe7ec6.webp', 'jpg', 'webp', 63773, 77770, 80, 'success', NULL, '2025-07-22 18:22:56'),
(71, '::1', 'photo_2025-05-31_17-42-14.jpg', '687fd6ffe629f.webp', 'jpg', 'webp', 27658, 9876, 80, 'success', NULL, '2025-07-22 18:22:56'),
(72, '::1', 'photo_2025-06-01_18-01-47.jpg', '687fd6ffeb3b2.webp', 'jpg', 'webp', 28142, 7616, 80, 'success', NULL, '2025-07-22 18:22:56'),
(73, '::1', 'photo_2025-06-13_00-08-20.jpg', '687fd6ffef451.webp', 'jpg', 'webp', 51627, 15642, 80, 'success', NULL, '2025-07-22 18:22:56'),
(74, '::1', 'Снимок экрана 2025-07-22 160854.png', '687fd7000ad59.webp', 'png', 'webp', 119601, 43814, 80, 'success', NULL, '2025-07-22 18:22:56'),
(75, '::1', '7dSHaYMj.jpg', '687fd70010471.webp', 'jpg', 'webp', 71702, 42660, 80, 'success', NULL, '2025-07-22 18:22:56'),
(76, '::1', 'HglbmK_1.jpg', '687fd700128fc.webp', 'jpg', 'webp', 80512, 47444, 80, 'success', NULL, '2025-07-22 18:22:56'),
(77, '::1', 'Снимок экрана 2025-07-21 212410.png', '687fd70020983.webp', 'png', 'webp', 137734, 33274, 80, 'success', NULL, '2025-07-22 18:22:56'),
(78, '::1', '126364-1481205460.jpg', '687fd7000b922.webp', 'jpg', 'webp', 125970, 69340, 80, 'success', NULL, '2025-07-22 18:22:56'),
(79, '::1', 'photo_2024-11-22_09-09-40.jpg', '687fd7001dbcc.webp', 'jpg', 'webp', 98679, 38994, 80, 'success', NULL, '2025-07-22 18:22:56'),
(80, '::1', 'Снимок экрана 2025-07-05 142128.png', '687fd7002f72b.webp', 'png', 'webp', 183683, 40518, 80, 'success', NULL, '2025-07-22 18:22:56'),
(81, '::1', 'Снимок экрана 2025-07-22 145142.png', '687fd70017391.webp', 'png', 'webp', 187674, 61056, 80, 'success', NULL, '2025-07-22 18:22:56'),
(82, '::1', 'photo_2025-06-08_17-06-15.jpg', '687fd7002c08c.webp', 'jpg', 'webp', 97467, 48960, 80, 'success', NULL, '2025-07-22 18:22:56'),
(83, '::1', 'Снимок экрана 2025-07-02 021010.png', '687fd7004821f.webp', 'png', 'webp', 298535, 16746, 80, 'success', NULL, '2025-07-22 18:22:56'),
(84, '::1', 'photo_2025-06-14_16-26-28.jpg', '687fd7002e414.webp', 'jpg', 'webp', 112856, 77116, 80, 'success', NULL, '2025-07-22 18:22:56'),
(85, '::1', 'Снимок экрана 2025-07-18 231927.png', '687fd70044c4f.webp', 'png', 'webp', 303531, 32086, 80, 'success', NULL, '2025-07-22 18:22:56'),
(86, '::1', 'photo_2025-06-06_19-23-25.jpg', '687fd7003e0e5.webp', 'jpg', 'webp', 149149, 67786, 80, 'success', NULL, '2025-07-22 18:22:56'),
(87, '::1', 'Снимок экрана 2025-07-05 162320.png', '687fd7005d81b.webp', 'png', 'webp', 313867, 43114, 80, 'success', NULL, '2025-07-22 18:22:56'),
(88, '::1', 'photo_2025-06-26_21-28-06.jpg', '687fd70040719.webp', 'jpg', 'webp', 138090, 70666, 80, 'success', NULL, '2025-07-22 18:22:56'),
(89, '::1', 'Снимок экрана 2025-07-22 145156.png', '687fd70038c8e.webp', 'png', 'webp', 203501, 87222, 80, 'success', NULL, '2025-07-22 18:22:56'),
(90, '::1', '1024-1091892666.png', '687fd700583c8.webp', 'png', 'webp', 366827, 47504, 80, 'success', NULL, '2025-07-22 18:22:56'),
(91, '::1', '1024.png', '687fd7006127b.webp', 'png', 'webp', 479988, 45946, 80, 'success', NULL, '2025-07-22 18:22:56'),
(92, '::1', 'Снимок экрана 2025-07-02 031025.png', '687fd7007412c.webp', 'png', 'webp', 632930, 63154, 80, 'success', NULL, '2025-07-22 18:22:56'),
(93, '::1', 'kv_pd-421821700.png', '687fd700888a8.webp', 'png', 'webp', 1181843, 81114, 80, 'success', NULL, '2025-07-22 18:22:56'),
(94, '::1', 'Снимок экрана 2025-07-22 192715.png', '687fd70078391.webp', 'png', 'webp', 967836, 100848, 80, 'success', NULL, '2025-07-22 18:22:56'),
(95, '::1', 'Снимок экрана 2025-07-06 150131.png', '687fd7007ace9.webp', 'png', 'webp', 829160, 84880, 80, 'success', NULL, '2025-07-22 18:22:56'),
(96, '::1', 'идея не моя.jpg', '687fd700a39d5.webp', 'jpg', 'webp', 46259, 32792, 80, 'success', NULL, '2025-07-22 18:22:56'),
(97, '::1', 'Снимок экрана 2025-07-02 014847.png', '687fd7008fabe.webp', 'png', 'webp', 1129771, 66836, 80, 'success', NULL, '2025-07-22 18:22:56'),
(98, '::1', 'Китайские мудрости.jpg', '687fd700aa27d.webp', 'jpg', 'webp', 31286, 23962, 80, 'success', NULL, '2025-07-22 18:22:56'),
(99, '::1', 'Снимок экрана 2025-06-15 171356.png', '687fd700b2af5.webp', 'png', 'webp', 7205, 3702, 80, 'success', NULL, '2025-07-22 18:22:56'),
(100, '::1', '1056960_14.jpg', '687fd7006dc3a.webp', 'jpg', 'webp', 653852, 366056, 80, 'success', NULL, '2025-07-22 18:22:56'),
(101, '::1', 'Снимок экрана 2025-06-15 211143.png', '687fd700b4f0f.webp', 'png', 'webp', 118471, 11520, 80, 'success', NULL, '2025-07-22 18:22:56'),
(102, '::1', 'Снимок экрана 2025-06-16 192947.png', '687fd700c12c0.webp', 'png', 'webp', 2071, 1098, 80, 'success', NULL, '2025-07-22 18:22:56'),
(103, '::1', 'Снимок экрана 2025-06-16 055047.png', '687fd700bb3f3.webp', 'png', 'webp', 44495, 23510, 80, 'success', NULL, '2025-07-22 18:22:56'),
(104, '::1', 'Снимок экрана 2025-06-17 203958.png', '687fd700c6673.webp', 'png', 'webp', 6260, 3510, 80, 'success', NULL, '2025-07-22 18:22:56'),
(105, '::1', 'Снимок экрана 2025-07-18 231903.png', '687fd7009b17e.webp', 'png', 'webp', 1531249, 104750, 80, 'success', NULL, '2025-07-22 18:22:56'),
(106, '::1', 'Снимок экрана 2025-06-18 203221.png', '687fd700d0fb5.webp', 'png', 'webp', 19106, 1460, 80, 'success', NULL, '2025-07-22 18:22:56'),
(107, '::1', 'Снимок экрана 2025-06-18 203915.png', '687fd700d8ae0.webp', 'png', 'webp', 10730, 5018, 80, 'success', NULL, '2025-07-22 18:22:56'),
(108, '::1', 'Снимок экрана 2025-06-15 134608.png', '687fd700b9a73.webp', 'png', 'webp', 508487, 69748, 80, 'success', NULL, '2025-07-22 18:22:56'),
(109, '::1', 'Снимок экрана 2025-06-19 205428.png', '687fd700e3550.webp', 'png', 'webp', 28131, 11198, 80, 'success', NULL, '2025-07-22 18:22:56'),
(110, '::1', 'Снимок экрана 2025-06-19 211235.png', '687fd700edc0f.webp', 'png', 'webp', 21054, 15668, 80, 'success', NULL, '2025-07-22 18:22:56'),
(111, '::1', 'Снимок экрана 2025-06-19 210551.png', '687fd700ee000.webp', 'png', 'webp', 24840, 14670, 80, 'success', NULL, '2025-07-22 18:22:57'),
(112, '::1', 'Снимок экрана 2025-06-19 213229.png', '687fd700f400a.webp', 'png', 'webp', 6400, 2826, 80, 'success', NULL, '2025-07-22 18:22:57'),
(113, '::1', 'Скриншот сделанный 2025-06-02 в 03.13.52.png', '687fd700b8e90.webp', 'png', 'webp', 345047, 63072, 80, 'success', NULL, '2025-07-22 18:22:57'),
(114, '::1', 'Снимок экрана 2025-06-16 181823.png', '687fd700cc80a.webp', 'png', 'webp', 1509277, 163450, 80, 'success', NULL, '2025-07-22 18:22:57'),
(115, '::1', 'Снимок экрана 2025-06-17 213742.png', '687fd700ce824.webp', 'png', 'webp', 325686, 77516, 80, 'success', NULL, '2025-07-22 18:22:57'),
(116, '::1', 'Снимок экрана 2025-06-19 220245.png', '687fd70107954.webp', 'png', 'webp', 108078, 16462, 80, 'success', NULL, '2025-07-22 18:22:57'),
(117, '::1', 'Снимок экрана 2025-06-19 213729.png', '687fd701034a6.webp', 'png', 'webp', 26652, 7862, 80, 'success', NULL, '2025-07-22 18:22:57'),
(118, '::1', 'Снимок экрана 2025-06-18 004359.png', '687fd700d4cc4.webp', 'png', 'webp', 1315195, 95984, 80, 'success', NULL, '2025-07-22 18:22:57'),
(119, '::1', 'Снимок экрана 2025-06-20 123156.png', '687fd7010b491.webp', 'png', 'webp', 2559, 1448, 80, 'success', NULL, '2025-07-22 18:22:57'),
(120, '::1', 'Снимок экрана 2025-06-20 133816.png', '687fd70111766.webp', 'png', 'webp', 4782, 3536, 80, 'success', NULL, '2025-07-22 18:22:57'),
(121, '::1', 'Снимок экрана 2025-06-19 220249.png', '687fd7010c219.webp', 'png', 'webp', 107652, 16558, 80, 'success', NULL, '2025-07-22 18:22:57'),
(122, '::1', 'Снимок экрана 2025-06-22 202821.png', '687fd70113eac.webp', 'png', 'webp', 36315, 4330, 80, 'success', NULL, '2025-07-22 18:22:57'),
(123, '::1', 'Снимок экрана 2025-06-20 133036.png', '687fd7010f93f.webp', 'png', 'webp', 131605, 16324, 80, 'success', NULL, '2025-07-22 18:22:57'),
(124, '::1', 'Снимок экрана 2025-06-23 180326.png', '687fd70115e7b.webp', 'png', 'webp', 208273, 18308, 80, 'success', NULL, '2025-07-22 18:22:57'),
(125, '::1', 'Снимок экрана 2025-06-19 214558.png', '687fd7010c52a.webp', 'png', 'webp', 156819, 60184, 80, 'success', NULL, '2025-07-22 18:22:57'),
(126, '::1', 'Снимок экрана 2025-06-20 131952.png', '687fd701142d3.webp', 'png', 'webp', 895587, 115740, 80, 'success', NULL, '2025-07-22 18:22:57'),
(127, '::1', 'Снимок экрана 2025-06-24 174633.png', '687fd70126da6.webp', 'png', 'webp', 1522559, 76324, 80, 'success', NULL, '2025-07-22 18:22:57'),
(128, '::1', 'Снимок экрана 2025-06-25 221028.png', '687fd701604ad.webp', 'png', 'webp', 1167, 360, 80, 'success', NULL, '2025-07-22 18:22:57'),
(129, '::1', 'Снимок экрана 2025-06-17 124719.png', '687fd701330b7.webp', 'png', 'webp', 3539388, 349882, 80, 'success', NULL, '2025-07-22 18:22:57'),
(130, '::1', 'Снимок экрана 2025-06-19 124541.png', '687fd7014fd12.webp', 'png', 'webp', 2384860, 190882, 80, 'success', NULL, '2025-07-22 18:22:57'),
(131, '::1', 'Снимок экрана 2025-06-26 193139.png', '687fd70190e52.webp', 'png', 'webp', 6994, 1856, 80, 'success', NULL, '2025-07-22 18:22:57'),
(132, '::1', 'Снимок экрана 2025-06-26 205131.png', '687fd701985cd.webp', 'png', 'webp', 22544, 4836, 80, 'success', NULL, '2025-07-22 18:22:57'),
(133, '::1', 'Снимок экрана 2025-06-26 183053.png', '687fd70183d13.webp', 'png', 'webp', 179166, 44340, 80, 'success', NULL, '2025-07-22 18:22:57'),
(134, '::1', 'Снимок экрана 2025-06-26 205142.png', '687fd701a0264.webp', 'png', 'webp', 19263, 4274, 80, 'success', NULL, '2025-07-22 18:22:57'),
(135, '::1', 'Снимок экрана 2025-06-26 123418.png', '687fd7016ef68.webp', 'png', 'webp', 845385, 99194, 80, 'success', NULL, '2025-07-22 18:22:57'),
(136, '::1', 'Снимок экрана 2025-06-27 094658.png', '687fd701a7bdd.webp', 'png', 'webp', 37739, 7132, 80, 'success', NULL, '2025-07-22 18:22:57'),
(137, '::1', 'Снимок экрана 2025-06-27 100512.png', '687fd701b81f1.webp', 'png', 'webp', 1690528, 153690, 80, 'success', NULL, '2025-07-22 18:22:57'),
(138, '::1', 'Снимок экрана 2025-06-23 225153.png', '687fd701d491f.webp', 'png', 'webp', 2428241, 118180, 80, 'success', NULL, '2025-07-22 18:22:58'),
(139, '::1', 'vecteezy_male-3d-avatar_27245504.png', '687fd70157003.webp', 'png', 'webp', 3148289, 132684, 80, 'success', NULL, '2025-07-22 18:22:58'),
(140, '::1', 'Снимок экрана 2025-06-24 185217.png', '687fd702ab0fa.webp', 'png', 'webp', 2872098, 179622, 80, 'success', NULL, '2025-07-22 18:22:58'),
(141, '::1', 'rei-ayanami-3840x2160-19009.jpg', '687fd7033554a.webp', 'jpg', 'webp', 6953349, 2026266, 80, 'success', NULL, '2025-07-22 18:23:00'),
(142, '::1', 'vecteezy_extraordinary-contemporary-geometric-portrait-abstract_59918198.png', '687fd70164628.webp', 'png', 'webp', 4008460, 275854, 80, 'success', NULL, '2025-07-22 18:23:00'),
(143, '::1', 'vecteezy_vibrant-rustic-man-with-a-determined-look-4k_59919419.png', '687fd70195842.webp', 'png', 'webp', 4336450, 254016, 80, 'success', NULL, '2025-07-22 18:23:01'),
(144, '::1', 'vecteezy_smiling-3d-boy-character-render-with-brown-hair-and-blue-shirt_58665248.png', '687fd7022f444.webp', 'png', 'webp', 4202395, 253484, 80, 'success', NULL, '2025-07-22 18:23:01'),
(145, '::1', 'vecteezy_stunning-artistic-serious-young-man-in-casual-wear-exclusive_59926037.png', '687fd702a39f8.webp', 'png', 'webp', 7105627, 442950, 80, 'success', NULL, '2025-07-22 18:23:02'),
(146, '::1', 'vecteezy_ai-generative-cartoon-portrait-of-a-person-on-transparent_33501245.png', '687fd70294882.webp', 'png', 'webp', 6607019, 345006, 80, 'success', NULL, '2025-07-22 18:23:02');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(80) NOT NULL,
  `is_admin` enum('user','moderator','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `avatar`, `is_admin`, `created_at`, `updated_at`) VALUES
(1, 'razemsb', '$2y$10$0zsUFUECnD4KUbzRu9Mxp.LlYITSqGXTtLsaAM/PNCB5frJcUp7le', 'default-avatar-1.png', 'admin', '2025-07-22 15:16:02', '2025-07-22 18:10:25'),
(2, 'test', '$2y$10$HidI7A2y.rZp4GGaWlqIVeqDQHlY3S4EX.cNfvPq1NuVHcxBzm4SW', 'default-avatar-1.png', 'user', '2025-07-22 18:18:58', '2025-07-22 18:18:58'),
(3, 'cyufkhjggh', '$2y$10$Ctp.f9krpdNhY/ckVPwz1eUxvluaZ2GI8H9tBWvnCDp1Y4hlFog2W', 'default-avatar-4.png', 'user', '2025-07-22 18:19:36', '2025-07-22 18:19:36');

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
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
