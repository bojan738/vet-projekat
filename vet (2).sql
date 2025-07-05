-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2025 at 04:15 PM
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
-- Database: `vet`
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
  `appointment_date` datetime NOT NULL,
  `time_slot` time DEFAULT NULL,
  `status` enum('zakazano','obavljeno','otkazano') DEFAULT 'zakazano',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `reservation_code` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `schedule_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `pet_id`, `veterinarian_id`, `service_id`, `appointment_date`, `time_slot`, `status`, `notes`, `created_at`, `reservation_code`, `user_id`, `schedule_id`) VALUES
(19, 2, 8, 2, '2025-06-20 00:00:00', '09:00:00', 'zakazano', '', '2025-06-18 20:23:04', 'RSV-101', 2, 2),
(20, 3, 10, 3, '2025-06-20 00:00:00', '10:00:00', 'zakazano', '', '2025-06-18 20:23:04', 'RSV-102', 3, 3),
(21, 4, 10, 4, '2025-06-20 00:00:00', '11:00:00', 'zakazano', '', '2025-06-18 20:23:04', 'RSV-103', 4, 4),
(22, 5, 11, 5, '2025-06-20 00:00:00', '12:00:00', 'zakazano', '', '2025-06-18 20:23:04', 'RSV-104', 5, 5),
(23, 6, 9, 6, '2025-06-20 00:00:00', '13:00:00', 'zakazano', '', '2025-06-18 20:23:04', 'RSV-105', 6, 6),
(24, 7, 9, 7, '2025-06-20 00:00:00', '14:00:00', 'zakazano', '', '2025-06-18 20:23:04', 'RSV-106', 7, 7),
(25, 8, 10, 8, '2025-06-20 00:00:00', '15:00:00', 'zakazano', '', '2025-06-18 20:23:04', 'RSV-107', 8, 8),
(26, 9, 8, 9, '2025-06-20 00:00:00', '16:00:00', 'zakazano', '', '2025-06-18 20:23:04', 'RSV-108', 9, 9),
(27, 10, 1, 10, '2025-06-20 00:00:00', '17:00:00', 'zakazano', '', '2025-06-18 20:23:04', 'RSV-109', 10, 10),
(28, 12, 9, 1, '0000-00-00 00:00:00', '08:30:00', 'zakazano', '', '2025-06-29 15:20:35', NULL, 29, 53);

-- --------------------------------------------------------

--
-- Table structure for table `appointment_attendance`
--

CREATE TABLE `appointment_attendance` (
  `id` int(10) UNSIGNED NOT NULL,
  `appointment_id` int(10) UNSIGNED NOT NULL,
  `attended` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment_attendance`
--

INSERT INTO `appointment_attendance` (`id`, `appointment_id`, `attended`) VALUES
(12, 19, 1),
(13, 20, 0),
(14, 21, 1),
(15, 22, 1),
(16, 23, 0),
(17, 24, 1),
(18, 25, 1),
(19, 26, 0),
(20, 27, 1);

-- --------------------------------------------------------

--
-- Table structure for table `appointment_cancellations`
--

