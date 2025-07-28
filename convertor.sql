-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июл 28 2025 г., 18:20
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
  `id` int(5) UNSIGNED NOT NULL,
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
  `user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'ID Пользователя',
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

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(80) DEFAULT NULL,
  `temp_token` varchar(32) DEFAULT NULL,
  `avatar` varchar(80) NOT NULL,
  `role` enum('user','moderator','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `temp_token`, `avatar`, `role`, `created_at`, `updated_at`) VALUES
(1, 'razemsb', '$2y$10$0zsUFUECnD4KUbzRu9Mxp.LlYITSqGXTtLsaAM/PNCB5frJcUp7le', NULL, NULL, 'default-avatar-1.png', 'admin', '2025-07-22 15:16:02', '2025-07-22 18:10:25'),
(6, 'MakTraher', '$2y$10$kbCRfRAodtE2G9wdY19cm.zo2UwULp.QfEjq7PXv4r886.d73Jwau', NULL, NULL, 'default-avatar-4.png', 'admin', '2025-07-27 10:11:53', '2025-07-27 10:15:42');

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
  ADD KEY `idx_date` (`created_at`),
  ADD KEY `idx_user_id` (`user_id`);

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
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `conversions`
--
ALTER TABLE `conversions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `conversions`
--
ALTER TABLE `conversions`
  ADD CONSTRAINT `fk_conversions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
