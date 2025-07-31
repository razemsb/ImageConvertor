-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 31, 2025 at 02:21 PM
-- Server version: 10.11.13-MariaDB-log
-- PHP Version: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `convertor`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(5) UNSIGNED NOT NULL,
  `user_id` int(5) UNSIGNED NOT NULL COMMENT 'Admin ID',
  `login` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` date NOT NULL,
  `last_ip` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `user_id`, `login`, `password_hash`, `created_at`, `last_login`, `last_ip`) VALUES
(1, 1, 'razemsb', '$2y$10$8t/lOD1J4hfLFf2.tGcVHO6LbTprP7yPxXP2a7Jx8YYNxhFjZqPZi', '2025-07-21 20:29:23', '2025-07-31', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `admin_login_logs`
--

CREATE TABLE `admin_login_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `admin_id` int(5) UNSIGNED NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `success` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_login_logs`
--

INSERT INTO `admin_login_logs` (`id`, `admin_id`, `login_time`, `ip_address`, `user_agent`, `success`) VALUES
(1, 1, '2025-07-31 11:19:20', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 1),
(2, 1, '2025-07-31 11:20:44', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 1);

-- --------------------------------------------------------

--
-- Table structure for table `conversions`
--

CREATE TABLE `conversions` (
  `id` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL COMMENT 'IP пользователя',
  `user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'ID Пользователя',
  `user_agent` varchar(255) DEFAULT NULL,
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
-- Dumping data for table `conversions`
--