CREATE TABLE `appointment_cancellations` (
  `id` int(10) UNSIGNED NOT NULL,
  `appointment_id` int(10) UNSIGNED NOT NULL,
  `cancelled_by` int(10) UNSIGNED NOT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `cancelled_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(16, 19, 8, 2, 'Opšti pregled – dobar opšti status', 'Praćenje stanja i ishrane', 2500.00, '2025-06-18 20:24:58'),
(17, 20, 10, 3, 'Povreda šape – hitan slučaj', 'Hitan tretman i previjanje', 9000.00, '2025-06-18 20:24:58'),
(18, 21, 10, 4, 'Sterilizacija ženkice', 'Hirurško odstranjivanje jajnika', 8000.00, '2025-06-18 20:24:58'),
(19, 22, 11, 5, 'Zubni kamenac', 'Čišćenje i poliranje zuba', 4500.00, '2025-06-18 20:24:58'),
(20, 23, 9, 6, 'Problemi sa disanjem – papagaj', 'Snimanje grudnog koša', 6000.00, '2025-06-18 20:24:58'),
(21, 24, 9, 7, 'Letargija', 'Laboratorijska analiza krvi', 5000.00, '2025-06-18 20:24:58'),
(22, 25, 10, 8, 'Povreda šape – hitan slučaj', 'Hitan tretman i previjanje', 9000.00, '2025-06-18 20:24:58'),
(23, 26, 8, 9, 'Nema mikročipa', 'Ugradnja mikročipa', 2000.00, '2025-06-18 20:24:58'),
(25, 27, 1, 10, 'Povreda', 'Vakcinacija', 3000.00, '2025-06-26 18:03:40'),
(26, 27, 1, 10, 'Zubni kamenac', 'Ciscenje zuba', 4500.00, '2025-06-26 18:04:06');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `reset_token` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(2, 'Mica', 3, '2021-07-10', 'zenski', 2, 3, 2, 'mica.jpg'),
(3, 'Koko', 2, '2022-08-20', 'muski', 3, 5, 3, 'koko.jpg'),
(4, 'Bela', 7, '2017-01-30', 'zenski', 4, 7, 4, 'bela.jpg'),
(5, 'Čupko', 1, '2023-05-12', 'muski', 5, 9, 5, 'cupko.jpg'),
(6, 'Nindža', 4, '2020-09-05', 'zenski', 6, 11, 6, 'nindza.jpg'),
(7, 'Zeka', 2, '2022-12-01', 'muski', 7, 13, 7, 'zeka.jpg'),
(8, 'Zlatko', 1, '2023-11-10', 'muski', 8, 15, 8, 'zlatko.jpg'),
(9, 'Zmijko', 6, '2018-02-22', 'zenski', 9, 17, 9, 'zmijko.jpg'),
(10, 'Praseko', 3, '2021-04-14', 'muski', 10, 19, 10, 'praseko.jpg'),
(12, 'tom', 0, '2020-07-23', '', 2, 3, 11, 'uploads/1750277707_tom.jpg'),
(13, 'PERO', 100, '1925-01-02', '', 6, 11, 11, 'uploads/1750279908_rex.jpg');

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
(11, 29);

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
  `role_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `phone_number`, `address`, `active`, `negative_points`, `role_id`) VALUES
