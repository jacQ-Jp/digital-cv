-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 10, 2026 at 03:21 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `CV`
--

-- --------------------------------------------------------

--
-- Table structure for table `cvs`
--

CREATE TABLE `cvs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `headline` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(255) DEFAULT NULL,
  `contact_location` varchar(255) DEFAULT NULL,
  `contact_social` varchar(255) DEFAULT NULL,
  `languages_text` text DEFAULT NULL,
  `template_slug` varchar(255) NOT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cvs`
--

INSERT INTO `cvs` (`id`, `user_id`, `title`, `summary`, `photo_path`, `headline`, `contact_phone`, `contact_location`, `contact_social`, `languages_text`, `template_slug`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 'Mwah', 'aku emm', NULL, NULL, NULL, NULL, NULL, NULL, 'default', 'published', '2026-04-08 02:01:36', '2026-04-08 02:01:36'),
(2, 2, 'cdcsd', 'dscs', NULL, NULL, NULL, NULL, NULL, NULL, 'sidebar', 'draft', '2026-04-08 02:06:46', '2026-04-08 02:06:46'),
(3, 2, 'sfsf', 'sfsf', NULL, 'sfs', '2342342424', 'sfwfef', 'eferg', 'grrg', 'sidebar', 'published', '2026-04-08 02:26:58', '2026-04-08 02:26:58');

-- --------------------------------------------------------

--
-- Table structure for table `educations`
--

CREATE TABLE `educations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cv_id` bigint(20) UNSIGNED NOT NULL,
  `school` varchar(255) NOT NULL,
  `degree` varchar(255) NOT NULL,
  `year` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `educations`
--

INSERT INTO `educations` (`id`, `cv_id`, `school`, `degree`, `year`, `created_at`, `updated_at`) VALUES
(1, 1, 'aa', 'dd', '1222', '2026-04-08 02:06:02', '2026-04-08 02:06:02'),
(2, 2, 'dscsdc', 'sccs', '132', '2026-04-08 02:07:30', '2026-04-08 02:07:30');

-- --------------------------------------------------------

--
-- Table structure for table `experiences`
--

CREATE TABLE `experiences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cv_id` bigint(20) UNSIGNED NOT NULL,
  `company` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `experiences`
--

