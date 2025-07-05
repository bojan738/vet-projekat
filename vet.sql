-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 15, 2025 at 06:40 PM
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
(1, 1, 1, 1, '2025-06-15 09:15:00', '09:15:00', 'zakazano', 'Ima alergiju na hranu.', '2025-06-14 21:44:46', 'R123456', 13, NULL),
(2, 5, 1, 1, '2025-06-16 08:50:00', '08:50:00', 'zakazano', '', '2025-06-15 18:18:45', 'R885867', 29, NULL),
(3, 5, 1, 1, '2025-06-21 15:34:00', '15:34:00', 'zakazano', '', '2025-06-15 18:19:29', 'R954058', 29, NULL),
(4, 5, 1, 1, '2025-06-16 08:50:00', '08:50:00', 'zakazano', '', '2025-06-15 18:19:44', 'R968106', 29, NULL),
(5, 9, 1, 1, '2025-06-16 08:50:00', '08:50:00', 'zakazano', '', '2025-06-15 18:19:53', 'R615618', 29, NULL),
(6, 5, 1, 1, '2025-06-16 08:50:00', '08:50:00', 'zakazano', '', '2025-06-15 18:32:48', 'R746699', 29, 2);

-- --------------------------------------------------------

--
-- Table structure for table `appointment_attendance`
--

CREATE TABLE `appointment_attendance` (
  `id` int(10) UNSIGNED NOT NULL,
  `appointment_id` int(10) UNSIGNED NOT NULL,
  `attended` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(3, 1, 1, 1, 'Alergijka reakcija', 'Vakcina', 6000.00, '2025-06-15 15:00:23');

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
  `gender` enum('male','female','unknown') DEFAULT 'unknown',
  `type_id` int(10) UNSIGNED DEFAULT NULL,
  `breed_id` int(10) UNSIGNED DEFAULT NULL,
  `owner_id` int(10) UNSIGNED DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pets`
--

INSERT INTO `pets` (`id`, `name`, `age`, `birth_date`, `gender`, `type_id`, `breed_id`, `owner_id`, `photo`) VALUES
(1, 'Rex', NULL, '2018-05-10', 'male', 1, 1, 1, 'rex.jpg'),
(5, 'Tom', 3, '2022-06-14', 'male', 4, 7, 3, 'uploads/1749925477_Screenshot 2023-05-05 141110(1).png'),
(9, 'EEEe', 4, '2025-06-20', '', 4, 7, 3, 'images/pets/tom.jpg'),
(10, 'edo', 5, '2020-01-15', '', 4, 7, 3, 'uploads/1750000011_dog.jpg');

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
(2, 1, 'German Shepherd'),
(1, 1, 'Labrador'),
(4, 2, 'Persian'),
(3, 2, 'Siamese'),
(5, 3, 'Parrot'),
(7, 4, 'fdsf');

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
(1, 13),
(2, 14),
(3, 29);

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
(3, 'Bird'),
(2, 'Cat'),
(1, 'Dog'),
(4, 'fsdf');

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
(1, 'Vaccination', 'Vaccination service for pets', 250.00),
(2, 'Checkup', 'General health checkup', 40.00),
(3, 'Surgery', 'Minor surgical procedure', 150.00);

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
(11, 'Marko', 'Markovic', 'marko.vet@example.com', '$2y$10$J3rDhjjnGEx1i1AHESC6c.Whs5Nu.n/XjyJbPnu1iURCRpsp4Nmc2', '+381611234567', 'Ulica 1, Beograd', 1, 0, 2),
(12, 'Jelena', 'Jankovic', 'jelena.vet@example.com', 'hashed_password2', '+381619876543', 'Ulica 2, Novi Sad', 1, 0, 2),
(13, 'Petar', 'Petrovic', 'petar.owner@example.com', 'hashed_password3', '+381601112223', 'Ulica 3, Nis', 1, 0, 3),
(14, 'Ivana', 'Ivanovic', 'ivana.owner@example.com', 'hashed_password4', '+381602223334', 'Ulica 4, Kragujevac', 1, 0, 3),
(27, 'Lena', 'Jakovetic', 'nekimejl@gmail.com', '$2y$10$T0fWWOqap2F4xVS9oEhTNeXW8KOo84G8yFpN3KePBTYQIvsItBphu', '435345', 'Frefdsf 78', 1, 0, 3),
(28, 'Lena', 'Markovic', 'Aaaaa@gmail.com', '$2y$10$YMegYDu/ll8o.igGuFxCHeR3iIhPzx18qbiSSCQJRdCPlpNB6oGVO', '0626874324', 'fsdfdf 2', 1, 0, 3),
(29, 'Bojan', 'Markovic', 'recineso21@gmail.com', '$2y$10$NM8sxiurR5ptg0nXc5GC/e3TTevgK2i2091PuOea1xoT3jBTs.A6y', '31231232132', '3123213212', 1, 0, 3);

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
(1, 11, 'Small Animals', 'LIC123456', 'marko_markovic.jpg'),
(2, 12, 'Exotic Animals', 'LIC654321', 'jelena_jankovic.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `veterinarian_schedule`
--

CREATE TABLE `veterinarian_schedule` (
  `id` int(10) UNSIGNED NOT NULL,
  `veterinarian_id` int(10) UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL
) ;

--
-- Dumping data for table `veterinarian_schedule`
--

INSERT INTO `veterinarian_schedule` (`id`, `veterinarian_id`, `start_time`, `end_time`) VALUES
(2, 1, '2025-06-16 08:50:00', '2025-06-16 16:50:00'),
(3, 1, '2025-06-21 15:34:00', '2025-06-21 21:37:00');

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
(1, 1, 1),
(2, 1, 2),
(3, 2, 2),
(4, 2, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reservation_code` (`reservation_code`),
  ADD KEY `pet_id` (`pet_id`),
  ADD KEY `veterinarian_id` (`veterinarian_id`),
  ADD KEY `service_id` (`service_id`);

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
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `veterinarian_id` (`veterinarian_id`),
  ADD KEY `pet_id` (`pet_id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `appointment_attendance`
--
ALTER TABLE `appointment_attendance`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appointment_cancellations`
--
ALTER TABLE `appointment_cancellations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pet_breeds`
--
ALTER TABLE `pet_breeds`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pet_owners`
--
ALTER TABLE `pet_owners`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pet_types`
--
ALTER TABLE `pet_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `user_activations`
--
ALTER TABLE `user_activations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `veterinarians`
--
ALTER TABLE `veterinarians`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `veterinarian_schedule`
--
ALTER TABLE `veterinarian_schedule`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `veterinarian_services`
--
ALTER TABLE `veterinarian_services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`veterinarian_id`) REFERENCES `veterinarians` (`id`),
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

--
-- Constraints for table `appointment_attendance`
--
ALTER TABLE `appointment_attendance`
  ADD CONSTRAINT `appointment_attendance_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`);

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
  ADD CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`),
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
