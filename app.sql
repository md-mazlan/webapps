-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 16, 2025 at 01:02 PM
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
-- Table structure for table `account_deletion_requests`
--

CREATE TABLE `account_deletion_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `requested_at` datetime NOT NULL,
  `reviewed` tinyint(1) DEFAULT 0,
  `reviewed_at` datetime DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `decision` varchar(20) DEFAULT NULL,
  `response` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `active`, `created_at`) VALUES
(1, 'mazlan', 'mazlan97@live.com', '$2y$10$E7LpEGMOcba67PMxYq5WQuC9wdzgspNxO.cxh8UxtMXAFMMPJE/SS', 1, '2025-07-22 10:10:22'),
(2, 'admin', 'admin@dot.com', '$2y$10$7GKhxwUPDgjeFGs4sdRNx.oWU.6OinxQS9.Q/W9pXVMq9/8OhsFjK', 1, '2025-08-16 18:41:04');

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
-- Table structure for table `billplz_payment`
--

CREATE TABLE `billplz_payment` (
  `id` varchar(32) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `collection_id` varchar(32) DEFAULT NULL,
  `paid` tinyint(1) DEFAULT NULL,
  `state` varchar(20) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `paid_amount` int(11) DEFAULT NULL,
  `due_at` date DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mobile` varchar(30) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `reference_1_label` varchar(100) DEFAULT NULL,
  `reference_1` varchar(100) DEFAULT NULL,
  `reference_2_label` varchar(100) DEFAULT NULL,
  `reference_2` varchar(100) DEFAULT NULL,
  `redirect_url` varchar(255) DEFAULT NULL,
  `callback_url` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billplz_payment`
--

INSERT INTO `billplz_payment` (`id`, `user_id`, `collection_id`, `paid`, `state`, `amount`, `paid_amount`, `due_at`, `email`, `mobile`, `name`, `url`, `reference_1_label`, `reference_1`, `reference_2_label`, `reference_2`, `redirect_url`, `callback_url`, `description`, `paid_at`) VALUES
('08d339b032b801fd', 1, 'u121pgx5', 0, 'due', 100, 0, '2025-08-14', 'mazlan97@live.com', NULL, 'MOHD MAZLAN BIN ABDUL MANAN', 'https://www.billplz-sandbox.com/bills/08d339b032b801fd', 'User ID', '1', 'Reference 2', NULL, 'http://localhost/webapps/redirect.php', 'http://localhost/webapps/callback.php', 'PAYMENT DESCRIPTION', '2025-08-14 14:14:18'),
('2674cbffa93c327a', 9, 'u121pgx5', 1, 'paid', 100, 100, '2025-08-14', 'mazlan3@live.com', '+60107896572', 'MOHD MAZLAN BIN ABDUL MANAN', 'https://www.billplz-sandbox.com/bills/2674cbffa93c327a', 'Reference 1', NULL, 'Reference 2', NULL, 'http://192.168.0.133/webapps/redirect.php', 'http://192.168.0.133/webapps/callback.php', 'PAYMENT DESCRIPTION', '2025-08-14 15:51:02'),
('40d06663f17ccbeb', 9, 'u121pgx5', 1, 'paid', 100, 100, '2025-08-14', 'mazlan3@live.com', '+60107896572', 'MOHD MAZLAN BIN ABDUL MANAN', 'https://www.billplz-sandbox.com/bills/40d06663f17ccbeb', 'Reference 1', NULL, 'Reference 2', NULL, 'http://192.168.0.133/webapps/redirect.php', 'http://192.168.0.133/webapps/callback.php', 'PAYMENT DESCRIPTION', '2025-08-14 15:45:26'),
('82b415f28268f059', 1, 'u121pgx5', 1, 'paid', 100, 100, '2025-08-14', 'mazlan97@live.com', NULL, 'MOHD MAZLAN BIN ABDUL MANAN', 'https://www.billplz-sandbox.com/bills/82b415f28268f059', 'User ID', '1', 'Reference 2', NULL, 'http://localhost/webapps/redirect.php', 'http://localhost/webapps/callback.php', 'PAYMENT DESCRIPTION', '2025-08-14 19:26:57');

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
(5, 'gallery', 'Galeri', '2025-07-22 01:27:23'),
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
-- Table structure for table `inbox`
--

CREATE TABLE `inbox` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sender` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inbox`
--

INSERT INTO `inbox` (`id`, `user_id`, `sender`, `subject`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 'System', 'Welcome!', 'Welcome to your inbox. This is a dummy message.', 0, '2025-08-16 12:29:51'),
(2, 1, 'Admin', 'Test Message', 'This is a test message for user 1.', 1, '2025-08-15 12:29:51'),
(3, 1, 'Support', 'Need Help?', 'Contact us anytime for support.', 0, '2025-08-14 12:29:51');

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
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `published_at` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `body`, `image_url`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 'News', 'asd asdasd a\r\nwfafawd', 'uploads/news/news_68a02c2e2e64d4.14175028.jpg', '2025-08-16 14:58:00', '2025-08-16 14:58:54', '2025-08-16 15:21:06');

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
(35, 'Arab', 'Non-Indigenous'),
(36, 'Kedayan', 'Major');

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
(1, '971009126123', 'mazlan97@live.com', 'mazlan', '$2y$10$FViyRRCuK8ZqC5PHAig.Se91YAiA50nXp4QDtlN.kU27bD2al4S5q', '2025-07-24 18:57:44'),
(9, '971009136123', 'mazlan3@live.com', '', '$2y$10$LKSBOo0J01FEwPbeMZ8PQunCmxCv79Wbd1PvW1w8WsS66Xsh7GSGy', '2025-08-07 22:15:01'),
(27, '971009146123', 'mazlan4@live.com', 'Mazlan', '$2y$10$EYIF.ulISIjPEpF3enJL2eYADbrzO5S/uOuuHd5sZl.Ui7w3W.s8W', '2025-08-07 22:45:53'),
(28, '971009156123', 'mazlan5@live.com', 'mazlan', '$2y$10$v60g9Any4BZBYA86U8AO5ODFo1VXvHsaDRhNd0CD.cit4n9DcAGd.', '2025-08-07 23:18:01'),
(29, '870314145261', 'syarezanzbnghsb@gmail.com', 'SYAREZAN SAMAT', '$2y$10$.SVmueZc5DSqeeGiCNmO4OpY5ZJHPHtSj5pEAnvPl10RcTEuCjaLO', '2025-08-14 21:24:31'),
(30, '870513145440', 'angkatanbersatusabah@gmail.com', 'WAN NURUL AIMI', '$2y$10$qLS/fVo2GUOA/8ij/Jz7D.JJBcUeUEysgakOAdFGBQturjLQjHedu', '2025-08-14 21:53:29');

-- --------------------------------------------------------

--
-- Table structure for table `user_employment`
--

CREATE TABLE `user_employment` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `employment` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `employer_name` varchar(255) NOT NULL,
  `company_address` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_employment`