INSERT INTO `experiences` (`id`, `cv_id`, `company`, `position`, `start_date`, `end_date`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'SMK Negeri 1 Bantul', 'member', '2026-04-05', '2026-04-22', 'yaya', '2026-04-08 02:05:46', '2026-04-08 02:05:46'),
(2, 2, 'scd', 'sdcdc', '1231-12-22', '1331-03-12', 'sdvcsdfv', '2026-04-08 02:07:17', '2026-04-08 02:07:17');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_04_07_013934_create_roles_table', 1),
(2, '2026_04_07_013935_create_users_table', 1),
(3, '2026_04_07_013944_create_templates_table', 1),
(4, '2026_04_07_013957_create_cvs_table', 1),
(5, '2026_04_07_014014_create_experiences_table', 1),
(6, '2026_04_07_014021_create_educations_table', 1),
(7, '2026_04_08_000001_create_skills_table', 1),
(8, '2026_04_08_000002_add_photo_path_to_cvs_table', 2),
(9, '2026_04_08_000003_add_profile_fields_to_cvs_table', 3),
(10, '2026_04_09_000000_create_skills_table', 4);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'User', 'user', '2026-04-08 01:32:05', '2026-04-08 01:32:05'),
(2, 'Admin', 'admin', '2026-04-08 01:32:05', '2026-04-08 01:32:05');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1oOPDONxCGZ3BSWjBFcFD4nhPGtBsuktGJN6nPMS', NULL, '127.0.0.1', 'curl/8.7.1', 'eyJfdG9rZW4iOiJ1U3F4d3VnYTEzUzh5amhaRUhUMk9YTHVYRE5RaEowWTRtMXIzRTA5IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2Rhc2hib2FyZCJ9LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2Rhc2hib2FyZCIsInJvdXRlIjoiZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1775638653),
('D7WjJJdEXCdOfAJt4fy65lh9FW9NbpxijGRogbyi', 2, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJQSzF3UVRVTE5QTlJBTDcwN3pHUlVuTXA4MHFnMFBsWkJzcVh1Z1Q3IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2N2c1wvMVwvcHJldmlldyIsInJvdXRlIjoiY3ZzLnByZXZpZXcuc2hvdyJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MiwiY3ZfdGVtcGxhdGVfc2x1ZyI6InNpZGViYXIifQ==', 1775641262),
('HfTNnmlOdjtgw7YKdCaoV4WRAO7WXggliMC8Dk86', NULL, '127.0.0.1', 'curl/8.7.1', 'eyJfdG9rZW4iOiJFNDRwYjBqSXI1SUYyN0s5ekxRMjFiQ0JrSWJCRmhTQUJDZlNIT3VhIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9jdlwvMiIsInJvdXRlIjoiY3ZzLnB1YmxpYy5zaG93In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1775639541),
('hOogZljE3r2Sdtxb15P7msP7s0wUKjxl7xkHBYql', NULL, '127.0.0.1', 'curl/8.7.1', 'eyJfdG9rZW4iOiJZeFVucmJVMmNCQzREb29Ld0o1b1dieUJxNDZIZWFpMFlWVnZlZlp5IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2N2c1wvMSJ9LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2N2c1wvMSIsInJvdXRlIjoiY3ZzLnNob3cifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1775638959),
('Ru55EJd87gmNKqGcJFADjfW2oDBAcJClL1uocWHH', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.4 Safari/605.1.15', 'eyJfdG9rZW4iOiJINElHQVQ4bkZsemg5eVJFMmxqanA0aUNCa1JXckN0UHd6YTg1Qjh5IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9yZWdpc3RlciIsInJvdXRlIjoicmVnaXN0ZXIifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJ1cmwiOnsiaW50ZW5kZWQiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvY3ZzIn19', 1775783909),
('sjUyoFNLjk8OQBcIXwf3Ij8oZ957CcgF9o40JeG2', 2, '127.0.0.1', 'curl/8.7.1', 'eyJfdG9rZW4iOiJLd2JKZHMzSjJzVTJxc2pmS0pFdEVTYldvcWFZSFB0c2ludXF6bnAwIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9jdnNcLzEiLCJyb3V0ZSI6ImN2cy5zaG93In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjJ9', 1775638964),
('XscEZ6BjvfTJk5wLwRFA7Fpzdo4XOF2LUYEp079s', NULL, '127.0.0.1', 'curl/8.7.1', 'eyJfdG9rZW4iOiI0Nm9nVkZvV3J3UWJ3UjRNMml6cTFDWVFnMDltV0xZbmo1eVRDN3phIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1775637362),
('ZvfXumXII7g5rwPX9fCP3fTqEW3NARy7zLEE16M6', NULL, '127.0.0.1', 'curl/8.7.1', 'eyJfdG9rZW4iOiJOejQwbkNneVphU3FOS2VzN2lpN2tDVkhkQ2MwRnVzdXlib2RsMHVmIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1775637358);

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cv_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `level` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `cv_id`, `name`, `level`, `created_at`, `updated_at`) VALUES
(1, 1, 'waw', 'beginner', '2026-04-08 02:06:17', '2026-04-08 02:06:17'),
(2, 2, 'adcd', 'ada', '2026-04-08 02:07:40', '2026-04-08 02:07:40');

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE `templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`id`, `name`, `slug`, `description`, `thumbnail`, `is_active`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'Default', 'default', 'Clean & readable. Cocok untuk semua profesi.', '/images/templates/default.svg', 1, 1, '2026-04-08 01:32:05', '2026-04-08 01:45:15'),
(2, 'Modern', 'modern', 'Dark mode, kartu modern, fokus pada highlight.', '/images/templates/modern.svg', 1, 0, '2026-04-08 01:32:05', '2026-04-08 01:45:15'),
(3, 'Minimal', 'minimal', 'Tipografi minimal, ringkas, ATS-friendly.', '/images/templates/minimal.svg', 1, 0, '2026-04-08 01:45:15', '2026-04-08 01:45:15'),
(4, 'Classic', 'classic', 'Gaya formal klasik, header tegas, struktur rapi.', '/images/templates/classic.svg', 1, 0, '2026-04-08 01:45:15', '2026-04-08 01:45:15'),
(5, 'Sidebar', 'sidebar', 'Layout 2 kolom dengan sidebar untuk kontak & skills.', '/images/templates/sidebar.svg', 1, 0, '2026-04-08 01:45:15', '2026-04-08 01:45:15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@example.com', NULL, '$2y$12$o.I01wA2WAgarne/oCBYK.PF0a6n9x.mrT4fTK5Mz5UksVpjEDfpW', 2, NULL, '2026-04-08 01:32:05', '2026-04-08 01:32:05'),
(2, 'Test User', 'test@example.com', NULL, '$2y$12$CP0CHhLZVAK0WGQ5LE0c0eglbn5ldZicxeKwJ6DVf75xDWUHMJ/eK', 1, '1ESTmPMBbTcq1RtBeZ6hQLcQhZMR4lyNiyCt7knWGKnef4spcMlwg78d1BsT', '2026-04-08 01:32:05', '2026-04-08 01:32:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cvs`
--
ALTER TABLE `cvs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cvs_user_id_foreign` (`user_id`),
  ADD KEY `cvs_template_slug_foreign` (`template_slug`);

--
-- Indexes for table `educations`
--
ALTER TABLE `educations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `educations_cv_id_foreign` (`cv_id`);

--
-- Indexes for table `experiences`
--
ALTER TABLE `experiences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `experiences_cv_id_foreign` (`cv_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `skills_cv_id_foreign` (`cv_id`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `templates_slug_unique` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cvs`
--
ALTER TABLE `cvs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `educations`
--
ALTER TABLE `educations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `experiences`
--
ALTER TABLE `experiences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cvs`
--
ALTER TABLE `cvs`
  ADD CONSTRAINT `cvs_template_slug_foreign` FOREIGN KEY (`template_slug`) REFERENCES `templates` (`slug`) ON DELETE CASCADE,
  ADD CONSTRAINT `cvs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `educations`
--
ALTER TABLE `educations`
  ADD CONSTRAINT `educations_cv_id_foreign` FOREIGN KEY (`cv_id`) REFERENCES `cvs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `experiences`
--
ALTER TABLE `experiences`
  ADD CONSTRAINT `experiences_cv_id_foreign` FOREIGN KEY (`cv_id`) REFERENCES `cvs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `skills`
--
ALTER TABLE `skills`
  ADD CONSTRAINT `skills_cv_id_foreign` FOREIGN KEY (`cv_id`) REFERENCES `cvs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