(1, 'Andrej', 'Nikolic', 'andrej@example.com', '$2b$12$PuWGpkltREZ5TE5tGEPIFOauRjqrHfMubXcxSfeeQEzO/EfDogSwO', '+381601112233', 'Adresa 40', 1, 0, 3),
(2, 'Marija', 'Jovic', 'marija@example.com', '$2b$12$iVsCuXxIMCodbcjffZ4sJ.qX358zuGQ4QOSb8Rfik1VKyB58OVfj6', '+381601112233', 'Adresa 41', 1, 0, 3),
(3, 'Nemanja', 'Kovacevic', 'nemanja@example.com', '$2b$12$Sd4SnI6oZreNoQpyoiY3R.rVtGxf/FRpSRxnHocF.5niN0BK/TQGO', '+381601112233', 'Adresa 42', 1, 1, 3),
(4, 'Jelena', 'Petrovic', 'jelena@example.com', '$2b$12$jWKt3QiIXy3.PhPZc0I0jO8HJiY4nodXjrArgtZ6DobzlHROq7Yf.', '+381601112233', 'Adresa 43', 1, 0, 2),
(5, 'Vuk', 'Stankovic', 'vuk@example.com', '$2b$12$ItSGl53Xn1SlGb.uFzhy/u0gFveCO3.TAKfxuYzgis/OJ/mP68SS6', '+381601112233', 'Adresa 44', 1, 0, 2),
(6, 'Ivana', 'Milosevic', 'ivana@example.com', '$2b$12$eAyITHL6hMT5gkzGcVvgde2LNpokqCS.QqZa9t97cVMYJzbxn7xNe', '+381601112233', 'Adresa 45', 1, 1, 3),
(7, 'Bojan', 'Zdravkovic', 'bojan@example.com', '$2b$12$in2hI9rT57gWjKDwr50VSeZHGCFkacKdf3bx5wo.cuiRiYCJBjK7q', '+381601112233', 'Adresa 46', 1, 0, 3),
(8, 'Katarina', 'Savic', 'katarina@example.com', '$2b$12$213j2JXX0VuQ576PeGNaNeOBXu8Aa0bwSGDpHqeGfaKpwncNknWdu', '+381601112233', 'Adresa 47', 1, 0, 3),
(9, 'Nikola', 'Todorovic', 'nikola@example.com', '$2b$12$pAH10dq3nGG5oXuX.Yx/GOgA6fvvWDnE28t6DKDmcbZ27ehdt2rU2', '+381601112233', 'Adresa 48', 1, 1, 3),
(10, 'Milica', 'Antic', 'milica@example.com', '$2b$12$jqGAIeLZtyxUG7EsduBkLuTN6j2H2LRydMibWJg6OUTF/Z0ge22ce', '+381601112233', 'Adresa 49', 1, 0, 3),
(11, 'Marko', 'Markovic', 'marko.vet@example.com', '$2y$10$J3rDhjjnGEx1i1AHESC6c.Whs5Nu.n/XjyJbPnu1iURCRpsp4Nmc2', '+381611234567', 'Ulica 1, Beograd', 1, 0, 2),
(29, 'Marko', 'Markovic', 'recineso21@gmail.com', '$2y$10$NM8sxiurR5ptg0nXc5GC/e3TTevgK2i2091PuOea1xoT3jBTs.A6y', '31231232132', '3123213212', 1, 0, 3),
(30, 'Ana', 'Vetković', 'vet1@petcare.com', '$2b$12$ZFDCVTNyR8d9VOR7327p5O4t1rtxo6IdFCZ2i.9I5K/zDLn0KoELq', '+381601112234', 'Vet Adresa 1', 1, 0, 2),
(31, 'Ivan', 'Lazarević', 'vet2@petcare.com', '$2b$12$mYYuhX4Teg41zPHqWNusq.qr3mPCX6xJVQI6YLmUDbQo2V7Jjlt02', '+381601112235', 'Vet Adresa 2', 1, 0, 2),
(32, 'Maja', 'Bogdanović', 'vet3@petcare.com', '$2b$12$ajxseIf8zQB0Bf5hvIwniex0DloqWarUhtcm3N8ePsLp2JlR45Wha', '+381601112236', 'Vet Adresa 3', 1, 0, 2),
(33, 'Stefan', 'Jovanović', 'vet4@petcare.com', '$2b$12$7ssCNutSzqtmv95AABBaLuvYW3O25vskOwRSkctYktFiBrnEeFcsi', '+381601112237', 'Vet Adresa 4', 1, 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_activations`
--

CREATE TABLE `user_activations` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `activation_code` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 11, 'Veliki kucni ljubimci', 'LIC123456', 'vet1.jpg'),
(8, 30, 'Mali kućni ljubimci', 'LIC300001', 'ana_vetkovic.jpg'),
(9, 31, 'Egzotične životinje', 'LIC300002', 'vet1.jpg'),
(10, 32, 'Hirurgija', 'LIC300003', 'maja_bogdanovic.jpg'),
(11, 33, 'Dermatologija', 'LIC300004', 'vet1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `veterinarian_schedule`
--

CREATE TABLE `veterinarian_schedule` (
  `id` int(10) UNSIGNED NOT NULL,
  `veterinarian_id` int(10) UNSIGNED NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ;

--
-- Dumping data for table `veterinarian_schedule`
--

INSERT INTO `veterinarian_schedule` (`id`, `veterinarian_id`, `start_time`, `end_time`) VALUES
(20, 1, '08:00:00', '08:30:00'),
(21, 1, '08:30:00', '09:00:00'),
(22, 1, '09:00:00', '09:30:00'),
(23, 1, '09:30:00', '10:00:00'),
(24, 1, '10:00:00', '10:30:00'),
(25, 1, '10:30:00', '11:00:00'),
(26, 1, '11:00:00', '11:30:00'),
(27, 1, '11:30:00', '12:00:00'),
(28, 1, '12:00:00', '12:30:00'),
(29, 1, '12:30:00', '13:00:00'),
(30, 1, '13:00:00', '13:30:00'),
(31, 1, '13:30:00', '14:00:00'),
(32, 1, '14:00:00', '14:30:00'),
(33, 1, '14:30:00', '15:00:00'),
(34, 1, '15:00:00', '15:30:00'),
(35, 1, '15:30:00', '16:00:00'),
(36, 8, '08:00:00', '08:30:00'),
(37, 8, '08:30:00', '09:00:00'),
(38, 8, '09:00:00', '09:30:00'),
(39, 8, '09:30:00', '10:00:00'),
(40, 8, '10:00:00', '10:30:00'),
(41, 8, '10:30:00', '11:00:00'),
(42, 8, '11:00:00', '11:30:00'),
(43, 8, '11:30:00', '12:00:00'),
(44, 8, '12:00:00', '12:30:00'),
(45, 8, '12:30:00', '13:00:00'),
(46, 8, '13:00:00', '13:30:00'),
(47, 8, '13:30:00', '14:00:00'),
(48, 8, '14:00:00', '14:30:00'),
(49, 8, '14:30:00', '15:00:00'),
(50, 8, '15:00:00', '15:30:00'),
(51, 8, '15:30:00', '16:00:00'),
(52, 9, '08:00:00', '08:30:00'),
(53, 9, '08:30:00', '09:00:00'),
(54, 9, '09:00:00', '09:30:00'),
(55, 9, '09:30:00', '10:00:00'),
(56, 9, '10:00:00', '10:30:00'),
(57, 9, '10:30:00', '11:00:00'),
(58, 9, '11:00:00', '11:30:00'),
(59, 9, '11:30:00', '12:00:00'),
(60, 9, '12:00:00', '12:30:00'),
(61, 9, '12:30:00', '13:00:00'),
(62, 9, '13:00:00', '13:30:00'),
(63, 9, '13:30:00', '14:00:00'),
(64, 9, '14:00:00', '14:30:00'),
(65, 9, '14:30:00', '15:00:00'),
(66, 9, '15:00:00', '15:30:00'),
(67, 9, '15:30:00', '16:00:00'),
(68, 10, '12:00:00', '12:30:00'),
(69, 10, '12:30:00', '13:00:00'),
(70, 10, '13:00:00', '13:30:00'),
(71, 10, '13:30:00', '14:00:00'),
(72, 10, '14:00:00', '14:30:00'),
(73, 10, '14:30:00', '15:00:00'),
(74, 10, '15:00:00', '15:30:00'),
(75, 10, '15:30:00', '16:00:00'),
(76, 10, '16:00:00', '16:30:00'),
(77, 10, '16:30:00', '17:00:00'),
(78, 10, '17:00:00', '17:30:00'),
(79, 10, '17:30:00', '18:00:00'),
(80, 10, '18:00:00', '18:30:00'),
(81, 10, '18:30:00', '19:00:00'),
(82, 10, '19:00:00', '19:30:00'),
(83, 10, '19:30:00', '20:00:00'),
(84, 11, '12:00:00', '12:30:00'),
(85, 11, '12:30:00', '13:00:00'),
(86, 11, '13:00:00', '13:30:00'),
(87, 11, '13:30:00', '14:00:00'),
(88, 11, '14:00:00', '14:30:00'),
(89, 11, '14:30:00', '15:00:00'),
(90, 11, '15:00:00', '15:30:00'),
(91, 11, '15:30:00', '16:00:00'),
(92, 11, '16:00:00', '16:30:00'),
(93, 11, '16:30:00', '17:00:00'),
(94, 11, '17:00:00', '17:30:00'),
(95, 11, '17:30:00', '18:00:00'),
(96, 11, '18:00:00', '18:30:00'),
(97, 11, '18:30:00', '19:00:00'),
(98, 11, '19:00:00', '19:30:00'),
(99, 11, '19:30:00', '20:00:00');

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
  ADD KEY `veterinarian_id` (`veterinarian_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `appointments_ibfk_1` (`pet_id`);

--
-- Indexes for table `appointment_attendance`
--
ALTER TABLE `appointment_attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `appointment_cancellations`
--
ALTER TABLE `appointment_cancellations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `appointment_id` (`appointment_id`),
  ADD KEY `cancelled_by` (`cancelled_by`);

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
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `user_activations`
--
ALTER TABLE `user_activations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `appointment_attendance`
--
ALTER TABLE `appointment_attendance`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `appointment_cancellations`
--
ALTER TABLE `appointment_cancellations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `pet_breeds`
--
ALTER TABLE `pet_breeds`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `pet_owners`
--
ALTER TABLE `pet_owners`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `user_activations`
--
ALTER TABLE `user_activations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `veterinarians`
--
ALTER TABLE `veterinarians`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `veterinarian_schedule`
--
ALTER TABLE `veterinarian_schedule`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

--
-- Constraints for table `appointment_attendance`
--
ALTER TABLE `appointment_attendance`
  ADD CONSTRAINT `appointment_attendance_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `appointment_cancellations`
--
ALTER TABLE `appointment_cancellations`
  ADD CONSTRAINT `appointment_cancellations_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`),
  ADD CONSTRAINT `appointment_cancellations_ibfk_2` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medical_records_ibfk_2` FOREIGN KEY (`veterinarian_id`) REFERENCES `veterinarians` (`id`),
  ADD CONSTRAINT `medical_records_ibfk_3` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

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
-- Constraints for table `user_activations`
--
ALTER TABLE `user_activations`
  ADD CONSTRAINT `user_activations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

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
