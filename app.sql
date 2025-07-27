-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 28, 2025 at 12:51 AM
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
-- Database: `app`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'mazlan', 'mazlan97@live.com', '$2y$10$E7LpEGMOcba67PMxYq5WQuC9wdzgspNxO.cxh8UxtMXAFMMPJE/SS', '2025-07-22 10:10:22');

-- --------------------------------------------------------

--
-- Table structure for table `admin_tokens`
--

CREATE TABLE `admin_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_used_at` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `device_info` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_tokens`
--

INSERT INTO `admin_tokens` (`id`, `user_id`, `token`, `expires_at`, `created_at`, `last_used_at`, `ip_address`, `device_info`, `location`) VALUES
(4, 1, 'd9c598b0394a0b42117136d720eaca1c94e7bcd33cff8a4b334c6468d09575cc', '2025-08-22 04:38:09', '2025-07-23 10:38:09', '2025-07-23 10:38:09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `body` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `content_id`, `author`, `body`) VALUES
(1, 2, 'author', 'asd asd asd awfdaw f asfdas d asfsdfdghs odfkjaskfdm paskld asd'),
(2, 9, 'asdas', 'dasdasd');

-- --------------------------------------------------------

--
-- Table structure for table `auth_tokens`
--

CREATE TABLE `auth_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_used_at` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `device_info` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `content_id`, `comment`, `created_at`) VALUES
(1, 1, 9, 'test', '2025-07-23 10:41:00');

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

CREATE TABLE `content` (
  `id` int(11) NOT NULL,
  `content_type` enum('event','article','gallery','video') NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`id`, `content_type`, `title`, `created_at`) VALUES
(1, 'event', 'Pelancaran', '2025-07-22 01:19:34'),
(2, 'article', 'title ', '2025-07-22 01:19:56'),
(5, 'gallery', 'Pelancaran', '2025-07-22 01:27:23'),
(6, 'video', 'title ', '2025-07-22 01:32:27'),
(7, 'event', 'Astro Comet', '2025-07-22 03:05:32'),
(8, 'video', 'LAGU', '2025-07-22 03:19:09'),
(9, 'article', 'asdasd', '2025-07-22 06:11:28');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` datetime NOT NULL,
  `banner_url` varchar(2083) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `content_id`, `description`, `event_date`, `banner_url`) VALUES
(1, 1, 'pelancaran', '2025-07-18 10:18:00', ''),
(2, 7, 'asd asd asfdafsdf asgdf sdfh gdfjfgklujlu gkjhtmfum nfgu', '2025-07-30 11:05:00', 'uploads/121a4a1bf18dc402_1753153532.png');

-- --------------------------------------------------------

--
-- Table structure for table `galleries`
--

CREATE TABLE `galleries` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `galleries`
--

INSERT INTO `galleries` (`id`, `content_id`, `description`) VALUES
(1, 5, 'asd');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_images`
--

CREATE TABLE `gallery_images` (
  `id` int(11) NOT NULL,
  `gallery_id` int(11) NOT NULL,
  `image_src` varchar(2083) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery_images`
--

INSERT INTO `gallery_images` (`id`, `gallery_id`, `image_src`, `title`, `sort_order`) VALUES
(1, 1, 'uploads/1753147643_Screenshot (1).png', '1753147643_Screenshot (1).png', 0),
(2, 1, 'uploads/1753147643_Screenshot (2).png', '1753147643_Screenshot (2).png', 0),
(3, 1, 'uploads/1753147643_Screenshot (3).png', '1753147643_Screenshot (3).png', 0),
(4, 1, 'uploads/1753147643_Screenshot (4).png', '1753147643_Screenshot (4).png', 0);

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `content_id`, `created_at`) VALUES
(2, 1, 8, '2025-07-24 09:11:35'),
(3, 1, 9, '2025-07-24 09:14:19'),
(4, 2, 9, '2025-07-24 09:15:46');

-- --------------------------------------------------------

--
-- Table structure for table `sabah_dun_seats`
--

