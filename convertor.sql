-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июл 27 2025 г., 22:01
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
(1, 'razemsb', '$2y$10$Q9E6ImS4ADniKgLe6Qny6.kaD3oeOPihJ7dVaARM3XIDIGBHIBNA6', '2025-07-21 20:29:23'),
(2, 'MakTraher', '$2y$10$Ii6TgWgn3JwHqTy3npiioudZ1X41sAd4.XdqffXITnAofGsD90Yaq\r\n      ', '2025-07-27 19:49:19');

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
(1, '::1', 1, 'Снимок экрана 2025-07-26 221403.png', '68852d35ba6df.webp', 'png', 'webp', 31301, 3748, 80, 'success', NULL, '2025-07-26 19:32:05'),
(2, '::1', 1, 'Снимок экрана 2025-07-26 221403.png', '68852d74a97dd.webp', 'png', 'webp', 31301, 3748, 80, 'success', NULL, '2025-07-26 19:33:08'),
(3, '::1', 1, 'Снимок экрана 2025-07-26 210906.png', '68852f61ef2b0.webp', 'png', 'webp', 49849, 12390, 80, 'success', NULL, '2025-07-26 19:41:22'),
(4, '::1', NULL, 'Снимок экрана 2025-07-26 221403.png', '688604068681f.webp', 'png', 'webp', 31301, 3748, 80, 'success', NULL, '2025-07-27 10:48:38'),
(5, '::1', NULL, 'Снимок экрана 2025-07-26 224605.png', '6886040a3faf2.png', 'png', 'png', 396400, 438013, 80, 'success', NULL, '2025-07-27 10:48:42'),
(6, '::1', NULL, 'Снимок экрана 2025-07-26 215832.png', '6886040b99bd4.png', 'png', 'png', 25040, 27363, 80, 'success', NULL, '2025-07-27 10:48:43'),
(7, '::1', NULL, 'Снимок экрана 2025-07-26 224605.png', '6886040d684fa.jpeg', 'png', 'jpeg', 396400, 168173, 80, 'success', NULL, '2025-07-27 10:48:45'),
(8, '::1', NULL, 'a8e980ee-d90e-46fa-af74-944cc72fbedd.jpg', '6886041297533.jpeg', 'jpg', 'jpeg', 56873, 59530, 80, 'success', NULL, '2025-07-27 10:48:50'),
(9, '::1', NULL, 'Programming meme.jpg', '688604129b974.jpeg', 'jpg', 'jpeg', 48906, 51933, 80, 'success', NULL, '2025-07-27 10:48:50'),
(10, '::1', NULL, 'Снимок экрана 2025-07-26 001402.png', '68860412a2628.jpeg', 'png', 'jpeg', 32636, 27384, 80, 'success', NULL, '2025-07-27 10:48:50'),
(11, '::1', NULL, 'загружено (4).jpg', '68860412a72fc.jpeg', 'jpg', 'jpeg', 52067, 59552, 80, 'success', NULL, '2025-07-27 10:48:50'),
(12, '::1', NULL, 'Снимок экрана 2025-07-26 000438.png', '68860412ac021.jpeg', 'png', 'jpeg', 27158, 22874, 80, 'success', NULL, '2025-07-27 10:48:50'),
(13, '::1', NULL, 'Снимок экрана 2025-07-26 000452.png', '68860412b4d37.jpeg', 'png', 'jpeg', 32303, 22816, 80, 'success', NULL, '2025-07-27 10:48:50'),
(14, '::1', NULL, 'Снимок экрана 2025-07-26 215832.png', '68860412bcd10.jpeg', 'png', 'jpeg', 25040, 5932, 80, 'success', NULL, '2025-07-27 10:48:50'),
(15, '::1', NULL, 'Снимок экрана 2025-07-26 210906.png', '68860412bfdc9.jpeg', 'png', 'jpeg', 49849, 22746, 80, 'success', NULL, '2025-07-27 10:48:50'),
(16, '::1', NULL, 'Снимок экрана 2025-07-26 221403.png', '68860412c2d40.jpeg', 'png', 'jpeg', 31301, 7561, 80, 'success', NULL, '2025-07-27 10:48:50'),
(17, '::1', NULL, 'загружено (3).jpg', '68860412c828d.jpeg', 'jpg', 'jpeg', 83935, 84966, 80, 'success', NULL, '2025-07-27 10:48:50'),
(18, '::1', NULL, 'загружено (2).jpg', '68860412d07b1.jpeg', 'jpg', 'jpeg', 83935, 84966, 80, 'success', NULL, '2025-07-27 10:48:50'),
(19, '::1', NULL, 'Снимок экрана 2025-07-26 144016.png', '68860412dc6ff.jpeg', 'png', 'jpeg', 131297, 128714, 80, 'success', NULL, '2025-07-27 10:48:50'),
(20, '::1', NULL, 'Снимок экрана 2025-07-26 144433.png', '68860412e2c39.jpeg', 'png', 'jpeg', 46837, 59543, 80, 'success', NULL, '2025-07-27 10:48:50'),
(21, '::1', NULL, 'Devil may cry if it was peak.jpg', '68860412e6000.jpeg', 'jpg', 'jpeg', 41303, 42816, 80, 'success', NULL, '2025-07-27 10:48:50'),
(22, '::1', NULL, 'Снимок экрана 2025-07-26 004921.png', '68860412ea561.jpeg', 'png', 'jpeg', 291601, 136843, 80, 'success', NULL, '2025-07-27 10:48:50'),
(23, '::1', NULL, 'Снимок экрана 2025-07-26 153211.png', '68860412f1527.jpeg', 'png', 'jpeg', 194473, 133288, 80, 'success', NULL, '2025-07-27 10:48:50'),
(24, '::1', NULL, 'Снимок экрана 2025-07-26 144009.png', '688604130583f.jpeg', 'png', 'jpeg', 316765, 100164, 80, 'success', NULL, '2025-07-27 10:48:51'),
(25, '::1', NULL, 'Снимок экрана 2025-07-26 143909.png', '6886041310f97.jpeg', 'png', 'jpeg', 350907, 92623, 80, 'success', NULL, '2025-07-27 10:48:51'),
(26, '::1', NULL, 'Снимок экрана 2025-07-26 224605.png', '688604131ff05.jpeg', 'png', 'jpeg', 396400, 168173, 80, 'success', NULL, '2025-07-27 10:48:51'),
(27, '::1', NULL, 'Снимок экрана 2025-07-26 144002.png', '688604132fec0.jpeg', 'png', 'jpeg', 297902, 76391, 80, 'success', NULL, '2025-07-27 10:48:51'),
(28, '::1', NULL, '7d41c7fa-ea8d-4886-9d16-300b2fea05f7.jpg', '688604133ac37.jpeg', 'jpg', 'jpeg', 71482, 73630, 80, 'success', NULL, '2025-07-27 10:48:51'),
(29, '::1', NULL, '52f62b8c-a383-4f86-b4d6-5f6830b7ec0d.jpg', '6886041341d97.jpeg', 'jpg', 'jpeg', 145209, 152786, 80, 'success', NULL, '2025-07-27 10:48:51'),
(30, '::1', NULL, 'Снимок экрана 2025-07-26 113508.png', '688604134f52d.jpeg', 'png', 'jpeg', 1133884, 178357, 80, 'success', NULL, '2025-07-27 10:48:51'),
(31, '::1', NULL, 'загружено (5).jpg', '688604135949e.jpeg', 'jpg', 'jpeg', 67174, 70410, 80, 'success', NULL, '2025-07-27 10:48:51'),
(32, '::1', NULL, 'albert-salim-XV7OUFLfB8Q-unsplash.jpg', '68860413e673c.jpeg', 'jpg', 'jpeg', 5667167, 4808952, 80, 'success', NULL, '2025-07-27 10:48:52');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `temp_token` varchar(32) DEFAULT NULL,
  `avatar` varchar(80) NOT NULL,
  `role` enum('user','moderator','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `temp_token`, `avatar`, `role`, `created_at`, `updated_at`) VALUES
(1, 'razemsb', '$2y$10$0zsUFUECnD4KUbzRu9Mxp.LlYITSqGXTtLsaAM/PNCB5frJcUp7le', NULL, 'default-avatar-1.png', 'admin', '2025-07-22 15:16:02', '2025-07-22 18:10:25'),
(2, 'test', '$2y$10$HidI7A2y.rZp4GGaWlqIVeqDQHlY3S4EX.cNfvPq1NuVHcxBzm4SW', NULL, 'default-avatar-1.png', 'user', '2025-07-22 18:18:58', '2025-07-22 18:18:58'),
(3, 'cyufkhjggh', '$2y$10$Ctp.f9krpdNhY/ckVPwz1eUxvluaZ2GI8H9tBWvnCDp1Y4hlFog2W', NULL, 'default-avatar-4.png', 'user', '2025-07-22 18:19:36', '2025-07-22 18:19:36'),
(4, 'razer', '$2y$10$9reAqtVn7CJDObnoeE0Xz.rNK4Kncv4AfrElHcqttZf/M.LNI8pCy', NULL, 'default-avatar-5.png', 'user', '2025-07-26 16:33:27', '2025-07-26 16:33:27'),
(5, 'ewjhjkhkj', '$2y$10$CesWk5yRc7OAFICYqZVA8ejzLLpGi8tPG9RZ7Lnl8k3TU0ROP1vH2', NULL, 'default-avatar-5.png', 'user', '2025-07-27 09:57:18', '2025-07-27 09:57:18'),
(6, 'MakTraher', '$2y$10$kbCRfRAodtE2G9wdY19cm.zo2UwULp.QfEjq7PXv4r886.d73Jwau', NULL, 'default-avatar-4.png', 'admin', '2025-07-27 10:11:53', '2025-07-27 10:15:42'),
(7, 'PidrilaGomodrila', '$2y$10$fL3yqppE9emLG0NFXB7rDes2fEZAtsVu8X6OhJHY83H6.RGS0TwR.', NULL, 'default-avatar-1.png', 'user', '2025-07-27 10:19:42', '2025-07-27 10:19:42');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `conversions`
--
ALTER TABLE `conversions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
