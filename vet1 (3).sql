-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 07, 2025 at 03:20 PM
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
-- Database: `vet1`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(10) UNSIGNED NOT NULL,
  `pet_id` int(10) UNSIGNED NOT NULL,
  `veterinarian_id` int(10) UNSIGNED NOT NULL,
  `service_id` int(10) UNSIGNED NOT NULL,
  `appointment_date` date DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `time_slot` time DEFAULT NULL,
  `status` enum('scheduled','canceled','done') DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `reservation_code` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `schedule_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `pet_id`, `veterinarian_id`, `service_id`, `appointment_date`, `start_time`, `end_time`, `time_slot`, `status`, `notes`, `created_at`, `reservation_code`, `user_id`, `schedule_id`) VALUES
(77, 13, 8, 1, '2025-07-07', '08:00:00', '08:30:00', NULL, 'scheduled', NULL, '2025-07-04 23:26:16', 'RSV-0AA711', 29, 649),
(204, 26, 1, 1, '2025-07-11', '08:30:00', '09:00:00', NULL, 'scheduled', NULL, '2025-07-07 01:24:38', '565586', 36, 622),
(205, 24, 1, 1, '2025-07-11', '08:30:00', '09:00:00', NULL, '', NULL, '2025-07-07 01:25:14', '527449', 36, 622),
(206, 24, 11, 1, '2025-07-11', '08:00:00', '08:30:00', NULL, '', NULL, '2025-07-07 01:26:00', '828889', 36, 989),
(207, 25, 11, 1, '2025-07-11', '08:00:00', '08:30:00', NULL, '', NULL, '2025-07-07 01:26:42', '378724', 36, 989),
(208, 24, 11, 1, '2025-07-11', '08:00:00', '08:30:00', NULL, '', NULL, '2025-07-07 02:03:49', '958611', 36, 989),
(209, 25, 11, 1, '2025-07-11', '08:00:00', '08:30:00', NULL, '', NULL, '2025-07-07 02:04:20', '731386', 36, 989),
(211, 26, 11, 1, '2025-07-11', '08:00:00', '08:30:00', NULL, '', NULL, '2025-07-07 02:05:48', '848365', 36, 989),
(212, 26, 11, 6, '2025-07-12', '08:00:00', '08:30:00', NULL, '', NULL, '2025-07-07 02:06:15', '615195', 36, 1005),
(213, 26, 11, 2, '2025-07-12', '08:00:00', '08:30:00', NULL, 'scheduled', NULL, '2025-07-07 02:06:50', '710180', 36, 1005),
(214, 25, 11, 1, '2025-07-11', '08:30:00', '09:00:00', NULL, 'scheduled', NULL, '2025-07-07 02:09:01', '745201', 36, 990),
(217, 24, 1, 1, '2025-07-08', '08:00:00', '08:30:00', NULL, '', NULL, '2025-07-07 12:59:11', '569571', 36, 573),
(218, 24, 1, 1, '2025-07-07', '15:00:00', '15:30:00', NULL, 'scheduled', NULL, '2025-07-07 13:02:29', '236703', 36, 571),
(219, 25, 1, 1, '2025-07-09', '15:30:00', '16:00:00', NULL, 'scheduled', NULL, '2025-07-07 13:14:12', '998532', 36, 604),
(222, 32, 1, 1, '2025-07-10', '13:00:00', '13:30:00', NULL, 'scheduled', NULL, '2025-07-07 14:55:39', '231909', 62, 615);

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `id` int(10) UNSIGNED NOT NULL,
  `appointment_id` int(10) UNSIGNED NOT NULL,
  `veterinarian_id` int(10) UNSIGNED NOT NULL,
  `pet_id` int(10) UNSIGNED NOT NULL,
  `diagnosis` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL CHECK (`price` >= 0),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_records`
--

INSERT INTO `medical_records` (`id`, `appointment_id`, `veterinarian_id`, `pet_id`, `diagnosis`, `treatment`, `price`, `created_at`) VALUES
(75, 219, 1, 25, 'eee', 'Ciscenje zuba', 4500.00, '2025-07-07 14:39:47'),
(78, 222, 1, 32, 'eee', 'Ciscenje zuba', 4500.00, '2025-07-07 15:07:55');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `new_password` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `new_password`, `token`, `created_at`) VALUES
(2, 36, '$2y$10$yijWhE.dIDF9VJRiogxYhO9IPUNTpMpXLrlKfgUyFi1H84RrGqt8m', '2c0f91196ce7572c182c982b65c37406', '2025-07-05 23:50:19'),
(5, 36, '$2y$10$VhgqZDQnO1px2YFgonn8f.rB/RvJM9iekfixzCWwokOwy2L8duepC', 'ea16c11140fbecd1667f6eb4274ba7ee', '2025-07-06 19:53:06'),
(6, 36, '$2y$10$OiDOv3f3i7mRni7p2wohVOGM4b/I4mZ4JcyJC1WGPb4Vukfh.PBR6', '115a507b37e4f73b1378fe013278d24a', '2025-07-06 19:55:36'),
(7, 36, '$2y$10$AnCFKnTFtPecNOoMtO8y9uX2Jz8tdLaSAxq4c1VKggPGaiESIROxW', '52462b5bab74a7a08e6e176b0c0e4987', '2025-07-06 19:57:39'),
(9, 36, '$2y$10$K/CS3rHk3Wy7ZaqZDiG4su.hQtE1PjO4UK2ulDdykKSDC01WX0.FS', '5c7083be36c84dc964e090a3d52cc385', '0000-00-00 00:00:00'),
(11, 36, '$2y$10$GG4QmZZTzHwEbklymfVuh.faHVAivZ6OwEYj.xwoBg1OsSDUGSzna', '6d057a71f027f8f33ad26654bf669a74', '0000-00-00 00:00:00'),
(12, 36, '$2y$10$zeHYkHIPyuJiI5zhH6pDf.IljmjdH73uam1Fy.S65mKxHy5NzeR4y', '04918698a5ffed5486a2996bd1fe40dd', '0000-00-00 00:00:00'),
(13, 36, '$2y$10$n2Jh4AGxrsMcQtYmwhsALeOmHXPyhLBTt09v8AJmWP8Xq0KuXJorW', '6f720454155d96188946a8219740f0fc', '0000-00-00 00:00:00'),
(15, 36, '$2y$10$KDUjXDSNwHSQGVyux5kRxeoP5qbbIK95iMU8nMZwfpUbKpg5GJXOq', '89a036fbdee780401a436561de8635f8', '2025-07-06 20:19:33'),
(16, 36, '$2y$10$9R3NBhKXUwDs9GZ/eMfzAejTFMmNFzZKEyrerQAdg05DJgPZNzW6m', '4b273e4b9f034ba9a3755c4dbaa41973', '2025-07-06 20:21:50'),
(18, 62, '$2y$10$SqTGQ4WLhD9pbhNDKriltuXbzp9QBRugT3fKTg51XJNkVfM.TKkcO', '9dd4a86a976a7e857340187de550d2c2', '2025-07-07 14:51:01');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets_codes`
--

CREATE TABLE `password_resets_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(10) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets_codes`
--

INSERT INTO `password_resets_codes` (`id`, `user_id`, `code`, `created_at`) VALUES
(2, 36, '6ebf4b342a', '2025-07-06 20:28:20'),
(3, 36, '7356bb2238', '2025-07-06 20:32:31'),
(4, 36, '254215', '2025-07-06 20:35:12'),
(5, 36, '278820', '2025-07-06 20:36:26'),
(6, 36, '295079', '2025-07-07 01:34:53'),
(7, 62, '254605', '2025-07-07 14:54:10');

-- --------------------------------------------------------

--
-- Table structure for table `pets`
--

CREATE TABLE `pets` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('muski','zenski','nepoznat') DEFAULT 'nepoznat',
  `type_id` int(10) UNSIGNED DEFAULT NULL,
  `breed_id` int(10) UNSIGNED DEFAULT NULL,
  `owner_id` int(10) UNSIGNED DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pets`
--