CREATE TABLE `sabah_dun_seats` (
  `code` varchar(10) NOT NULL,
  `seat` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sabah_dun_seats`
--

INSERT INTO `sabah_dun_seats` (`code`, `seat`) VALUES
('N01', 'Banggi'),
('N02', 'Bengkoka'),
('N03', 'Pitas'),
('N04', 'Tanjong Kapor'),
('N05', 'Matunggong'),
('N06', 'Bandau'),
('N07', 'Tandek'),
('N08', 'Pintasan'),
('N09', 'Tempasuk'),
('N10', 'Usukan'),
('N11', 'Kadamaian'),
('N12', 'Sulaman'),
('N13', 'Pantai Dalit'),
('N14', 'Tamparuli'),
('N15', 'Kiulu'),
('N16', 'Karambunai'),
('N17', 'Darau'),
('N18', 'Inanam'),
('N19', 'Likas'),
('N20', 'Api-Api'),
('N21', 'Luyang'),
('N22', 'Tanjung Aru'),
('N23', 'Petagas'),
('N24', 'Putatan'),
('N25', 'Kepayan'),
('N26', 'Moyog'),
('N27', 'Kawang'),
('N28', 'Pantai Manis'),
('N29', 'Bongawan'),
('N30', 'Membakut'),
('N31', 'Klias'),
('N32', 'Kuala Penyu'),
('N33', 'Lumadan'),
('N34', 'Sindumin'),
('N35', 'Kundasang'),
('N36', 'Karanaan'),
('N37', 'Paginatan'),
('N38', 'Tambunan'),
('N39', 'Bingkor'),
('N40', 'Liawan'),
('N41', 'Melalap'),
('N42', 'Kemabong'),
('N43', 'Sook'),
('N44', 'Nabawan'),
('N45', 'Sungai Sibuga'),
('N46', 'Sungai Manila'),
('N47', 'Sugut'),
('N48', 'Labuk'),
('N49', 'Gum-Gum'),
('N50', 'Sungai Sibuga'),
('N51', 'Sekong'),
('N52', 'Karamunting'),
('N53', 'Elopura'),
('N54', 'Tanjong Papat'),
('N55', 'Kuamut'),
('N56', 'Sukau'),
('N57', 'Tungku'),
('N58', 'Lahad Datu'),
('N59', 'Kunak'),
('N60', 'Sulabayan'),
('N61', 'Senallang'),
('N62', 'Bugaya'),
('N63', 'Balung'),
('N64', 'Apas'),
('N65', 'Sri Tanjung'),
('N66', 'Merotai'),
('N67', 'Tanjong Batu'),
('N68', 'Sebatik'),
('N69', 'Bandar Lama'),
('N70', 'Bukit Garam'),
('N71', 'Segama'),
('N72', 'Sungai Manila'),
('N73', 'Darvel');

-- --------------------------------------------------------

--
-- Table structure for table `sabah_ethnic_groups`
--

CREATE TABLE `sabah_ethnic_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sabah_ethnic_groups`
--

INSERT INTO `sabah_ethnic_groups` (`id`, `name`, `category`) VALUES
(1, 'Kadazan', 'Major'),
(2, 'Dusun', 'Major'),
(3, 'Rungus', 'Major'),
(4, 'Murut', 'Major'),
(5, 'Bajau', 'Major'),
(6, 'Irranun', 'Major'),
(7, 'Suluk', 'Major'),
(8, 'Brunei Malay', 'Major'),
(9, 'Lundayeh', 'Major'),
(10, 'Bisaya', 'Major'),
(11, 'Ida\'an', 'Major'),
(12, 'Sama-Bajau', 'Major'),
(13, 'Sungai', 'Major'),
(14, 'Tambanuo', 'Other Indigenous'),
(15, 'Dumpas', 'Other Indigenous'),
(16, 'Tidong', 'Other Indigenous'),
(17, 'Bagahak', 'Other Indigenous'),
(18, 'Begak', 'Other Indigenous'),
(19, 'Minokok', 'Other Indigenous'),
(20, 'Orang Sungai', 'Other Indigenous'),
(21, 'Tatana', 'Other Indigenous'),
(22, 'Labuk Kadazan', 'Other Indigenous'),
(23, 'Bonggi', 'Other Indigenous'),
(24, 'Sibutu', 'Other Indigenous'),
(25, 'Cocos Malay', 'Other Indigenous'),
(26, 'Chinese', 'Non-Indigenous'),
(27, 'Indian', 'Non-Indigenous'),
(28, 'Malay (Peninsular)', 'Non-Indigenous'),
(29, 'Bugis', 'Non-Indigenous'),
(30, 'Javanese', 'Non-Indigenous'),
(31, 'Timorese', 'Non-Indigenous'),
(32, 'Toraja', 'Non-Indigenous'),
(33, 'Filipino', 'Non-Indigenous'),
(34, 'Pakistani', 'Non-Indigenous'),
(35, 'Arab', 'Non-Indigenous');

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `name`) VALUES
(1, 'Johor'),
(2, 'Kedah'),
(3, 'Kelantan'),
(17, 'Kuala Lumpur'),
(5, 'Labuan'),
(6, 'Melaka'),
(7, 'Negeri Sembilan'),
(8, 'Pahang'),
(9, 'Penang'),
(10, 'Perak'),
(11, 'Perlis'),
(12, 'Putrajaya'),
(13, 'Sabah'),
(14, 'Sarawak'),
(15, 'Selangor'),
(16, 'Terengganu');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nric` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nric`, `email`, `username`, `password`, `created_at`) VALUES
(1, '971009126139', 'mazlan97@live.com', 'mazlan', '$2y$10$FViyRRCuK8ZqC5PHAig.Se91YAiA50nXp4QDtlN.kU27bD2al4S5q', '2025-07-24 18:57:44');

-- --------------------------------------------------------

--
-- Table structure for table `user_employment`
--

CREATE TABLE `user_employment` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_current` tinyint(1) DEFAULT 0,
  `responsibilities` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_employment`
--

INSERT INTO `user_employment` (`id`, `user_id`, `company`, `job_title`, `department`, `start_date`, `end_date`, `is_current`, `responsibilities`, `updated_at`) VALUES
(1, 1, 'Google inc', 'Engineer', '', NULL, NULL, 1, '', '2025-07-26 05:18:41');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `gender` enum('m','f') DEFAULT NULL,
  `ethnic` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `profile_pic_url` varchar(2083) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `full_name`, `gender`, `ethnic`, `phone`, `birthday`, `address1`, `address2`, `area`, `postal_code`, `city`, `state`, `profile_pic_url`, `updated_at`) VALUES
(1, 1, 'MOHD MAZLAN', 'm', 'Irranun', '', '2025-07-31', '', '', '', '', '', 'Sabah', 'uploads/profiles/225ae1c17519d7fa3fc2326ef683902e.jpg', '2025-07-27 08:50:55');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `uploaded_src` varchar(2083) DEFAULT NULL,
  `uploaded_description` text DEFAULT NULL,
  `embedded_src` varchar(2083) DEFAULT NULL,
  `embedded_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `content_id`, `uploaded_src`, `uploaded_description`, `embedded_src`, `embedded_description`) VALUES