INSERT INTO `conversions` (`id`, `ip`, `user_id`, `user_agent`, `original_name`, `new_name`, `original_format`, `new_format`, `original_size`, `new_size`, `quality`, `status`, `error_message`, `created_at`) VALUES
(1, '127.0.0.1', 6, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'Снимок экрана 2025-07-31 022430.png', '75383fe8-b7b4-4f3e-98e1-b191132e3696-enigma-converted.jpeg', 'png', 'jpeg', 6625, 8042, 90, 'success', NULL, '2025-07-31 11:17:42'),
(2, '127.0.0.1', 6, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'Снимок экрана 2025-07-31 134359.png', 'ae6f2f0d-4647-44cd-9d00-a036b2989937-enigma-converted.jpeg', 'png', 'jpeg', 37301, 37171, 90, 'success', NULL, '2025-07-31 11:17:42'),
(3, '127.0.0.1', 6, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'Снимок экрана 2025-07-31 021936.png', 'e3539e6f-aa00-4cc1-b744-cf362e5972d9-enigma-converted.jpeg', 'png', 'jpeg', 72314, 102723, 90, 'success', NULL, '2025-07-31 11:17:42'),
(4, '127.0.0.1', 6, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'Снимок экрана 2025-07-31 131451.png', '1193fc7f-b0a9-47fd-83f2-a56b6c85eb40-enigma-converted.jpeg', 'png', 'jpeg', 122360, 195268, 90, 'success', NULL, '2025-07-31 11:17:42'),
(5, '127.0.0.1', 6, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'Снимок экрана 2025-07-31 021927.png', '8293db6a-f5b8-4798-8248-558d1480455c-enigma-converted.jpeg', 'png', 'jpeg', 261055, 121500, 90, 'success', NULL, '2025-07-31 11:17:42'),
(6, '127.0.0.1', 6, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'Снимок экрана 2025-07-31 021933.png', '078bfb7c-2957-45cf-9395-bff114954bb8-enigma-converted.jpeg', 'png', 'jpeg', 137885, 156683, 90, 'success', NULL, '2025-07-31 11:17:46'),
(7, '127.0.0.1', 6, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'Снимок экрана 2025-07-31 022430.png', 'a739bee4-c675-48fb-a6e8-954eb63511cf-enigma-converted.jpeg', 'png', 'jpeg', 6625, 8042, 90, 'success', NULL, '2025-07-31 11:17:46'),
(8, '127.0.0.1', 6, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'Снимок экрана 2025-07-31 131442.png', '61a11e29-8cc3-4333-b541-cfd15010affe-enigma-converted.jpeg', 'png', 'jpeg', 23068, 23716, 90, 'success', NULL, '2025-07-31 11:17:46'),
(9, '127.0.0.1', 6, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'Снимок экрана 2025-07-31 021936.png', '91327294-f39b-4f29-8b08-bd9428835c92-enigma-converted.jpeg', 'png', 'jpeg', 72314, 102723, 90, 'success', NULL, '2025-07-31 11:17:46'),
(10, '127.0.0.1', 6, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'Снимок экрана 2025-07-31 022138.png', '75aa9639-b345-44c7-bf32-fa91d6da6303-enigma-converted.jpeg', 'png', 'jpeg', 120727, 96504, 90, 'success', NULL, '2025-07-31 11:17:46'),
(11, '127.0.0.1', 6, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'Снимок экрана 2025-07-31 131451.png', 'ea675172-e7ad-4ca7-871c-67f68cb1e97d-enigma-converted.jpeg', 'png', 'jpeg', 122360, 195268, 90, 'success', NULL, '2025-07-31 11:17:46'),
(12, '127.0.0.1', 6, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'Снимок экрана 2025-07-31 021850.png', '92e83520-b8e1-421c-8b8e-0857de1a4f7f-enigma-converted.jpeg', 'png', 'jpeg', 204318, 132190, 90, 'success', NULL, '2025-07-31 11:17:46'),
(13, '127.0.0.1', 6, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'Снимок экрана 2025-07-31 021845.png', 'ee4092a8-8acb-45ac-a451-d793f6e9d0c9-enigma-converted.jpeg', 'png', 'jpeg', 371436, 134628, 90, 'success', NULL, '2025-07-31 11:17:46'),
(14, '127.0.0.1', 6, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'Снимок экрана 2025-07-31 021927.png', '29f94018-6cc9-4b4b-a2d9-764455b5ea2a-enigma-converted.jpeg', 'png', 'jpeg', 261055, 121500, 90, 'success', NULL, '2025-07-31 11:17:46'),
(15, '127.0.0.1', 6, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'albert-salim-XV7OUFLfB8Q-unsplash.jpg', NULL, 'jpg', 'jpeg', NULL, NULL, 90, 'error', 'MAX_FILE_SIZE_ERROR', '2025-07-31 11:17:57'),
(16, '127.0.0.1', 6, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'albert-salim-XV7OUFLfB8Q-unsplash.jpg', NULL, 'jpg', 'jpeg', NULL, NULL, 90, 'error', 'MAX_FILE_SIZE_ERROR', '2025-07-31 11:17:59'),
(17, '127.0.0.1', 6, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'albert-salim-XV7OUFLfB8Q-unsplash.jpg', NULL, 'jpg', 'jpeg', NULL, NULL, 90, 'error', 'MAX_FILE_SIZE_ERROR', '2025-07-31 11:18:03'),
(18, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 131451.png', 'acbfd657-20df-44bf-aea8-de3393244ebe-enigma-converted.webp', 'png', 'webp', 122360, 104134, 90, 'success', NULL, '2025-07-31 11:18:20'),
(19, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 131459.png', '1bd1e5c5-aae0-456e-80c0-3a06ef30c8a0-enigma-converted.webp', 'png', 'webp', 126551, 109438, 90, 'success', NULL, '2025-07-31 11:18:21'),
(20, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 134359.png', '30eaba1b-fe90-41ec-a8f0-2276f8de03b6-enigma-converted.webp', 'png', 'webp', 37301, 15438, 90, 'success', NULL, '2025-07-31 11:18:22'),
(21, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 131451.png', '41faeeec-fcbc-4d8d-84a0-8344e760fba1-enigma-converted.webp', 'png', 'webp', 122360, 104134, 90, 'success', NULL, '2025-07-31 11:18:23'),
(22, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 131459.png', '2327a826-9939-4654-a6e3-6f44b8f29adf-enigma-converted.webp', 'png', 'webp', 126551, 109438, 90, 'success', NULL, '2025-07-31 11:18:24'),
(23, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 134359.png', 'd0cfbe5c-ede6-4d75-8aa0-359e91467c93-enigma-converted.webp', 'png', 'webp', 37301, 15438, 90, 'success', NULL, '2025-07-31 11:18:25'),
(24, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 131451.png', '451a328c-e15f-44bb-9311-10bbcfa25c10-enigma-converted.webp', 'png', 'webp', 122360, 104134, 90, 'success', NULL, '2025-07-31 11:18:26'),
(25, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 131459.png', '80f948ff-2be4-49be-8beb-443da5c0fde7-enigma-converted.webp', 'png', 'webp', 126551, 109438, 90, 'success', NULL, '2025-07-31 11:18:27'),
(26, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 134359.png', 'b3297178-2636-4ed6-a730-b6a7ae8168f2-enigma-converted.avif', 'png', 'avif', 37301, 18754, 90, 'success', NULL, '2025-07-31 11:18:33'),
(27, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 131451.png', 'e3455d97-07c1-4e96-b797-94a06f9944ac-enigma-converted.avif', 'png', 'avif', 122360, 59968, 90, 'success', NULL, '2025-07-31 11:18:42'),
(28, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 131459.png', '4a4a4e14-3a33-44d0-8193-9fff6764f241-enigma-converted.avif', 'png', 'avif', 126551, 59040, 90, 'success', NULL, '2025-07-31 11:18:51'),
(29, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 134359.png', '0bfbbb08-a0f9-46ae-8052-cb11bc25ed82-enigma-converted.avif', 'png', 'avif', 37301, 18754, 90, 'success', NULL, '2025-07-31 11:18:54'),
(30, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 131451.png', 'c4b4a717-be6b-465d-a2d8-1cc6dcc4d2ff-enigma-converted.avif', 'png', 'avif', 122360, 59968, 90, 'success', NULL, '2025-07-31 11:19:03'),
(31, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 131459.png', 'd607e41e-e1cf-4a52-aebe-bc2c715ff089-enigma-converted.avif', 'png', 'avif', 126551, 59040, 90, 'success', NULL, '2025-07-31 11:19:11'),
(32, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 134359.png', 'a6896d4d-580e-4b4e-ae3b-4fd8abff3305-enigma-converted.png', 'png', 'png', 37301, 46393, 90, 'success', NULL, '2025-07-31 11:19:11'),
(33, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 131451.png', 'b63783d7-cf35-47da-a8f4-eb93157c2f40-enigma-converted.png', 'png', 'png', 122360, 161470, 90, 'success', NULL, '2025-07-31 11:19:11'),
(34, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 134359.png', '75b4a2cb-3418-4865-aaa5-7ded431e1598-enigma-converted.png', 'png', 'png', 37301, 46393, 90, 'success', NULL, '2025-07-31 11:19:11'),
(35, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 131459.png', '2c6d16ad-b2dd-4fec-959b-025f858e5514-enigma-converted.png', 'png', 'png', 126551, 168404, 90, 'success', NULL, '2025-07-31 11:19:11'),
(36, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 131451.png', '84105a23-d1fa-48fb-8236-79db78c6fd45-enigma-converted.png', 'png', 'png', 122360, 161470, 90, 'success', NULL, '2025-07-31 11:19:12'),
(37, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 022430.png', '8cb9186a-19b3-489f-89a3-44f2713fba59-enigma-converted.png', 'png', 'png', 6625, 7958, 90, 'success', NULL, '2025-07-31 11:19:12'),
(38, '::1', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'Снимок экрана 2025-07-31 021933.png', '934505d8-3bea-4feb-9aa5-b673b1cc72bf-enigma-converted.png', 'png', 'png', 137885, 178492, 90, 'success', NULL, '2025-07-31 11:19:12'),
(39, '::1', 15, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Снимок экрана 2025-07-31 021936.png', '74706784-42ce-4096-b538-a3d878bd1fd7-enigma-converted.avif', 'png', 'avif', 72314, 41098, 90, 'success', NULL, '2025-07-31 11:21:12'),
(40, '::1', 15, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Снимок экрана 2025-07-31 022430.png', 'f5ca4b15-0c63-4dc1-8cdf-90c7abdc19f6-enigma-converted.avif', 'png', 'avif', 6625, 6055, 90, 'success', NULL, '2025-07-31 11:21:13'),
(41, '::1', 15, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Снимок экрана 2025-07-31 021927.png', '35b5464b-7e2c-4261-8dac-3b1cb08b0274-enigma-converted.avif', 'png', 'avif', 261055, 49387, 90, 'success', NULL, '2025-07-31 11:21:21'),
(42, '::1', 15, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Снимок экрана 2025-07-31 021845.png', '77192a5a-745e-447c-ab71-2f1bda0f8a5e-enigma-converted.avif', 'png', 'avif', 371436, 45161, 90, 'success', NULL, '2025-07-31 11:21:29'),
(43, '::1', 15, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Снимок экрана 2025-07-31 021936.png', '6e7e3333-067d-48ed-b6c1-3b0389510738-enigma-converted.jpeg', 'png', 'jpeg', 72314, 102723, 90, 'success', NULL, '2025-07-31 11:21:29'),
(44, '::1', 15, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Снимок экрана 2025-07-31 021927.png', '71567777-eb8b-4db6-9b5b-9f014993171f-enigma-converted.jpeg', 'png', 'jpeg', 261055, 121500, 90, 'success', NULL, '2025-07-31 11:21:29'),
(45, '::1', 15, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Снимок экрана 2025-07-31 021845.png', '521034da-56a0-408b-adaf-653aa3f7b316-enigma-converted.jpeg', 'png', 'jpeg', 371436, 134628, 90, 'success', NULL, '2025-07-31 11:21:29'),
(46, '::1', 15, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Снимок экрана 2025-07-31 021927.png', '84f31ef6-454b-4691-a55d-4e53629265ec-enigma-converted.png', 'png', 'png', 261055, 346950, 90, 'success', NULL, '2025-07-31 11:21:29'),
(47, '::1', 15, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Снимок экрана 2025-07-31 021845.png', 'dfff99e0-6029-457b-8a4b-040444f804f8-enigma-converted.png', 'png', 'png', 371436, 572541, 90, 'success', NULL, '2025-07-31 11:21:29'),
(48, '::1', 15, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Снимок экрана 2025-07-31 021845.png', '347d4ebc-afe0-40ee-9ab5-f74e661b6ace-enigma-converted.webp', 'png', 'webp', 371436, 53170, 90, 'success', NULL, '2025-07-31 11:21:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(80) DEFAULT NULL,
  `last_ip` varchar(15) DEFAULT NULL,
  `last_agent` varchar(255) DEFAULT NULL,
  `avatar` varchar(80) NOT NULL,
  `role` enum('user','moderator','admin') NOT NULL,
  `status` enum('active','banned') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `last_ip`, `last_agent`, `avatar`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'razemsb', '$2y$10$8t/lOD1J4hfLFf2.tGcVHO6LbTprP7yPxXP2a7Jx8YYNxhFjZqPZi', 'maxim1xxx363@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'default-avatar-1.png', 'admin', 'active', '2025-07-22 15:16:02', '2025-07-31 11:14:49'),
(6, 'MakTraher', '$2y$10$kbCRfRAodtE2G9wdY19cm.zo2UwULp.QfEjq7PXv4r886.d73Jwau', 'maxim1xxx363@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'default-avatar-4.png', 'admin', 'active', '2025-07-27 10:11:53', '2025-07-31 11:17:34'),
(15, 'IamBatman', '$2y$10$7wxcgkvuIfX5zdavIYzrzO8rVJ.16p1eabrLGyO6UEdG700xpalgu', 'batman@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'default-avatar-4.png', 'user', 'active', '2025-07-31 11:20:33', '2025-07-31 11:20:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD KEY `admins_ibfk_1` (`user_id`);

--
-- Indexes for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `conversions`
--
ALTER TABLE `conversions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_ip` (`ip`),
  ADD KEY `idx_date` (`created_at`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `conversions`
--
ALTER TABLE `conversions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  ADD CONSTRAINT `fk_admin_logs` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `conversions`
--
ALTER TABLE `conversions`
  ADD CONSTRAINT `fk_conversions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