INSERT INTO `pets` (`id`, `name`, `age`, `birth_date`, `gender`, `type_id`, `breed_id`, `owner_id`, `photo`) VALUES
(2, 'Mica', 3, '2021-07-10', 'zenski', 2, 3, 2, 'macka1.jpg'),
(3, 'Koko', 2, '2022-08-20', 'muski', 3, 5, 3, 'pas1.jpg'),
(4, 'Bela', 7, '2017-01-30', 'zenski', 4, 7, 4, 'konj1.jpg'),
(5, 'Čupko', 1, '2023-05-12', 'muski', 5, 9, 5, 'hrcak1.jpg'),
(6, 'Nindža', 4, '2020-09-05', 'zenski', 6, 11, 6, 'ribica1.jpg'),
(7, 'Zeka', 2, '2022-12-01', 'muski', 7, 13, 7, 'zec1.jpg'),
(8, 'Zli', 1, '2023-11-10', 'muski', 8, 15, 8, 'ribica2.jpg'),
(9, 'Zmijko', 6, '2018-02-22', 'zenski', 9, 17, 9, 'konj2.jpg'),
(10, 'Praseko', 3, '2021-04-14', 'muski', 10, 19, 10, 'hrcak2.jpg'),
(12, 'tom', 0, '2020-07-23', 'muski', 2, 3, 11, 'macka2.jpg'),
(13, 'PERO', 100, '1925-01-02', 'zenski', 6, 11, 11, 'pas2.jpg'),
(24, 'matejae', 1, '2024-01-05', 'zenski', 2, 4, 14, 'dog.jpg'),
(25, 'TomTom', 0, '2025-06-30', 'zenski', 6, 12, 14, 'zec2.jpg'),
(26, 'eeee', 0, '2025-07-04', 'muski', 4, 7, 14, 'pas1.jpg'),
(28, 'Reksss', 0, '2025-07-04', '', 4, 7, 15, 'konj2.jpg'),
(29, 'eeee', 0, '2025-06-28', 'muski', 5, 10, 14, 'macka2.jpg'),
(32, 'Mica', 0, '2025-07-01', 'muski', 2, 3, 21, 'hrcak1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `pet_breeds`
--

CREATE TABLE `pet_breeds` (
  `id` int(10) UNSIGNED NOT NULL,
  `type_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pet_breeds`
--

INSERT INTO `pet_breeds` (`id`, `type_id`, `name`) VALUES
(1, 1, 'labrador retriver'),
(2, 1, 'nemački ovčar'),
(3, 2, 'persijska'),
(4, 2, 'sibirska'),
(6, 3, 'ara'),
(5, 3, 'tigrica'),
(7, 4, 'arapski konj'),
(8, 4, 'lipicaner'),
(10, 5, 'patuljasti hrčak'),
(9, 5, 'sirijski hrčak'),
(11, 6, 'crvenouha kornjača'),
(12, 6, 'grčka kornjača'),
(13, 7, 'domaći zec'),
(14, 7, 'patuljasti zec'),
(16, 8, 'borac'),
(15, 8, 'zlatna ribica'),
(18, 9, 'boa konstriktor'),
(17, 9, 'kraljevski piton'),
(20, 10, 'minijaturno domaće prase'),
(19, 10, 'vijetnamsko prase');

-- --------------------------------------------------------

--
-- Table structure for table `pet_owners`
--

CREATE TABLE `pet_owners` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pet_owners`
--

INSERT INTO `pet_owners` (`id`, `user_id`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10),
(11, 29),
(12, 34),
(13, 35),
(14, 36),
(15, 38),
(16, 46),
(17, 47),
(18, 48),
(19, 52),
(20, 61),
(21, 62);

-- --------------------------------------------------------

--
-- Table structure for table `pet_types`
--

CREATE TABLE `pet_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pet_types`
--

INSERT INTO `pet_types` (`id`, `name`) VALUES
(5, 'hrčak'),
(4, 'konj'),
(6, 'kornjača'),
(2, 'mačka'),
(3, 'papagaj'),
(1, 'pas'),
(10, 'prase'),
(8, 'riba'),
(7, 'zec'),
(9, 'zmija');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admin'),
(3, 'owner'),
(2, 'veterinarian');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL CHECK (`price` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `price`) VALUES
(1, 'Vakcinacija', 'Vakcinacija protiv zaraznih bolesti', 3000.00),
(2, 'Opsti pregled', 'Kompletan klinički pregled ljubimca', 2500.00),
(3, 'Kastracija', 'Hirurško uklanjanje reproduktivnih organa', 7000.00),
(4, 'Sterilizacija', 'Sterilizacija ženke', 8000.00),
(5, 'Ciscenje zuba', 'Uklanjanje kamenca i nege zuba', 4500.00),
(6, 'Snimanje', 'RTG ili ultrazvučni pregled', 6000.00),
(7, 'Laboratorijske analize', 'Krv, urin i biohemijske analize', 5000.00),
(8, 'Hitna pomoc', 'Brza intervencija u hitnim slučajevima', 9000.00),
(9, 'Mikrocipovanje', 'Ugradnja mikročipa za identifikaciju', 2000.00),
(10, 'Eutanazija', 'Humano uspavljivanje ljubimca', 10000.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `negative_points` int(10) UNSIGNED DEFAULT 0,
  `role_id` int(10) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `activation_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `phone_number`, `address`, `active`, `negative_points`, `role_id`, `is_active`, `activation_token`) VALUES
(1, 'Admin', 'Admin', 'admin@petcare.com', '$2y$10$CecY2hC02UnKX7OhcDnAHO9IG7fbn83uZdf.8rVCm53mQ1PNmzEHe', '+381601112233', 'Adresa 40', 1, 0, 1, 1, NULL),
(2, 'Marija', 'Jovic', 'marija@example.com', '$2b$12$iVsCuXxIMCodbcjffZ4sJ.qX358zuGQ4QOSb8Rfik1VKyB58OVfj6', '+381601112233', 'Adresa 41', 1, 0, 3, 1, NULL),
(3, 'Nemanja', 'Kovacevic', 'nemanja@example.com', '$2b$12$Sd4SnI6oZreNoQpyoiY3R.rVtGxf/FRpSRxnHocF.5niN0BK/TQGO', '+381601112233', 'Adresa 42', 1, 1, 3, 0, NULL),
(4, 'Jelena', 'Petrovic', 'jelena@example.com', '$2b$12$jWKt3QiIXy3.PhPZc0I0jO8HJiY4nodXjrArgtZ6DobzlHROq7Yf.', '+381601112233', 'Adresa 43', 1, 1, 2, 0, NULL),
(5, 'Vuk', 'Stankovic', 'vuk@example.com', '$2b$12$ItSGl53Xn1SlGb.uFzhy/u0gFveCO3.TAKfxuYzgis/OJ/mP68SS6', '+381601112233', 'Adresa 44', 1, 0, 2, 0, NULL),
(6, 'Ivana', 'Milosevic', 'ivana@example.com', '$2b$12$eAyITHL6hMT5gkzGcVvgde2LNpokqCS.QqZa9t97cVMYJzbxn7xNe', '+381601112233', 'Adresa 45', 1, 1, 3, 0, NULL),
(7, 'Bojan', 'Zdravkovic', 'bojan@example.com', '$2b$12$in2hI9rT57gWjKDwr50VSeZHGCFkacKdf3bx5wo.cuiRiYCJBjK7q', '+381601112233', 'Adresa 46', 1, 0, 3, 0, NULL),
(8, 'Katarina', 'Savic', 'katarina@example.com', '$2b$12$213j2JXX0VuQ576PeGNaNeOBXu8Aa0bwSGDpHqeGfaKpwncNknWdu', '+381601112233', 'Adresa 47', 1, 1, 3, 0, NULL),
(9, 'Nikola', 'Todorovic', 'nikola@example.com', '$2b$12$pAH10dq3nGG5oXuX.Yx/GOgA6fvvWDnE28t6DKDmcbZ27ehdt2rU2', '+381601112233', 'Adresa 48', 1, 1, 3, 0, NULL),
(10, 'Milica', 'Antic', 'milica@example.com', '$2b$12$jqGAIeLZtyxUG7EsduBkLuTN6j2H2LRydMibWJg6OUTF/Z0ge22ce', '+381601112233', 'Adresa 498', 1, 6, 3, 1, NULL),
(11, 'Marko', 'Markovic', 'marko.vet@example.com', '$2y$10$dHiCCkD1irfCjA2wVo8p/.t0idnpLbDrngcDGe4yoQy5FK/S4jrmK', '+381611234567', 'Ulica 1, Beograd', 1, 0, 2, 1, NULL),
(29, 'Marko', 'Markovic', 'recineso21@gmail.com', '$2y$10$NM8sxiurR5ptg0nXc5GC/e3TTevgK2i2091PuOea1xoT3jBTs.A6y', '31231232132', '3123213212', 1, 5, 3, 0, NULL),
(30, 'Anaaa', 'Vetkovićcccc', 'vet1@petcare.com', '$2b$12$ZFDCVTNyR8d9VOR7327p5O4t1rtxo6IdFCZ2i.9I5K/zDLn0KoELq', '381601112234', 'Vet Adresa 1', 1, 0, 2, 0, NULL),
(31, 'Ivan', 'Lazarević', 'vet2@petcare.com', '$2b$12$mYYuhX4Teg41zPHqWNusq.qr3mPCX6xJVQI6YLmUDbQo2V7Jjlt02', '+381601112235', 'Vet Adresa 2', 1, 0, 2, 0, NULL),
(32, 'Maja', 'Bogdanović', 'vet3@petcare.com', '$2b$12$ajxseIf8zQB0Bf5hvIwniex0DloqWarUhtcm3N8ePsLp2JlR45Wha', '+381601112236', 'Vet Adresa 3', 1, 0, 2, 0, NULL),
(33, 'Stefan', 'Jovanović', 'vet4@petcare.com', '$2b$12$7ssCNutSzqtmv95AABBaLuvYW3O25vskOwRSkctYktFiBrnEeFcsi', '+381601112237', 'Vet Adresa 4', 1, 0, 2, 0, NULL),
(34, 'Lena', 'Jakovetic', 'lena@gmail.com', '$2y$10$cCiHOfyYtETqjgOepIYuIu6EA35vjughtp78aJzwJ2VxeH.0CXcBq', '134562324', 'Subotica 17', 1, 0, 3, 0, 'e33c3882009632a6a57dc2f4a0131976'),
(35, 'edo', 'edic', 'edo@gmail.com', '$2y$10$Ij.7eh9Gxah6ZBziZwkUeeXGOmAkoE5EjooFIuy189U1iXaYbfgnK', '123123123', 'subotica12', 1, 0, 3, 0, 'a932f2f1f49d2d80df34ffbdfe4f34d9'),
(36, 'Bojan2', 'Kovacic', 'reci@gmail.com', '$2y$10$vo3rQ8AJy1FTSJdXlk346OvlKAdS/CR.QWBJT1QnR4RBj7oEhdYy.', '06734231222', 'rsdas 2', 1, 5, 3, 1, NULL),
(38, 'Hanaa', 'Tomicc', 'hana2@gmail.com', '$2y$10$xKRsZlGntL/mgjomHaW7xeEyGH3T0hmOZz5nZl76VwrOypSWfuD7W', '065887729', 'reza 2', 1, 2, 3, 1, NULL),
(41, 'Bojan', 'Kovacic', 'kovacicpredrag0@gmail.com', '', '+381601112234', 'Vet Adresa 1', 1, 0, 2, 1, NULL),
(44, 'Bojan', 'Vetković', 'tomiqca@gmail.com', '$2y$10$Ytb2JlNLqX0skE/OxM6uMeHgziMA72pDfAndPhsWEaoQYF3VpQGbW', '23456789', 'cfghjk', 1, 0, 3, 0, 'b93aa81162b6a89a40a2d38f0931b05d'),
(45, 'Bojan', 'Kovacic', 'tomica@gmail.com', '$2y$10$/XZlQcFO9FxuKzBOOv2mLORbrmE2JeBGZrEqc0kCyitu9xVYhidiW', '84512', 'sfxdgcjbhnk', 1, 0, 3, 0, '86d1cb58c60572f027233504d2234bbf'),
(46, 'Bojan', 'Kovacic', 'bojan123@gmail.com', '$2y$10$eQ2UzEDvf74L2mTR..NsSuOPddohDZo/ayQVpVxFOnh2yhfeSHIHW', '0658877992', 'fdsfd', 1, 0, 3, 1, NULL),
(47, 'Mateja', 'ggrgr', 'mateja@gmail.com', '$2y$10$okDdrcIA5UxG7S81838MGeWe327qJDR21hSfqBsk3f.AfEN8XMU3u', '24343242432432', 'dfsdfsddsfds', 1, 0, 3, 1, NULL),
(48, 'Bojan', 'Kovacic', 'boja123@gmail.com', '$2y$10$wxwuDoWeYJsxaR4LlkPLYOOTbESZ7nrV6ZORMEREvfo6CH.K0bB3K', '2623232', 'sdfghjk', 1, 0, 3, 0, 'ed66440005512b56fe1477ebeb0b5a8c'),
(49, 'Bojan', 'Kovacic', 'tomicaaaa@gmail.com', '', '123456222', 'Vet Adresa 1', 1, 0, 2, 1, NULL),
(50, 'Milicaa', 'Vetković', 'kovacicbojtgrgffgan27@gmail.com', '', '81601112234', 'Vet Adresa 1', 1, 0, 2, 1, NULL),
(51, 'Bojan', 'Kovacic', 'kovacicbojassn27@gmail.comss', '', '453432543435', 'Vet Adresa 1', 1, 0, 2, 1, NULL),
(52, 'lena', 'lenicic', 'lenic@gmail.com', '$2y$10$86SXeeK1uQ1qrxg.lKisne5sm7pvUoMOI6sNl8zy0YYp.RKznwhEm', '06857477', 'AA 23', 1, 0, 3, 1, NULL),
(55, 'Bojan', 'Kovacic', 'kov@gmail.com', '', '381601112234', 'Vet Adresa 1', 1, 0, 2, 1, NULL),
(58, 'Bojan', 'Kovacic', 'vet6@petcare.com', '$2y$10$OHaG2q0l6Gc/xhH5lH.ageOw22Eo2Yu91HIdMIGw1Ide80i/lVxS6', '06533778899', 'Vet Adresa 1', 1, 0, 2, 1, NULL),
(59, 'edo', 'Arfi', 'vet17@petcare.com', '$2y$10$akujz4C7sAEg1mGC.T94duT6G4hgED253UH/e9H/KkS5O5MOWKPKK', '381601112234', 'Vet Adresa 1', 1, 0, 2, 1, NULL),
(60, 'edo', 'Arfi', 'vet12@petcare.com', '$2y$10$kLyxlpTKnIMgs8ygF62yJeIaRH7yLUvqj8wSqS2xASjwAZZFeg5Ey', '381601112234', 'Vet Adresa 1', 1, 0, 2, 1, NULL),
(61, 'Sonja', 'Evenger', 'sonja@gmail.com', '$2y$10$PeLsCAqmnqYhaV1vP4ju2O6o0xpgLY35t1fxwWXFzTBXb58BlgwK6', '0658877992', 'rewrewrewrew', 1, 0, 3, 0, '661ac9a16fa13caeb8850f41767fb4e1'),
(62, 'Sonjaa', 'Evegerr', 'sonja1@gmail.com', '$2y$10$TPXaRmnXrCrWCj87/Dur3emXeNx9rOaVD36Q/9MM0GVcw.vGjkEAW', '06588779922', 'ada 2', 1, 0, 3, 1, NULL),
(63, 'Bojan', 'Kovacic', 'kovacicdsadsaadsapredrag0@gmail.com', '$2y$10$Wricd/hPCE/ARqWGuNa9j.Mbi16wYQk3izKJQayiEiVELFaJ4qc8K', '381601112234', 'Vet Adresa 1', 1, 0, 2, 1, NULL),
(65, 'Bojan', 'Kovacic', 'bokiboki@gmail.com', '$2y$10$dWktGTUbtnwphVQZds4ac.CVGPDNwy5ecWRPXfCsSgI8I99tmtTs6', '381601112234', 'Vet Adresa 1', 1, 0, 2, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `veterinarians`
--

CREATE TABLE `veterinarians` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `license_number` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `veterinarians`
--

INSERT INTO `veterinarians` (`id`, `user_id`, `specialization`, `license_number`, `photo`) VALUES
(1, 11, 'Veliki kucni ljubimci', 'LIC123456', 'marko_markovic.jpg'),
(8, 30, 'Mali kućni ljubimci', 'LIC300001', 'ana_vetkovic.jpg'),
(9, 31, 'Egzotične životinje', 'LIC30000', 'ivan_lazarevic.jpg'),
(10, 32, 'Hirurgija', 'LIC300003', 'maja_bogdanovic.jpg'),
(11, 33, 'Dermatologija', 'LIC300004', 'stefan_jovanovic.jpg'),
(15, 41, 'Mali kućni ljubimci', 'LIC300002', NULL),
(20, 58, 'Egzotične životinje', 'LIC30000121', NULL),
(22, 60, 'Mali kućni ljubimci', 'LIC300001222', 'ivan_lazarevic.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `veterinarian_schedule`
--

CREATE TABLE `veterinarian_schedule` (
  `id` int(10) UNSIGNED NOT NULL,
  `veterinarian_id` int(10) UNSIGNED NOT NULL,
  `day_of_week` enum('Ponedeljak','Utorak','Sreda','Cetvrtak','Petak','Subota','Nedelja') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `veterinarian_schedule`
--

INSERT INTO `veterinarian_schedule` (`id`, `veterinarian_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(558, 1, 'Ponedeljak', '08:00:00', '08:30:00'),
(559, 1, 'Ponedeljak', '09:00:00', '09:30:00'),
(560, 1, 'Ponedeljak', '09:30:00', '10:00:00'),
(561, 1, 'Ponedeljak', '10:00:00', '10:30:00'),
(562, 1, 'Ponedeljak', '10:30:00', '11:00:00'),
(563, 1, 'Ponedeljak', '11:00:00', '11:30:00'),
(564, 1, 'Ponedeljak', '11:30:00', '12:00:00'),
(565, 1, 'Ponedeljak', '12:00:00', '12:30:00'),
(566, 1, 'Ponedeljak', '12:30:00', '13:00:00'),
(567, 1, 'Ponedeljak', '13:00:00', '13:30:00'),
(568, 1, 'Ponedeljak', '13:30:00', '14:00:00'),
(569, 1, 'Ponedeljak', '14:00:00', '14:30:00'),
(570, 1, 'Ponedeljak', '14:30:00', '15:00:00'),
(571, 1, 'Ponedeljak', '15:00:00', '15:30:00'),
(572, 1, 'Ponedeljak', '15:30:00', '16:00:00'),
(573, 1, 'Utorak', '08:00:00', '08:30:00'),
(574, 1, 'Utorak', '08:30:00', '09:00:00'),
(575, 1, 'Utorak', '09:00:00', '09:30:00'),
(576, 1, 'Utorak', '09:30:00', '10:00:00'),
(577, 1, 'Utorak', '10:00:00', '10:30:00'),
(578, 1, 'Utorak', '10:30:00', '11:00:00'),
(579, 1, 'Utorak', '11:00:00', '11:30:00'),
(580, 1, 'Utorak', '11:30:00', '12:00:00'),
(581, 1, 'Utorak', '12:00:00', '12:30:00'),
(582, 1, 'Utorak', '12:30:00', '13:00:00'),
(583, 1, 'Utorak', '13:00:00', '13:30:00'),
(584, 1, 'Utorak', '13:30:00', '14:00:00'),
(585, 1, 'Utorak', '14:00:00', '14:30:00'),
(586, 1, 'Utorak', '14:30:00', '15:00:00'),
(587, 1, 'Utorak', '15:00:00', '15:30:00'),
(588, 1, 'Utorak', '15:30:00', '16:00:00'),
(589, 1, 'Sreda', '08:00:00', '08:30:00'),
(590, 1, 'Sreda', '08:30:00', '09:00:00'),
(591, 1, 'Sreda', '09:00:00', '09:30:00'),
(592, 1, 'Sreda', '09:30:00', '10:00:00'),
(593, 1, 'Sreda', '10:00:00', '10:30:00'),
(594, 1, 'Sreda', '10:30:00', '11:00:00'),
(595, 1, 'Sreda', '11:00:00', '11:30:00'),
(596, 1, 'Sreda', '11:30:00', '12:00:00'),
(597, 1, 'Sreda', '12:00:00', '12:30:00'),
(598, 1, 'Sreda', '12:30:00', '13:00:00'),
(599, 1, 'Sreda', '13:00:00', '13:30:00'),
(600, 1, 'Sreda', '13:30:00', '14:00:00'),
(601, 1, 'Sreda', '14:00:00', '14:30:00'),
(602, 1, 'Sreda', '14:30:00', '15:00:00'),
(603, 1, 'Sreda', '15:00:00', '15:30:00'),
(604, 1, 'Sreda', '15:30:00', '16:00:00'),
(605, 1, 'Cetvrtak', '08:00:00', '08:30:00'),
(606, 1, 'Cetvrtak', '08:30:00', '09:00:00'),
(607, 1, 'Cetvrtak', '09:00:00', '09:30:00'),
(608, 1, 'Cetvrtak', '09:30:00', '10:00:00'),
(609, 1, 'Cetvrtak', '10:00:00', '10:30:00'),
(610, 1, 'Cetvrtak', '10:30:00', '11:00:00'),
(611, 1, 'Cetvrtak', '11:00:00', '11:30:00'),
(612, 1, 'Cetvrtak', '11:30:00', '12:00:00'),
(613, 1, 'Cetvrtak', '12:00:00', '12:30:00'),
(614, 1, 'Cetvrtak', '12:30:00', '13:00:00'),
(615, 1, 'Cetvrtak', '13:00:00', '13:30:00'),
(616, 1, 'Cetvrtak', '13:30:00', '14:00:00'),
(617, 1, 'Cetvrtak', '14:00:00', '14:30:00'),
(618, 1, 'Cetvrtak', '14:30:00', '15:00:00'),
(619, 1, 'Cetvrtak', '15:00:00', '15:30:00'),
(620, 1, 'Cetvrtak', '15:30:00', '16:00:00'),
(621, 1, 'Petak', '08:00:00', '08:30:00'),
(622, 1, 'Petak', '08:30:00', '09:00:00'),
(623, 1, 'Petak', '09:00:00', '09:30:00'),
(624, 1, 'Petak', '09:30:00', '10:00:00'),
(625, 1, 'Petak', '10:00:00', '10:30:00'),
(626, 1, 'Petak', '10:30:00', '11:00:00'),
(627, 1, 'Petak', '11:00:00', '11:30:00'),
(628, 1, 'Petak', '11:30:00', '12:00:00'),
(629, 1, 'Petak', '12:00:00', '12:30:00'),
(630, 1, 'Petak', '12:30:00', '13:00:00'),
(631, 1, 'Petak', '13:00:00', '13:30:00'),
(632, 1, 'Petak', '13:30:00', '14:00:00'),
(633, 1, 'Petak', '14:00:00', '14:30:00'),
(634, 1, 'Petak', '14:30:00', '15:00:00'),
(635, 1, 'Petak', '15:00:00', '15:30:00'),
(636, 1, 'Petak', '15:30:00', '16:00:00'),
(637, 1, 'Subota', '08:00:00', '08:30:00'),
(638, 1, 'Subota', '08:30:00', '09:00:00'),
(639, 1, 'Subota', '09:00:00', '09:30:00'),
(640, 1, 'Subota', '09:30:00', '10:00:00'),
(641, 1, 'Subota', '10:00:00', '10:30:00'),
(642, 1, 'Subota', '10:30:00', '11:00:00'),
(643, 1, 'Subota', '11:00:00', '11:30:00'),
(644, 1, 'Subota', '11:30:00', '12:00:00'),
(645, 1, 'Subota', '12:00:00', '12:30:00'),
(646, 1, 'Subota', '12:30:00', '13:00:00'),
(647, 1, 'Subota', '13:00:00', '13:30:00'),
(648, 1, 'Subota', '14:00:00', '14:30:00'),
(649, 8, 'Ponedeljak', '08:00:00', '08:30:00'),
(650, 8, 'Ponedeljak', '08:30:00', '09:00:00'),
(651, 8, 'Ponedeljak', '09:00:00', '09:30:00'),
(652, 8, 'Ponedeljak', '09:30:00', '10:00:00'),
(653, 8, 'Ponedeljak', '10:00:00', '10:30:00'),
(654, 8, 'Ponedeljak', '10:30:00', '11:00:00'),
(655, 8, 'Ponedeljak', '11:00:00', '11:30:00'),
(656, 8, 'Ponedeljak', '11:30:00', '12:00:00'),
(657, 8, 'Ponedeljak', '12:00:00', '12:30:00'),
(658, 8, 'Ponedeljak', '12:30:00', '13:00:00'),
(659, 8, 'Ponedeljak', '13:00:00', '13:30:00'),
(660, 8, 'Ponedeljak', '13:30:00', '14:00:00'),
(661, 8, 'Ponedeljak', '14:00:00', '14:30:00'),
(662, 8, 'Ponedeljak', '14:30:00', '15:00:00'),
(663, 8, 'Ponedeljak', '15:00:00', '15:30:00'),
(664, 8, 'Ponedeljak', '15:30:00', '16:00:00'),
(665, 8, 'Utorak', '08:00:00', '08:30:00'),
(666, 8, 'Utorak', '08:30:00', '09:00:00'),
(667, 8, 'Utorak', '09:00:00', '09:30:00'),
(668, 8, 'Utorak', '09:30:00', '10:00:00'),
(669, 8, 'Utorak', '10:00:00', '10:30:00'),
(670, 8, 'Utorak', '10:30:00', '11:00:00'),
(671, 8, 'Utorak', '11:00:00', '11:30:00'),
(672, 8, 'Utorak', '11:30:00', '12:00:00'),
(673, 8, 'Utorak', '12:00:00', '12:30:00'),
(674, 8, 'Utorak', '12:30:00', '13:00:00'),
(675, 8, 'Utorak', '13:00:00', '13:30:00'),
(676, 8, 'Utorak', '13:30:00', '14:00:00'),
(677, 8, 'Utorak', '14:00:00', '14:30:00'),
(678, 8, 'Utorak', '14:30:00', '15:00:00'),
(679, 8, 'Utorak', '15:00:00', '15:30:00'),
(680, 8, 'Utorak', '15:30:00', '16:00:00'),
(681, 8, 'Sreda', '08:00:00', '08:30:00'),
(682, 8, 'Sreda', '08:30:00', '09:00:00'),
(683, 8, 'Sreda', '09:00:00', '09:30:00'),
(684, 8, 'Sreda', '09:30:00', '10:00:00'),
(685, 8, 'Sreda', '10:00:00', '10:30:00'),
(686, 8, 'Sreda', '10:30:00', '11:00:00'),
(687, 8, 'Sreda', '11:00:00', '11:30:00'),
(688, 8, 'Sreda', '11:30:00', '12:00:00'),
(689, 8, 'Sreda', '12:00:00', '12:30:00'),
(690, 8, 'Sreda', '12:30:00', '13:00:00'),
(691, 8, 'Sreda', '13:00:00', '13:30:00'),
(692, 8, 'Sreda', '13:30:00', '14:00:00'),
(693, 8, 'Sreda', '14:00:00', '14:30:00'),
(694, 8, 'Sreda', '14:30:00', '15:00:00'),
(695, 8, 'Sreda', '15:00:00', '15:30:00'),
(696, 8, 'Sreda', '15:30:00', '16:00:00'),
(697, 8, 'Cetvrtak', '08:00:00', '08:30:00'),
(698, 8, 'Cetvrtak', '08:30:00', '09:00:00'),
(699, 8, 'Cetvrtak', '09:00:00', '09:30:00'),
(700, 8, 'Cetvrtak', '09:30:00', '10:00:00'),
(701, 8, 'Cetvrtak', '10:00:00', '10:30:00'),
(702, 8, 'Cetvrtak', '10:30:00', '11:00:00'),
(703, 8, 'Cetvrtak', '11:00:00', '11:30:00'),
(704, 8, 'Cetvrtak', '11:30:00', '12:00:00'),
(705, 8, 'Cetvrtak', '12:00:00', '12:30:00'),
(706, 8, 'Cetvrtak', '12:30:00', '13:00:00'),
(707, 8, 'Cetvrtak', '13:00:00', '13:30:00'),
(708, 8, 'Cetvrtak', '13:30:00', '14:00:00'),
(709, 8, 'Cetvrtak', '14:00:00', '14:30:00'),
(710, 8, 'Cetvrtak', '14:30:00', '15:00:00'),
(711, 8, 'Cetvrtak', '15:00:00', '15:30:00'),
(712, 8, 'Cetvrtak', '15:30:00', '16:00:00'),
(713, 8, 'Petak', '08:00:00', '08:30:00'),
(714, 8, 'Petak', '08:30:00', '09:00:00'),
(715, 8, 'Petak', '09:00:00', '09:30:00'),
(716, 8, 'Petak', '09:30:00', '10:00:00'),
(717, 8, 'Petak', '10:00:00', '10:30:00'),
(718, 8, 'Petak', '10:30:00', '11:00:00'),
(719, 8, 'Petak', '11:00:00', '11:30:00'),
(720, 8, 'Petak', '11:30:00', '12:00:00'),
(721, 8, 'Petak', '12:00:00', '12:30:00'),
(722, 8, 'Petak', '12:30:00', '13:00:00'),
(723, 8, 'Petak', '13:00:00', '13:30:00'),
(724, 8, 'Petak', '13:30:00', '14:00:00'),
(725, 8, 'Petak', '14:00:00', '14:30:00'),
(726, 8, 'Petak', '14:30:00', '15:00:00'),
(727, 8, 'Petak', '15:00:00', '15:30:00'),
(728, 8, 'Petak', '15:30:00', '16:00:00'),
(729, 8, 'Subota', '08:00:00', '08:30:00'),
(730, 8, 'Subota', '08:30:00', '09:00:00'),
(731, 8, 'Subota', '09:00:00', '09:30:00'),
(732, 8, 'Subota', '09:30:00', '10:00:00'),
(733, 8, 'Subota', '10:00:00', '10:30:00'),
(734, 8, 'Subota', '10:30:00', '11:00:00'),
(735, 8, 'Subota', '11:00:00', '11:30:00'),
(736, 8, 'Subota', '11:30:00', '12:00:00'),
(737, 8, 'Subota', '12:00:00', '12:30:00'),
(738, 8, 'Subota', '12:30:00', '13:00:00'),
(739, 8, 'Subota', '13:00:00', '13:30:00'),
(740, 8, 'Subota', '13:30:00', '14:00:00'),
(741, 9, 'Ponedeljak', '08:00:00', '08:30:00'),
(742, 9, 'Ponedeljak', '08:30:00', '09:00:00'),
(743, 9, 'Ponedeljak', '09:00:00', '09:30:00'),
(744, 9, 'Ponedeljak', '09:30:00', '10:00:00'),
(745, 9, 'Ponedeljak', '10:00:00', '10:30:00'),
(746, 9, 'Ponedeljak', '10:30:00', '11:00:00'),
(747, 9, 'Ponedeljak', '11:00:00', '11:30:00'),
(748, 9, 'Ponedeljak', '11:30:00', '12:00:00'),
(749, 9, 'Ponedeljak', '12:00:00', '12:30:00'),
(750, 9, 'Ponedeljak', '12:30:00', '13:00:00'),
(751, 9, 'Ponedeljak', '13:00:00', '13:30:00'),
(752, 9, 'Ponedeljak', '13:30:00', '14:00:00'),
(753, 9, 'Ponedeljak', '14:00:00', '14:30:00'),
(754, 9, 'Ponedeljak', '14:30:00', '15:00:00'),
(755, 9, 'Ponedeljak', '15:00:00', '15:30:00'),
(756, 9, 'Ponedeljak', '15:30:00', '16:00:00'),
(757, 9, 'Utorak', '08:00:00', '08:30:00'),
(758, 9, 'Utorak', '08:30:00', '09:00:00'),
(759, 9, 'Utorak', '09:00:00', '09:30:00'),
(760, 9, 'Utorak', '09:30:00', '10:00:00'),
(761, 9, 'Utorak', '10:00:00', '10:30:00'),
(762, 9, 'Utorak', '10:30:00', '11:00:00'),
(763, 9, 'Utorak', '11:00:00', '11:30:00'),
(764, 9, 'Utorak', '11:30:00', '12:00:00'),
(765, 9, 'Utorak', '12:00:00', '12:30:00'),
(766, 9, 'Utorak', '12:30:00', '13:00:00'),
(767, 9, 'Utorak', '13:00:00', '13:30:00'),
(768, 9, 'Utorak', '13:30:00', '14:00:00'),
(769, 9, 'Utorak', '14:00:00', '14:30:00'),
(770, 9, 'Utorak', '14:30:00', '15:00:00'),
(771, 9, 'Utorak', '15:00:00', '15:30:00'),
(772, 9, 'Utorak', '15:30:00', '16:00:00'),
(773, 9, 'Sreda', '08:00:00', '08:30:00'),
(774, 9, 'Sreda', '08:30:00', '09:00:00'),
(775, 9, 'Sreda', '09:00:00', '09:30:00'),
(776, 9, 'Sreda', '09:30:00', '10:00:00'),
(777, 9, 'Sreda', '10:00:00', '10:30:00'),
(778, 9, 'Sreda', '10:30:00', '11:00:00'),
(779, 9, 'Sreda', '11:00:00', '11:30:00'),
(780, 9, 'Sreda', '11:30:00', '12:00:00'),
(781, 9, 'Sreda', '12:00:00', '12:30:00'),
(782, 9, 'Sreda', '12:30:00', '13:00:00'),
(783, 9, 'Sreda', '13:00:00', '13:30:00'),
(784, 9, 'Sreda', '13:30:00', '14:00:00'),
(785, 9, 'Sreda', '14:00:00', '14:30:00'),
(786, 9, 'Sreda', '14:30:00', '15:00:00'),
(787, 9, 'Sreda', '15:00:00', '15:30:00'),
(788, 9, 'Sreda', '15:30:00', '16:00:00'),
(789, 9, 'Cetvrtak', '08:00:00', '08:30:00'),
(790, 9, 'Cetvrtak', '08:30:00', '09:00:00'),
(791, 9, 'Cetvrtak', '09:00:00', '09:30:00'),
(792, 9, 'Cetvrtak', '09:30:00', '10:00:00'),
(793, 9, 'Cetvrtak', '10:00:00', '10:30:00'),
(794, 9, 'Cetvrtak', '10:30:00', '11:00:00'),
(795, 9, 'Cetvrtak', '11:00:00', '11:30:00'),
(796, 9, 'Cetvrtak', '11:30:00', '12:00:00'),
(797, 9, 'Cetvrtak', '12:00:00', '12:30:00'),
(798, 9, 'Cetvrtak', '12:30:00', '13:00:00'),
(799, 9, 'Cetvrtak', '13:00:00', '13:30:00'),
(800, 9, 'Cetvrtak', '13:30:00', '14:00:00'),
(801, 9, 'Cetvrtak', '14:00:00', '14:30:00'),
(802, 9, 'Cetvrtak', '14:30:00', '15:00:00'),
(803, 9, 'Cetvrtak', '15:00:00', '15:30:00'),
(804, 9, 'Cetvrtak', '15:30:00', '16:00:00'),
(805, 9, 'Petak', '08:00:00', '08:30:00'),
(806, 9, 'Petak', '08:30:00', '09:00:00'),
(807, 9, 'Petak', '09:00:00', '09:30:00'),
(808, 9, 'Petak', '09:30:00', '10:00:00'),
(809, 9, 'Petak', '10:00:00', '10:30:00'),
(810, 9, 'Petak', '10:30:00', '11:00:00'),
(811, 9, 'Petak', '11:00:00', '11:30:00'),
(812, 9, 'Petak', '11:30:00', '12:00:00'),
(813, 9, 'Petak', '12:00:00', '12:30:00'),
(814, 9, 'Petak', '12:30:00', '13:00:00'),
(815, 9, 'Petak', '13:00:00', '13:30:00'),
(816, 9, 'Petak', '13:30:00', '14:00:00'),
(817, 9, 'Petak', '14:00:00', '14:30:00'),
(818, 9, 'Petak', '14:30:00', '15:00:00'),
(819, 9, 'Petak', '15:00:00', '15:30:00'),
(820, 9, 'Petak', '15:30:00', '16:00:00'),
(821, 9, 'Subota', '08:00:00', '08:30:00'),
(822, 9, 'Subota', '08:30:00', '09:00:00'),
(823, 9, 'Subota', '09:00:00', '09:30:00'),
(824, 9, 'Subota', '09:30:00', '10:00:00'),
(825, 9, 'Subota', '10:00:00', '10:30:00'),
(826, 9, 'Subota', '10:30:00', '11:00:00'),
(827, 9, 'Subota', '11:00:00', '11:30:00'),
(828, 9, 'Subota', '11:30:00', '12:00:00'),
(829, 9, 'Subota', '12:00:00', '12:30:00'),
(830, 9, 'Subota', '12:30:00', '13:00:00'),
(831, 9, 'Subota', '13:00:00', '13:30:00'),
(832, 9, 'Subota', '13:30:00', '14:00:00'),
(833, 10, 'Ponedeljak', '08:00:00', '08:30:00'),
(834, 10, 'Ponedeljak', '08:30:00', '09:00:00'),
(835, 10, 'Ponedeljak', '09:00:00', '09:30:00'),
(836, 10, 'Ponedeljak', '09:30:00', '10:00:00'),
(837, 10, 'Ponedeljak', '10:00:00', '10:30:00'),
(838, 10, 'Ponedeljak', '10:30:00', '11:00:00'),
(839, 10, 'Ponedeljak', '11:00:00', '11:30:00'),
(840, 10, 'Ponedeljak', '11:30:00', '12:00:00'),
(841, 10, 'Ponedeljak', '12:00:00', '12:30:00'),
(842, 10, 'Ponedeljak', '12:30:00', '13:00:00'),
(843, 10, 'Ponedeljak', '13:00:00', '13:30:00'),
(844, 10, 'Ponedeljak', '13:30:00', '14:00:00'),
(845, 10, 'Ponedeljak', '14:00:00', '14:30:00'),
(846, 10, 'Ponedeljak', '14:30:00', '15:00:00'),
(847, 10, 'Ponedeljak', '15:00:00', '15:30:00'),
(848, 10, 'Ponedeljak', '15:30:00', '16:00:00'),
(849, 10, 'Utorak', '08:00:00', '08:30:00'),
(850, 10, 'Utorak', '08:30:00', '09:00:00'),
(851, 10, 'Utorak', '09:00:00', '09:30:00'),
(852, 10, 'Utorak', '09:30:00', '10:00:00'),
(853, 10, 'Utorak', '10:00:00', '10:30:00'),
(854, 10, 'Utorak', '10:30:00', '11:00:00'),
(855, 10, 'Utorak', '11:00:00', '11:30:00'),
(856, 10, 'Utorak', '11:30:00', '12:00:00'),
(857, 10, 'Utorak', '12:00:00', '12:30:00'),
(858, 10, 'Utorak', '12:30:00', '13:00:00'),
(859, 10, 'Utorak', '13:00:00', '13:30:00'),
(860, 10, 'Utorak', '13:30:00', '14:00:00'),
(861, 10, 'Utorak', '14:00:00', '14:30:00'),
(862, 10, 'Utorak', '14:30:00', '15:00:00'),
(863, 10, 'Utorak', '15:00:00', '15:30:00'),
(864, 10, 'Utorak', '15:30:00', '16:00:00'),
(865, 10, 'Sreda', '08:00:00', '08:30:00'),
(866, 10, 'Sreda', '08:30:00', '09:00:00'),
(867, 10, 'Sreda', '09:00:00', '09:30:00'),
(868, 10, 'Sreda', '09:30:00', '10:00:00'),
(869, 10, 'Sreda', '10:00:00', '10:30:00'),
(870, 10, 'Sreda', '10:30:00', '11:00:00'),
(871, 10, 'Sreda', '11:00:00', '11:30:00'),
(872, 10, 'Sreda', '11:30:00', '12:00:00'),
(873, 10, 'Sreda', '12:00:00', '12:30:00'),
(874, 10, 'Sreda', '12:30:00', '13:00:00'),
(875, 10, 'Sreda', '13:00:00', '13:30:00'),
(876, 10, 'Sreda', '13:30:00', '14:00:00'),
(877, 10, 'Sreda', '14:00:00', '14:30:00'),
(878, 10, 'Sreda', '14:30:00', '15:00:00'),
(879, 10, 'Sreda', '15:00:00', '15:30:00'),
(880, 10, 'Sreda', '15:30:00', '16:00:00'),
(881, 10, 'Cetvrtak', '08:00:00', '08:30:00'),
(882, 10, 'Cetvrtak', '08:30:00', '09:00:00'),
(883, 10, 'Cetvrtak', '09:00:00', '09:30:00'),
(884, 10, 'Cetvrtak', '09:30:00', '10:00:00'),
(885, 10, 'Cetvrtak', '10:00:00', '10:30:00'),
(886, 10, 'Cetvrtak', '10:30:00', '11:00:00'),
(887, 10, 'Cetvrtak', '11:00:00', '11:30:00'),
(888, 10, 'Cetvrtak', '11:30:00', '12:00:00'),
(889, 10, 'Cetvrtak', '12:00:00', '12:30:00'),
(890, 10, 'Cetvrtak', '12:30:00', '13:00:00'),
(891, 10, 'Cetvrtak', '13:00:00', '13:30:00'),
(892, 10, 'Cetvrtak', '13:30:00', '14:00:00'),
(893, 10, 'Cetvrtak', '14:00:00', '14:30:00'),
(894, 10, 'Cetvrtak', '14:30:00', '15:00:00'),
(895, 10, 'Cetvrtak', '15:00:00', '15:30:00'),
(896, 10, 'Cetvrtak', '15:30:00', '16:00:00'),
(897, 10, 'Petak', '08:00:00', '08:30:00'),
(898, 10, 'Petak', '08:30:00', '09:00:00'),
(899, 10, 'Petak', '09:00:00', '09:30:00'),
(900, 10, 'Petak', '09:30:00', '10:00:00'),
(901, 10, 'Petak', '10:00:00', '10:30:00'),
(902, 10, 'Petak', '10:30:00', '11:00:00'),
(903, 10, 'Petak', '11:00:00', '11:30:00'),
(904, 10, 'Petak', '11:30:00', '12:00:00'),
(905, 10, 'Petak', '12:00:00', '12:30:00'),
(906, 10, 'Petak', '12:30:00', '13:00:00'),
(907, 10, 'Petak', '13:00:00', '13:30:00'),
(908, 10, 'Petak', '13:30:00', '14:00:00'),
(909, 10, 'Petak', '14:00:00', '14:30:00'),
(910, 10, 'Petak', '14:30:00', '15:00:00'),
(911, 10, 'Petak', '15:00:00', '15:30:00'),
(912, 10, 'Petak', '15:30:00', '16:00:00'),
(913, 10, 'Subota', '08:00:00', '08:30:00'),
(914, 10, 'Subota', '08:30:00', '09:00:00'),
(915, 10, 'Subota', '09:00:00', '09:30:00'),
(916, 10, 'Subota', '09:30:00', '10:00:00'),
(917, 10, 'Subota', '10:00:00', '10:30:00'),
(918, 10, 'Subota', '10:30:00', '11:00:00'),
(919, 10, 'Subota', '11:00:00', '11:30:00'),
(920, 10, 'Subota', '11:30:00', '12:00:00'),
(921, 10, 'Subota', '12:00:00', '12:30:00'),
(922, 10, 'Subota', '12:30:00', '13:00:00'),
(923, 10, 'Subota', '13:00:00', '13:30:00'),
(924, 10, 'Subota', '13:30:00', '14:00:00'),
(925, 11, 'Ponedeljak', '08:00:00', '08:30:00'),
(926, 11, 'Ponedeljak', '08:30:00', '09:00:00'),
(927, 11, 'Ponedeljak', '09:00:00', '09:30:00'),
(928, 11, 'Ponedeljak', '09:30:00', '10:00:00'),
(929, 11, 'Ponedeljak', '10:00:00', '10:30:00'),
(930, 11, 'Ponedeljak', '10:30:00', '11:00:00'),
(931, 11, 'Ponedeljak', '11:00:00', '11:30:00'),
(932, 11, 'Ponedeljak', '11:30:00', '12:00:00'),
(933, 11, 'Ponedeljak', '12:00:00', '12:30:00'),
(934, 11, 'Ponedeljak', '12:30:00', '13:00:00'),
(935, 11, 'Ponedeljak', '13:00:00', '13:30:00'),
(936, 11, 'Ponedeljak', '13:30:00', '14:00:00'),
(937, 11, 'Ponedeljak', '14:00:00', '14:30:00'),
(938, 11, 'Ponedeljak', '14:30:00', '15:00:00'),
(939, 11, 'Ponedeljak', '15:00:00', '15:30:00'),
(940, 11, 'Ponedeljak', '15:30:00', '16:00:00'),
(941, 11, 'Utorak', '08:00:00', '08:30:00'),
(942, 11, 'Utorak', '08:30:00', '09:00:00'),
(943, 11, 'Utorak', '09:00:00', '09:30:00'),
(944, 11, 'Utorak', '09:30:00', '10:00:00'),
(945, 11, 'Utorak', '10:00:00', '10:30:00'),
(946, 11, 'Utorak', '10:30:00', '11:00:00'),
(947, 11, 'Utorak', '11:00:00', '11:30:00'),
(948, 11, 'Utorak', '11:30:00', '12:00:00'),
(949, 11, 'Utorak', '12:00:00', '12:30:00'),
(950, 11, 'Utorak', '12:30:00', '13:00:00'),
(951, 11, 'Utorak', '13:00:00', '13:30:00'),
(952, 11, 'Utorak', '13:30:00', '14:00:00'),
(953, 11, 'Utorak', '14:00:00', '14:30:00'),
(954, 11, 'Utorak', '14:30:00', '15:00:00'),
(955, 11, 'Utorak', '15:00:00', '15:30:00'),
(956, 11, 'Utorak', '15:30:00', '16:00:00'),
(957, 11, 'Sreda', '08:00:00', '08:30:00'),
(958, 11, 'Sreda', '08:30:00', '09:00:00'),
(959, 11, 'Sreda', '09:00:00', '09:30:00'),
(960, 11, 'Sreda', '09:30:00', '10:00:00'),
(961, 11, 'Sreda', '10:00:00', '10:30:00'),
(962, 11, 'Sreda', '10:30:00', '11:00:00'),
(963, 11, 'Sreda', '11:00:00', '11:30:00'),
(964, 11, 'Sreda', '11:30:00', '12:00:00'),
(965, 11, 'Sreda', '12:00:00', '12:30:00'),
(966, 11, 'Sreda', '12:30:00', '13:00:00'),
(967, 11, 'Sreda', '13:00:00', '13:30:00'),
(968, 11, 'Sreda', '13:30:00', '14:00:00'),
(969, 11, 'Sreda', '14:00:00', '14:30:00'),
(970, 11, 'Sreda', '14:30:00', '15:00:00'),
(971, 11, 'Sreda', '15:00:00', '15:30:00'),
(972, 11, 'Sreda', '15:30:00', '16:00:00'),
(973, 11, 'Cetvrtak', '08:00:00', '08:30:00'),
(974, 11, 'Cetvrtak', '08:30:00', '09:00:00'),
(975, 11, 'Cetvrtak', '09:00:00', '09:30:00'),
(976, 11, 'Cetvrtak', '09:30:00', '10:00:00'),
(977, 11, 'Cetvrtak', '10:00:00', '10:30:00'),
(978, 11, 'Cetvrtak', '10:30:00', '11:00:00'),
(979, 11, 'Cetvrtak', '11:00:00', '11:30:00'),
(980, 11, 'Cetvrtak', '11:30:00', '12:00:00'),
(981, 11, 'Cetvrtak', '12:00:00', '12:30:00'),
(982, 11, 'Cetvrtak', '12:30:00', '13:00:00'),
(983, 11, 'Cetvrtak', '13:00:00', '13:30:00'),
(984, 11, 'Cetvrtak', '13:30:00', '14:00:00'),
(985, 11, 'Cetvrtak', '14:00:00', '14:30:00'),
(986, 11, 'Cetvrtak', '14:30:00', '15:00:00'),
(987, 11, 'Cetvrtak', '15:00:00', '15:30:00'),
(988, 11, 'Cetvrtak', '15:30:00', '16:00:00'),
(989, 11, 'Petak', '08:00:00', '08:30:00'),
(990, 11, 'Petak', '08:30:00', '09:00:00'),
(991, 11, 'Petak', '09:00:00', '09:30:00'),
(992, 11, 'Petak', '09:30:00', '10:00:00'),
(993, 11, 'Petak', '10:00:00', '10:30:00'),
(994, 11, 'Petak', '10:30:00', '11:00:00'),
(995, 11, 'Petak', '11:00:00', '11:30:00'),
(996, 11, 'Petak', '11:30:00', '12:00:00'),
(997, 11, 'Petak', '12:00:00', '12:30:00'),
(998, 11, 'Petak', '12:30:00', '13:00:00'),
(999, 11, 'Petak', '13:00:00', '13:30:00'),
(1000, 11, 'Petak', '13:30:00', '14:00:00'),
(1001, 11, 'Petak', '14:00:00', '14:30:00'),
(1002, 11, 'Petak', '14:30:00', '15:00:00'),
(1003, 11, 'Petak', '15:00:00', '15:30:00'),
(1004, 11, 'Petak', '15:30:00', '16:00:00'),
(1005, 11, 'Subota', '08:00:00', '08:30:00'),
(1006, 11, 'Subota', '08:30:00', '09:00:00'),
(1007, 11, 'Subota', '09:00:00', '09:30:00'),
(1008, 11, 'Subota', '09:30:00', '10:00:00'),
(1009, 11, 'Subota', '10:00:00', '10:30:00'),
(1010, 11, 'Subota', '10:30:00', '11:00:00'),
(1011, 11, 'Subota', '11:00:00', '11:30:00'),
(1012, 11, 'Subota', '11:30:00', '12:00:00'),
(1013, 11, 'Subota', '12:00:00', '12:30:00'),
(1014, 11, 'Subota', '12:30:00', '13:00:00'),
(1015, 11, 'Subota', '13:00:00', '13:30:00'),
(1016, 11, 'Subota', '13:30:00', '14:00:00'),
(1023, 20, 'Ponedeljak', '08:00:00', '08:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `veterinarian_services`
--

CREATE TABLE `veterinarian_services` (
  `id` int(10) UNSIGNED NOT NULL,
  `veterinarian_id` int(10) UNSIGNED NOT NULL,
  `service_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `veterinarian_services`
--

INSERT INTO `veterinarian_services` (`id`, `veterinarian_id`, `service_id`) VALUES
(5, 1, 1),
(6, 1, 2),
(7, 1, 3),
(8, 1, 5),
(9, 1, 10),
(10, 8, 1),
(11, 8, 2),
(12, 8, 5),
(13, 8, 7),
(14, 8, 9),
(15, 9, 1),
(16, 9, 2),
(17, 9, 6),
(18, 9, 7),
(19, 10, 3),
(20, 10, 4),
(21, 10, 8),
(22, 10, 10),
(23, 11, 2),
(24, 11, 5),
(25, 11, 7);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reservation_code` (`reservation_code`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `pet_id` (`pet_id`),
  ADD KEY `veterinarian_id` (`veterinarian_id`);

--
-- Indexes for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `veterinarian_id` (`veterinarian_id`),
  ADD KEY `pet_id` (`pet_id`),
  ADD KEY `medical_records_ibfk_1` (`appointment_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `token` (`token`);

--
-- Indexes for table `password_resets_codes`
--
ALTER TABLE `password_resets_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `code` (`code`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type_id` (`type_id`),
  ADD KEY `breed_id` (`breed_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `pet_breeds`
--
ALTER TABLE `pet_breeds`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_breed` (`type_id`,`name`);

--
-- Indexes for table `pet_owners`
--
ALTER TABLE `pet_owners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `pet_types`
--
ALTER TABLE `pet_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD UNIQUE KEY `email_3` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `veterinarians`
--
ALTER TABLE `veterinarians`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `license_number` (`license_number`);

--
-- Indexes for table `veterinarian_schedule`
--
ALTER TABLE `veterinarian_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `veterinarian_id` (`veterinarian_id`);

--
-- Indexes for table `veterinarian_services`
--
ALTER TABLE `veterinarian_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_vs` (`veterinarian_id`,`service_id`),
  ADD KEY `service_id` (`service_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=223;

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `password_resets_codes`
--
ALTER TABLE `password_resets_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `pet_breeds`
--
ALTER TABLE `pet_breeds`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `pet_owners`
--
ALTER TABLE `pet_owners`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `pet_types`
--
ALTER TABLE `pet_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `veterinarians`
--
ALTER TABLE `veterinarians`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `veterinarian_schedule`
--
ALTER TABLE `veterinarian_schedule`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1026;

--
-- AUTO_INCREMENT for table `veterinarian_services`
--
ALTER TABLE `veterinarian_services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`veterinarian_id`) REFERENCES `veterinarians` (`id`),
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  ADD CONSTRAINT `appointments_ibfk_4` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`),
  ADD CONSTRAINT `appointments_ibfk_5` FOREIGN KEY (`veterinarian_id`) REFERENCES `veterinarians` (`id`);

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medical_records_ibfk_2` FOREIGN KEY (`veterinarian_id`) REFERENCES `veterinarians` (`id`),
  ADD CONSTRAINT `medical_records_ibfk_3` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`);

--
-- Constraints for table `password_resets_codes`
--
ALTER TABLE `password_resets_codes`
  ADD CONSTRAINT `password_resets_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `pets_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `pet_types` (`id`),
  ADD CONSTRAINT `pets_ibfk_2` FOREIGN KEY (`breed_id`) REFERENCES `pet_breeds` (`id`),
  ADD CONSTRAINT `pets_ibfk_3` FOREIGN KEY (`owner_id`) REFERENCES `pet_owners` (`id`);

--
-- Constraints for table `pet_breeds`
--
ALTER TABLE `pet_breeds`
  ADD CONSTRAINT `pet_breeds_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `pet_types` (`id`);

--
-- Constraints for table `pet_owners`
--
ALTER TABLE `pet_owners`
  ADD CONSTRAINT `pet_owners_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `veterinarians`
--
ALTER TABLE `veterinarians`
  ADD CONSTRAINT `veterinarians_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `veterinarian_schedule`
--
ALTER TABLE `veterinarian_schedule`
  ADD CONSTRAINT `veterinarian_schedule_ibfk_1` FOREIGN KEY (`veterinarian_id`) REFERENCES `veterinarians` (`id`);

--
-- Constraints for table `veterinarian_services`
--
ALTER TABLE `veterinarian_services`
  ADD CONSTRAINT `veterinarian_services_ibfk_1` FOREIGN KEY (`veterinarian_id`) REFERENCES `veterinarians` (`id`),
  ADD CONSTRAINT `veterinarian_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