(1, 6, NULL, '', 'https://www.youtube.com/embed/m-q_Bd4K_H0?list=RDm-q_Bd4K_H0', ''),
(2, 8, '', '', 'https://www.youtube.com/embed/m-q_Bd4K_H0?list=RDm-q_Bd4K_H0', 'asd');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `admin_tokens`
--
ALTER TABLE `admin_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_id` (`content_id`);

--
-- Indexes for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_id` (`content_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_id` (`content_id`);

--
-- Indexes for table `galleries`
--
ALTER TABLE `galleries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_id` (`content_id`);

--
-- Indexes for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gallery_id` (`gallery_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`user_id`,`content_id`),
  ADD KEY `content_id` (`content_id`);

--
-- Indexes for table `sabah_dun_seats`
--
ALTER TABLE `sabah_dun_seats`
  ADD PRIMARY KEY (`code`);

--
-- Indexes for table `sabah_ethnic_groups`
--
ALTER TABLE `sabah_ethnic_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nric` (`nric`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_employment`
--
ALTER TABLE `user_employment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_id` (`content_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_tokens`
--
ALTER TABLE `admin_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `content`
--
ALTER TABLE `content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `galleries`
--
ALTER TABLE `galleries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gallery_images`
--
ALTER TABLE `gallery_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sabah_ethnic_groups`
--
ALTER TABLE `sabah_ethnic_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_employment`
--
ALTER TABLE `user_employment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_tokens`
--
ALTER TABLE `admin_tokens`
  ADD CONSTRAINT `fk_admin_tokens_admin_id` FOREIGN KEY (`user_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD CONSTRAINT `fk_user_tokens_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_fk_content` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `galleries`
--
ALTER TABLE `galleries`
  ADD CONSTRAINT `galleries_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD CONSTRAINT `gallery_images_ibfk_1` FOREIGN KEY (`gallery_id`) REFERENCES `galleries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_fk_content` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_employment`
--
ALTER TABLE `user_employment`
  ADD CONSTRAINT `fk_employment_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `fk_profile_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