--

INSERT INTO `user_employment` (`id`, `user_id`, `employment`, `position`, `employer_name`, `company_address`, `updated_at`) VALUES
(1, 1, 'Public', 'asdasd', 'asdasda', 'asdasd', '2025-08-13 23:18:14'),
(2, 9, '', '', '', '', '2025-08-13 07:47:45'),
(3, 27, '', '', '', '', '2025-08-07 14:45:53'),
(4, 28, '', '', '', '', '2025-08-08 13:44:38'),
(5, 29, 'Private', 'CSO', 'ZBN SDN BHD', 'Lot No R-10-03A, Block B, Level Tenth, Riverson Suites, 88100, Kota Kinabalu, Sabah.', '2025-08-14 20:26:30'),
(6, 30, 'Public', 'PEGAWAI TADBIR', 'JABATAN AKAUNTAN NEGERI SABAH', '', '2025-08-14 20:56:47');

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
  `voting_area` varchar(255) DEFAULT NULL,
  `service_area` varchar(255) DEFAULT NULL,
  `vest_size` varchar(10) DEFAULT NULL,
  `profile_pic_url` varchar(2083) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `full_name`, `gender`, `ethnic`, `phone`, `birthday`, `address1`, `address2`, `area`, `postal_code`, `city`, `state`, `voting_area`, `service_area`, `vest_size`, `profile_pic_url`, `updated_at`) VALUES
