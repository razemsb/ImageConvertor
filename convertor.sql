-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июл 26 2025 г., 00:58
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
  `user_id` int(5) DEFAULT NULL COMMENT 'ID Пользователя',
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

INSERT INTO `conversions` (`id`, `ip`, `user_id`, `original_name`, `new_name`, `original_format`, `new_format`, `original_size`, `new_size`, `quality`, `status`, `error_message`, `created_at`) VALUES
(1, '::1', 1, 'Снимок экрана 2025-07-24 205722.png', '688404e64d770.webp', 'png', 'webp', 94701, 57248, 80, 'success', NULL, '2025-07-25 22:27:50'),
(2, '::1', NULL, 'Снимок экрана 2025-07-24 205722.png', '688406ab7076f.webp', 'png', 'webp', 94701, 57248, 80, 'success', NULL, '2025-07-25 22:35:23'),
(3, '::1', NULL, 'Снимок экрана 2025-07-25 235304.png', '688406d479c63.webp', 'png', 'webp', 57178, 31642, 80, 'success', NULL, '2025-07-25 22:36:04'),
(4, '::1', NULL, 'Поздоровался.jpg', '688406fa5f93f.webp', 'jpg', 'webp', 38477, 24700, 80, 'success', NULL, '2025-07-25 22:36:42'),
(5, '::1', NULL, 'Снимок экрана 2025-07-25 235304.png', '6884071607dcf.webp', 'png', 'webp', 57178, 31642, 80, 'success', NULL, '2025-07-25 22:37:10'),
(6, '::1', NULL, 'Снимок экрана 2025-07-25 235304.png', '688407fca5102.webp', 'png', 'webp', 57178, 31642, 80, 'success', NULL, '2025-07-25 22:41:00'),
(7, '::1', NULL, 'Снимок экрана 2025-07-25 235304.png', '68840836b3da4.webp', 'png', 'webp', 57178, 31642, 80, 'success', NULL, '2025-07-25 22:41:58'),
(8, '::1', NULL, 'Снимок экрана 2025-07-25 235304.png', '68840898cd0db.webp', 'png', 'webp', 57178, 31642, 80, 'success', NULL, '2025-07-25 22:43:36'),
(9, '::1', NULL, 'Снимок экрана 2025-07-25 235304.png', '68840915c4402.webp', 'png', 'webp', 57178, 31642, 80, 'success', NULL, '2025-07-25 22:45:41'),
(10, '::1', 1, 'Снимок экрана 2025-07-25 235304.png', '68840937b54e8.webp', 'png', 'webp', 57178, 31642, 80, 'success', NULL, '2025-07-25 22:46:15'),
(11, '::1', 1, 'Снимок экрана 2025-07-25 235304.png', '68840bcc7a453.webp', 'png', 'webp', 57178, 31642, 80, 'success', NULL, '2025-07-25 22:57:16'),
(12, '::1', 1, 'Снимок экрана 2025-07-24 205722.png', '68840bd05a59f.webp', 'png', 'webp', 94701, 57248, 80, 'success', NULL, '2025-07-25 22:57:20'),
(13, '::1', 1, 'Снимок экрана 2025-07-24 205722.png', '68840bd45c99b.jpeg', 'png', 'jpeg', 94701, 101643, 80, 'success', NULL, '2025-07-25 22:57:24'),
(14, '::1', 1, 'Снимок экрана 2025-07-25 235304.png', '68840bd574fc0.jpeg', 'png', 'jpeg', 57178, 57476, 80, 'success', NULL, '2025-07-25 22:57:25'),
(15, '::1', 1, 'Снимок экрана 2025-07-24 205722.png', '68840bd6970cc.jpeg', 'png', 'jpeg', 94701, 101643, 80, 'success', NULL, '2025-07-25 22:57:26'),
(16, '::1', 1, 'Снимок экрана 2025-07-25 235304.png', '68840bd8ac40d.jpeg', 'png', 'jpeg', 57178, 57476, 80, 'success', NULL, '2025-07-25 22:57:28'),
(17, '::1', 1, 'Снимок экрана 2025-07-24 205722.png', '68840bda680c5.png', 'png', 'png', 94701, 112769, 80, 'success', NULL, '2025-07-25 22:57:30'),
(18, '::1', 1, 'Снимок экрана 2025-07-25 235304.png', '68840bdc33afc.png', 'png', 'png', 57178, 65847, 80, 'success', NULL, '2025-07-25 22:57:32'),
(19, '::1', 1, 'Снимок экрана 2025-07-24 205722.png', '68840bdd57afe.png', 'png', 'png', 94701, 112769, 80, 'success', NULL, '2025-07-25 22:57:33'),
(20, '::1', 1, 'Снимок экрана 2025-07-25 235304.png', '68840be0354ae.png', 'png', 'png', 57178, 65847, 80, 'success', NULL, '2025-07-25 22:57:36'),
(21, '::1', 1, 'Снимок экрана 2025-07-24 205722.png', '68840be133439.png', 'png', 'png', 94701, 112769, 80, 'success', NULL, '2025-07-25 22:57:37'),
(22, '::1', 1, 'Снимок экрана 2025-07-26 004921.png', '68840be3d4325.png', 'png', 'png', 291601, 278129, 80, 'success', NULL, '2025-07-25 22:57:39');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
