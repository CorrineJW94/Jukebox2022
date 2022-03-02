-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 03, 2017 at 08:00 PM
-- Server version: 5.7.17
-- PHP Version: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shuffle`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_playlist`
--

CREATE TABLE `admin_playlist` (
  `id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `played` varchar(1) COLLATE utf8_unicode_ci DEFAULT 'N',
  `playlist_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `albums`
--

CREATE TABLE `albums` (
  `id` int(11) NOT NULL,
  `album` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `artists`
--

CREATE TABLE `artists` (
  `id` int(11) NOT NULL,
  `artist` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `available_songs`
--

CREATE TABLE `available_songs` (
  `id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `available_songs_playlist_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `available_songs_playlists`
--

CREATE TABLE `available_songs_playlists` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` varchar(1) COLLATE utf8_unicode_ci DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `id` int(11) NOT NULL,
  `genre` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `years`
--

CREATE TABLE `years` (
  `id` int(11) NOT NULL,
  `year` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `playlists`
--

CREATE TABLE `playlists` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tracks` int(11) NOT NULL DEFAULT '0',
  `total_time` int(11) NOT NULL DEFAULT '0',
  `active` varchar(1) COLLATE utf8_unicode_ci DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `playlists_songs`
--

CREATE TABLE `playlists_songs` (
  `id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(128) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting`, `value`) VALUES
('credit_maximum', '90'),
('credit_minimum', '5'),
('credit_per_track', '0.5'),
('credit_total', '3');

-- --------------------------------------------------------

--
-- Table structure for table `songs`
--

CREATE TABLE `songs` (
  `id` int(11) NOT NULL,
  `artist` int(11) NOT NULL DEFAULT '1',
  `genre` int(11) NOT NULL DEFAULT '1',
  `album` int(11) NOT NULL DEFAULT '1',
  `year` int(11) NOT NULL DEFAULT '1',
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `track_time` int(11) NOT NULL,
  `last_played_on` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sugestions`
--

CREATE TABLE `sugestions` (
  `id` int(11) NOT NULL,
  `Track` varchar(256) DEFAULT NULL,
  `Artist` varchar(256) DEFAULT NULL,
  `user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `swear_filter_replacement`
--

CREATE TABLE `swear_filter_replacement` (
  `id` int(11) NOT NULL,
  `replacement` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `swear_filter_replacement`
--

INSERT INTO `swear_filter_replacement` (`id`, `replacement`) VALUES
(1, 'shuffle');

-- --------------------------------------------------------

--
-- Table structure for table `swear_filter_words`
--

CREATE TABLE `swear_filter_words` (
  `id` int(11) NOT NULL,
  `word` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `swear_filter_words`
--

INSERT INTO `swear_filter_words` (`id`, `word`) VALUES
(1, '4r5e'),
(2, '5h1t'),
(3, '5hit'),
(4, 'a55'),
(5, 'anal'),
(6, 'ar5e'),
(7, 'arrse'),
(8, 'arse'),
(9, 'ass'),
(10, 'ass-fucker'),
(11, 'assfucker'),
(12, 'assfukka'),
(13, 'asshole'),
(14, 'asswhole'),
(15, 'b!tch'),
(16, 'b00bs'),
(17, 'b17ch'),
(18, 'b1tch'),
(19, 'ballbag'),
(20, 'balls'),
(21, 'ballsack'),
(22, 'bastard'),
(23, 'beastiality'),
(24, 'bi+ch'),
(25, 'bitch'),
(26, 'bloody'),
(27, 'blowjob'),
(28, 'boiolas'),
(29, 'boobs'),
(30, 'booobs'),
(31, 'boooobs'),
(32, 'booooobs'),
(33, 'booooooobs'),
(34, 'breasts'),
(35, 'buceta'),
(36, 'bunny fucker'),
(37, 'buttmuch'),
(38, 'c0ck'),
(39, 'c0cksucker'),
(40, 'cawk'),
(41, 'chink'),
(42, 'cipa'),
(43, 'cl1t'),
(44, 'clit'),
(45, 'clit'),
(46, 'clits'),
(47, 'cnut'),
(48, 'cock'),
(49, 'cock-sucker'),
(50, 'cockface'),
(51, 'cockhead'),
(52, 'cockmunch'),
(53, 'cockmuncher'),
(54, 'cocksucker'),
(55, 'cocksuka'),
(56, 'cocksukka'),
(57, 'cok'),
(58, 'cokmuncher'),
(59, 'coksucka'),
(60, 'cox'),
(61, 'cum'),
(62, 'cunt'),
(63, 'cyalis'),
(64, 'd1ck'),
(65, 'dick'),
(66, 'dickhead'),
(67, 'dildo'),
(68, 'dirsa'),
(69, 'dlck'),
(70, 'dog-fucker'),
(71, 'doggin'),
(72, 'dogging'),
(73, 'donkeyribber'),
(74, 'doosh'),
(75, 'duche'),
(76, 'ejakulate'),
(77, 'f u c k e r'),
(78, 'f4nny'),
(79, 'fag'),
(80, 'faggitt'),
(81, 'faggot'),
(82, 'fanny'),
(83, 'fannyflaps'),
(84, 'fannyfucker'),
(85, 'fanyy'),
(86, 'fatass'),
(87, 'fcuk'),
(88, 'fcuker'),
(89, 'fcuking'),
(90, 'feck'),
(91, 'fecker'),
(92, 'fook'),
(93, 'fooker'),
(94, 'fuck'),
(95, 'fuck'),
(96, 'fucka'),
(97, 'fucker'),
(98, 'fuckhead'),
(99, 'fuckin'),
(100, 'fucking'),
(101, 'fuckingshitmotherfucker'),
(102, 'fuckwhit'),
(103, 'fuckwit'),
(104, 'fuk'),
(105, 'fuker'),
(106, 'fukker'),
(107, 'fukkin'),
(108, 'fukwhit'),
(109, 'fukwit'),
(110, 'fux'),
(111, 'fux0r'),
(112, 'gaylord'),
(113, 'goatse'),
(114, 'heshe'),
(115, 'hoare'),
(116, 'hoer'),
(117, 'hore'),
(118, 'jackoff'),
(119, 'jism'),
(120, 'kawk'),
(121, 'knob'),
(122, 'knobead'),
(123, 'knobed'),
(124, 'knobhead'),
(125, 'knobjocky'),
(126, 'knobjokey'),
(127, 'l3i+ch'),
(128, 'l3itch'),
(129, 'm0f0'),
(130, 'm0fo'),
(131, 'm45terbate'),
(132, 'ma5terb8'),
(133, 'ma5terbate'),
(134, 'master-bate'),
(135, 'masterb8'),
(136, 'masterbat*'),
(137, 'masterbat3'),
(138, 'masterbation'),
(139, 'masterbations'),
(140, 'masturbate'),
(141, 'mo-fo'),
(142, 'mof0'),
(143, 'mofo'),
(144, 'motherfucker'),
(145, 'motherfuckka'),
(146, 'mutha'),
(147, 'muthafecker'),
(148, 'muthafuckker'),
(149, 'muther'),
(150, 'mutherfucker'),
(151, 'n1gga'),
(152, 'n1gger'),
(153, 'nazi'),
(154, 'nigg3r'),
(155, 'nigg4h'),
(156, 'nigga'),
(157, 'niggah'),
(158, 'niggas'),
(159, 'niggaz'),
(160, 'nigger'),
(161, 'nob'),
(162, 'nob jokey'),
(163, 'nobhead'),
(164, 'nobjocky'),
(165, 'nobjokey'),
(166, 'numbnuts'),
(167, 'nutsack'),
(168, 'p0rn'),
(169, 'pawn'),
(170, 'penis'),
(171, 'penisfucker'),
(172, 'phuck'),
(173, 'pigfucker'),
(174, 'pimpis'),
(175, 'piss'),
(176, 'pissflaps'),
(177, 'porn'),
(178, 'prick'),
(179, 'pron'),
(180, 'pusse'),
(181, 'pussi'),
(182, 'pussy'),
(183, 'rimjaw'),
(184, 'rimming'),
(185, 's.o.b.'),
(186, 'schlong'),
(187, 'scroat'),
(188, 'scrote'),
(189, 'scrotum'),
(190, 'sh!+'),
(191, 'sh!t'),
(192, 'sh1t'),
(193, 'shag'),
(194, 'shagger'),
(195, 'shaggin'),
(196, 'shagging'),
(197, 'shemale'),
(198, 'shi+'),
(199, 'shit'),
(200, 'shit'),
(201, 'shitdick'),
(202, 'shite'),
(203, 'shited'),
(204, 'shitey'),
(205, 'shitfuck'),
(206, 'shithead'),
(207, 'shitter'),
(208, 'slut'),
(209, 'smut'),
(210, 'snatch'),
(211, 'spac'),
(212, 't1tt1e5'),
(213, 't1tties'),
(214, 'teets'),
(215, 'teez'),
(216, 'testical'),
(217, 'testicle'),
(218, 'titfuck'),
(219, 'tits'),
(220, 'titt'),
(221, 'tittie5'),
(222, 'tittiefucker'),
(223, 'titties'),
(224, 'tittyfuck'),
(225, 'tittywank'),
(226, 'titwank'),
(227, 'tw4t'),
(228, 'twat'),
(229, 'twathead'),
(230, 'twatty'),
(231, 'twunt'),
(232, 'twunter'),
(233, 'v14gra'),
(234, 'v1gra'),
(235, 'viagra'),
(236, 'w00se'),
(237, 'wang'),
(238, 'wank'),
(239, 'wanker'),
(240, 'wanky'),
(241, 'whoar'),
(242, 'whore'),
(243, 'willies'),
(244, 'willy');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(256) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_history`
--

CREATE TABLE `user_history` (
  `user_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `picked` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_playlist`
--

CREATE TABLE `user_playlist` (
  `id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `custom_text_1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_text_2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `played` varchar(1) COLLATE utf8_unicode_ci DEFAULT 'N',
  `user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_playlist`
--
ALTER TABLE `admin_playlist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `albums`
--
ALTER TABLE `albums`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `artists`
--
ALTER TABLE `artists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `available_songs`
--
ALTER TABLE `available_songs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `available_songs_playlists`
--
ALTER TABLE `available_songs_playlists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `years`
--
ALTER TABLE `years`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `playlists`
--
ALTER TABLE `playlists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `playlists_songs`
--
ALTER TABLE `playlists_songs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD UNIQUE KEY `setting` (`setting`);

--
-- Indexes for table `songs`
--
ALTER TABLE `songs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sugestions`
--
ALTER TABLE `sugestions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sugestions_id_uindex` (`id`);

--
-- Indexes for table `swear_filter_replacement`
--
ALTER TABLE `swear_filter_replacement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `swear_filter_words`
--
ALTER TABLE `swear_filter_words`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `user_playlist`
--
ALTER TABLE `user_playlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_playlist`
--
ALTER TABLE `admin_playlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `albums`
--
ALTER TABLE `albums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `artists`
--
ALTER TABLE `artists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `available_songs`
--
ALTER TABLE `available_songs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `available_songs_playlists`
--
ALTER TABLE `available_songs_playlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `years`
--
ALTER TABLE `years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `playlists`
--
ALTER TABLE `playlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `playlists_songs`
--
ALTER TABLE `playlists_songs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `songs`
--
ALTER TABLE `songs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `sugestions`
--
ALTER TABLE `sugestions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `swear_filter_replacement`
--
ALTER TABLE `swear_filter_replacement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `swear_filter_words`
--
ALTER TABLE `swear_filter_words`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `user_playlist`
--
ALTER TABLE `user_playlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