(1, 1, 'MOHD MAZLAN BIN ABDUL MANAN', 'm', 'Irranun', '010123123123', '2025-08-29', 'AWDAWF', '', '', '', '', NULL, 'N70', 'N70', 'XXXL', 'uploads/profiles/225ae1c17519d7fa3fc2326ef683902e.jpg', '2025-08-13 23:16:41'),
(2, 9, 'mohd mazlan bin abdul manan', 'm', '', '0107896572', NULL, 'SEPANGGAR\r\nseri maju', '', '', '', '', NULL, '', '', '', NULL, '2025-08-14 07:32:48'),
(3, 27, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-07 14:45:53'),
(4, 28, 'MOHD MAZLAN', 'm', '', '', NULL, 'ASD\r\nASD', '', '', '', '', NULL, '', '', '', NULL, '2025-08-14 06:33:08'),
(5, 29, 'MOHD SYAREZAN BIN ABDUL SAMAT', 'm', 'Bajau', '014-6790168', '1987-03-14', '', '', '', '', '', NULL, 'N12', 'N12', 'L', NULL, '2025-08-14 20:25:54'),
(6, 30, 'WAN NURUL AIMI', 'f', 'Sama-Bajau', '0146790168', '1987-05-13', 'Kota kinabalu', '', '', '', '', NULL, 'N12', 'N21', 'M', NULL, '2025-08-14 20:56:15');

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE `vendor` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `discount` text DEFAULT NULL,
  `offering` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor`
--

INSERT INTO `vendor` (`id`, `name`, `location`, `discount`, `offering`, `created_at`) VALUES
(1, 'FreshMart', 'Kota Kinabalu, Sabah', '10% off', 'Fresh fruits and vegetables', '2025-08-16 16:20:40'),
(2, 'Techie Gadgets', 'Sandakan, Sabah', 'RM50 voucher', 'Latest electronics and gadgets', '2025-08-15 16:20:40'),
(3, 'Batik Boutique', 'Tawau, Sabah', '15% off', 'Handmade batik clothing', '2025-08-14 16:20:40'),
(4, 'Coffee Corner', 'Lahad Datu, Sabah', 'Buy 1 Free 1', 'Specialty coffee and pastries', '2025-08-13 16:20:40'),
(5, 'Book Haven', 'Keningau, Sabah', '20% off', 'Books and stationery', '2025-08-12 16:20:40');

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
(1, 6, NULL, '', 'https://www.youtube.com/embed/ruEJCHrh9a0?si=Kn6NdfiFyOnkEipU', ''),
(2, 8, '', '', 'https://www.youtube.com/embed/m-q_Bd4K_H0?list=RDm-q_Bd4K_H0https://www.youtube.com/embed/ruEJCHrh9a0?si=Kn6NdfiFyOnkEipUhttps://www.youtube.com/embed/QYGdLMf9hzM?si=448_68iyB2fxNHRA', 'asd');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_deletion_requests`
--
ALTER TABLE `account_deletion_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- Indexes for table `billplz_payment`
--
ALTER TABLE `billplz_payment`
  ADD PRIMARY KEY (`id`),
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
-- Indexes for table `inbox`
--
ALTER TABLE `inbox`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`user_id`,`content_id`),
  ADD KEY `content_id` (`content_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

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
  ADD UNIQUE KEY `email` (`email`);

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
-- Indexes for table `vendor`
--
ALTER TABLE `vendor`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `account_deletion_requests`
--
ALTER TABLE `account_deletion_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin_tokens`
--
ALTER TABLE `admin_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
-- AUTO_INCREMENT for table `inbox`
--
ALTER TABLE `inbox`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sabah_ethnic_groups`
--
ALTER TABLE `sabah_ethnic_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `user_employment`
--
ALTER TABLE `user_employment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `vendor`
--
ALTER TABLE `vendor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account_deletion_requests`
--
ALTER TABLE `account_deletion_requests`
  ADD CONSTRAINT `account_deletion_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

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
-- Constraints for table `billplz_payment`
--
ALTER TABLE `billplz_payment`
  ADD CONSTRAINT `billplz_payment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

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
-- Constraints for table `inbox`
--
ALTER TABLE `inbox`
  ADD CONSTRAINT `inbox_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

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
