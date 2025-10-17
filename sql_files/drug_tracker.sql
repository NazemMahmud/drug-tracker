-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: db-sql
-- Generation Time: Oct 17, 2025 at 03:31 PM
-- Server version: 9.4.0
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


--
-- Database: `drug_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drugs`
--

CREATE TABLE `drugs` (
  `id` bigint UNSIGNED NOT NULL,
  `rxcui` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `base_names` json DEFAULT NULL,
  `dose_form_group_names` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drugs`
--

INSERT INTO `drugs` (`id`, `rxcui`, `name`, `base_names`, `dose_form_group_names`, `created_at`, `updated_at`) VALUES
(1, '596928', 'duloxetine 20 MG Delayed Release Oral Capsule [Cymbalta]', '[\"duloxetine\"]', '[\"Oral Product\", \"Pill\"]', '2025-10-17 14:49:52', '2025-10-17 14:49:52'),
(2, '596932', 'duloxetine 30 MG Delayed Release Oral Capsule [Cymbalta]', '[\"duloxetine\"]', '[\"Oral Product\", \"Pill\"]', '2025-10-17 14:50:31', '2025-10-17 14:50:31'),
(3, '615186', 'duloxetine 60 MG Delayed Release Oral Capsule [Cymbalta]', '[\"duloxetine\"]', '[\"Oral Product\", \"Pill\"]', '2025-10-17 14:50:39', '2025-10-17 14:50:39'),
(4, '997484', 'fexofenadine hydrochloride 30 MG Disintegrating Oral Tablet [Allegra]', '[\"fexofenadine\"]', '[\"Oral Product\", \"Pill\", \"Disintegrating Oral Product\"]', '2025-10-17 14:50:45', '2025-10-17 14:50:45'),
(5, '261266', 'pioglitazone 15 MG Oral Tablet [Actos]', '[\"pioglitazone\"]', '[\"Oral Product\", \"Pill\"]', '2025-10-17 14:50:51', '2025-10-17 14:50:51'),
(6, '261267', 'pioglitazone 30 MG Oral Tablet [Actos]', '[\"pioglitazone\"]', '[\"Oral Product\", \"Pill\"]', '2025-10-17 14:50:57', '2025-10-17 14:50:57'),
(7, '261268', 'pioglitazone 45 MG Oral Tablet [Actos]', '[\"pioglitazone\"]', '[\"Oral Product\", \"Pill\"]', '2025-10-17 14:51:04', '2025-10-17 14:51:04'),
(8, '991063', 'dicyclomine hydrochloride 10 MG Oral Capsule [Bentyl]', '[\"dicyclomine\"]', '[\"Oral Product\", \"Pill\"]', '2025-10-17 14:51:11', '2025-10-17 14:51:11'),
(9, '991069', '2 ML dicyclomine hydrochloride 10 MG/ML Injection [Bentyl]', '[\"dicyclomine\"]', '[\"Injectable Product\"]', '2025-10-17 15:05:52', '2025-10-17 15:05:52'),
(10, '991088', 'dicyclomine hydrochloride 20 MG Oral Tablet [Bentyl]', '[\"dicyclomine\"]', '[\"Oral Product\", \"Pill\"]', '2025-10-17 15:13:37', '2025-10-17 15:13:37');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_10_16_100630_create_users_drugs_table', 2),
(5, '2025_10_17_142739_create_drugs_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Piash', 'piash@mail.com', NULL, '$2y$12$kem0XbdBzJAnjjDcugEmpu/DnKA0GLaBEo4j3/fy35O33FiZ.oQYe', NULL, '2025-10-15 00:44:33', '2025-10-15 00:44:33'),
(2, 'Piash', 'test@mail.com', NULL, '$2y$12$Iy8KVwqYCyNY12dM6Mpp4e8F1zsk9qCwseGPjeDFUOT1uekdppG7.', NULL, '2025-10-17 10:01:57', '2025-10-17 10:01:57');

-- --------------------------------------------------------

--
-- Table structure for table `users_drugs`
--

CREATE TABLE `users_drugs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `rxcui` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_drugs`
--

INSERT INTO `users_drugs` (`id`, `user_id`, `rxcui`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '596928', '2025-10-17 14:49:52', '2025-10-17 14:49:52', NULL),
(2, 1, '596932', '2025-10-17 14:50:31', '2025-10-17 14:50:31', NULL),
(3, 1, '615186', '2025-10-17 14:50:39', '2025-10-17 14:50:39', NULL),
(4, 1, '997484', '2025-10-17 14:50:45', '2025-10-17 14:50:45', NULL),
(5, 1, '261266', '2025-10-17 14:50:51', '2025-10-17 14:50:51', NULL),
(6, 1, '261267', '2025-10-17 14:50:57', '2025-10-17 14:50:57', NULL),
(7, 1, '261268', '2025-10-17 14:51:04', '2025-10-17 14:51:04', NULL),
(8, 1, '991063', '2025-10-17 14:51:11', '2025-10-17 14:51:11', NULL),
(9, 1, '991069', '2025-10-17 15:05:52', '2025-10-17 15:05:52', NULL),
(10, 1, '991088', '2025-10-17 15:13:37', '2025-10-17 15:13:37', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `drugs`
--
ALTER TABLE `drugs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `drugs_rxcui_unique` (`rxcui`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `users_drugs`
--
ALTER TABLE `users_drugs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_drugs_user_id_rxcui_unique` (`user_id`,`rxcui`),
  ADD KEY `users_drugs_rxcui_index` (`rxcui`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `drugs`
--
ALTER TABLE `drugs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users_drugs`
--
ALTER TABLE `users_drugs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users_drugs`
--
ALTER TABLE `users_drugs`
  ADD CONSTRAINT `users_drugs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;
