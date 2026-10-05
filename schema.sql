-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 30, 2026 at 01:55 PM
-- Server version: 5.7.44-log
-- PHP Version: 8.5.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `qu9rehfi2312`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievement_text`
--

CREATE TABLE `achievement_text` (
  `name` varchar(64) NOT NULL,
  `text` varchar(1000) NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `achievement_users`
--

CREATE TABLE `achievement_users` (
  `username` varchar(20) NOT NULL,
  `name` varchar(64) NOT NULL,
  `type` varchar(12) NOT NULL COMMENT 's,v',
  `ach_date` date NOT NULL,
  `closed` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `activations`
--

CREATE TABLE `activations` (
  `username` varchar(20) NOT NULL,
  `secret` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `username` varchar(20) NOT NULL,
  `name` varchar(256) NOT NULL,
  `birthday` date NOT NULL,
  `country` varchar(5) NOT NULL,
  `what` varchar(500) NOT NULL,
  `why` varchar(500) NOT NULL,
  `date` datetime NOT NULL,
  `accepted` tinyint(1) NOT NULL DEFAULT '0',
  `review_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `badboys`
--

CREATE TABLE `badboys` (
  `ip` varchar(64) NOT NULL,
  `submit_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `username` varchar(33) NOT NULL DEFAULT '',
  `agent` varchar(315) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `ban_reasons`
--

CREATE TABLE `ban_reasons` (
  `id` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ban_reasons`
--

INSERT INTO `ban_reasons` (`id`, `reason`) VALUES
(1, 'Spam'),
(2, 'Underage');

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `content` mediumtext NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `bulletins`
--

CREATE TABLE `bulletins` (
  `id` int(11) NOT NULL,
  `content` varchar(500) NOT NULL,
  `by_user` varchar(20) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `channel_banners`
--

CREATE TABLE `channel_banners` (
  `username` varchar(20) NOT NULL,
  `links` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `channel_comments`
--

CREATE TABLE `channel_comments` (
  `id` int(11) NOT NULL,
  `on_channel` varchar(20) NOT NULL,
  `by_user` varchar(20) NOT NULL,
  `comment` varchar(500) NOT NULL,
  `date` datetime NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `contest_entries`
--

CREATE TABLE `contest_entries` (
  `url` varchar(11) NOT NULL,
  `votes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `contest_votes`
--

CREATE TABLE `contest_votes` (
  `url` varchar(11) NOT NULL,
  `ip` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `converting`
--

CREATE TABLE `converting` (
  `url` varchar(11) NOT NULL,
  `uploaded_on` datetime NOT NULL,
  `convert_status` tinyint(1) NOT NULL DEFAULT '0',
  `queue` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `feature_suggestions`
--

CREATE TABLE `feature_suggestions` (
  `id` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `from_user` varchar(20) NOT NULL,
  `votes` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `forgot_password`
--

CREATE TABLE `forgot_password` (
  `username` varchar(20) NOT NULL,
  `code` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `id` int(11) NOT NULL,
  `friend_1` varchar(20) NOT NULL,
  `friend_2` varchar(20) NOT NULL,
  `by_user` varchar(20) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `sent_on` datetime NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` smallint(4) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(5000) NOT NULL,
  `categories` smallint(2) DEFAULT '1',
  `created_by` varchar(20) NOT NULL,
  `creation_date` datetime NOT NULL,
  `instant_join` tinyint(1) NOT NULL DEFAULT '1',
  `instant_video` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `groups_members`
--

CREATE TABLE `groups_members` (
  `member` varchar(20) NOT NULL,
  `group_id` varchar(4) NOT NULL,
  `accepted` tinyint(1) NOT NULL DEFAULT '1',
  `submit_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `groups_messages`
--

CREATE TABLE `groups_messages` (
  `id` int(11) NOT NULL,
  `message` varchar(5000) NOT NULL,
  `submit_date` datetime NOT NULL,
  `topic_id` int(11) NOT NULL,
  `by_user` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `groups_topics`
--

CREATE TABLE `groups_topics` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `creation_date` datetime NOT NULL,
  `created_by` varchar(20) NOT NULL,
  `group_id` smallint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `groups_videos`
--

CREATE TABLE `groups_videos` (
  `video` varchar(11) NOT NULL,
  `group_id` smallint(4) NOT NULL,
  `accepted` tinyint(1) NOT NULL DEFAULT '1',
  `submit_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `iprange_bans`
--

CREATE TABLE `iprange_bans` (
  `bid` int(11) NOT NULL,
  `ip_range` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `mentions`
--

CREATE TABLE `mentions` (
  `video` int(11) DEFAULT NULL,
  `channel` varchar(20) DEFAULT NULL,
  `type` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `username` varchar(20) NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `most_subscribed_month`
--

CREATE TABLE `most_subscribed_month` (
  `username` varchar(20) NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `most_subscribed_week`
--

CREATE TABLE `most_subscribed_week` (
  `username` varchar(20) NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `most_viewed_month`
--

CREATE TABLE `most_viewed_month` (
  `username` varchar(20) NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `most_viewed_week`
--

CREATE TABLE `most_viewed_week` (
  `username` varchar(20) NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `option_name` varchar(50) NOT NULL,
  `value` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `playlists`
--

CREATE TABLE `playlists` (
  `purl` varchar(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `title` varchar(128) NOT NULL,
  `description` text,
  `created_by` varchar(20) NOT NULL,
  `thumbnail` varchar(11) NOT NULL,
  `created_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `playlists_videos`
--

CREATE TABLE `playlists_videos` (
  `url` varchar(11) NOT NULL,
  `purl` varchar(11) NOT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `private_messages`
--

CREATE TABLE `private_messages` (
  `id` int(11) NOT NULL,
  `from_user` varchar(20) NOT NULL,
  `to_user` varchar(20) NOT NULL,
  `message` varchar(10000) NOT NULL,
  `subject` varchar(256) NOT NULL DEFAULT '',
  `date_sent` datetime NOT NULL,
  `seen` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `rate_limits`
--

CREATE TABLE `rate_limits` (
  `id` int(255) NOT NULL,
  `ip` varchar(70) NOT NULL,
  `timestamp` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `expiration` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `recently_viewed`
--

CREATE TABLE `recently_viewed` (
  `url` varchar(11) NOT NULL,
  `time_viewed` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `replies`
--

CREATE TABLE `replies` (
  `id` int(11) NOT NULL,
  `for_user` varchar(20) NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(12) NOT NULL,
  `report_from` int(12) NOT NULL,
  `report_to` int(12) NOT NULL,
  `report_type` varchar(255) DEFAULT NULL,
  `report_description` text,
  `reported` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(12) NOT NULL,
  `session` varchar(255) NOT NULL,
  `user` int(32) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `remembered` int(2) NOT NULL DEFAULT '0',
  `first_access` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_access` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `browser` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `name` varchar(64) NOT NULL,
  `value` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`name`, `value`) VALUES
('channels', '1'),
('guidelines', '   <p>The LiiVid Guidelines are meant to be easily comprehensible and straightforward. We strive to make them inclusive and accommodating to everyone. We request you to abide by our concise set of rules to ensure a safe and positive experience for everyone.</p>\r\n  <h2 style=\"border-bottom: 1px solid #ccc;\">The Rules</h2>\r\n  <ul class=\"cgul\" style=\"padding-left: 29px;\">\r\n    <li><b>Respect Others:</b> LiiVid is a community of individuals with diverse opinions and backgrounds. We expect everyone to treat each other with respect and kindness. Any content that promotes hate speech, harassment, discrimination, or violence will not be tolerated on our platform.</li>\r\n    <li><b>Keep it Legal:</b> You must follow all applicable laws and regulations when using LiiVid. Do not upload or share any content that violates copyright, trademark, or any other intellectual property laws. Also, do not share content that is illegal or violates any other laws.</li>\r\n    <li><b>No Nudity or Sexual Content:</b> LiiVid is not a platform for pornography or sexually explicit content. Any content that contains nudity or sexual content will be removed from our platform.</li>\r\n    <li><b>No Spam:</b> We do not allow spam on our platform. Do not upload or share content that is intended to promote or sell illicit goods, products or services.</li>\r\n    <li><b>Age Restriction:</b> Users must be at least 13 years old to use LiiVid. If you are under 18 years old, you must have the consent of a parent or legal guardian to use our platform.</li>\r\n    <li><b>Reporting:</b> If you come across any content that violates our guidelines or community standards, please report it immediately. We take all reports seriously and will take appropriate action.</li>\r\n    <li><b>Be Authentic:</b> We encourage you to be yourself on LiiVid. Do not upload or share any content that is fake or misleading. You are responsible for the content you upload or share on our platform.</li>\r\n  </ul>\r\n<h2 style=\"border-bottom: 1px solid #ccc; margin: 22px 0 10px;\">We Enforce These Guidelines</h2>\r\n<p>Periodically, our staff at LiiVid reviews flagged videos to ensure they adhere to our Community Guidelines. If we find any violation, we take necessary action to remove them. Any account found violating these guidelines will be penalized, and repeated or severe violations may lead to account termination. We urge you to follow these guidelines without trying to find any loopholes or attempting to circumvent them by any means.</p>\r\n'),
('help', '<strong>Q: Is LiiVid a free service?</strong>\r\n\r\n<p>A: Yes, LiiVid is completely free to use with no strings attached.</p>\r\n\r\n<strong>Q: How can I share a video on LiiVid?</strong>\r\n\r\n<p>A: To share a video on LiiVid, simply copy the embed code provided under the video description.</p>\r\n<strong>Q: What is the maximum video length allowed on LiiVid?</strong>\r\n\r\n<p>A: Regular users can upload videos up to 35 minutes long, while partners can upload videos up to 1 hour long.</p>\r\n<strong>Q: What file formats are accepted for video uploads on LiiVid?</strong>\r\n\r\n<p>A: LiiVid accepts video uploads in the following formats: .3GP, .AVI, .FLV, .MOV, .MP4, .MPEG, .MPG, .WEBM, and .WMV.</p>\r\n<strong>Q: What is LiiVid\'s policy on copyright infringement?</strong>\r\n\r\n<p>A: LiiVid respects the rights of copyright holders and publishers and only accepts video uploads from persons who hold all necessary rights to the uploaded material. LiiVid\'s policy is to respond to any notices of alleged infringement that comply with the Digital Millennium Copyright Act (DMCA).</p>\r\n<strong>Q: Why do I keep getting asked to log in on LiiVid?</strong>\r\n\r\n<p>A: If LiiVid keeps asking you to log in, make sure you have cookies enabled on your browser.</p>\r\n<strong>Q: What are tags used for on LiiVid?</strong>\r\n\r\n<p>A: Tags are basically keywords that describe your videos, making them easier to find by other users.</p>\r\n<strong>Q: How do I delete one of my videos on LiiVid?</strong>\r\n\r\n<p>A: To delete a video on LiiVid, simply go to the watch page of your video and click on the \"Delete Video\" button right under the video description. Alternatively, you can also go to the \"My Videos\" page and delete a video from there.</p>\r\n<strong>Q: What is the size limit for video uploads on LiiVid?</strong>\r\n\r\n<p>A: The size limit for video uploads on LiiVid is 5 GB per video.</p>\r\n<strong>Q: How can I report a video on LiiVid?</strong>\r\n\r\n<p>A: To report a video on LiiVid, click the \"Flag\" button below the video player and report the video for the rule that it\'s breaking.</p>\r\n'),
('login', '1'),
('logo', '0'),
('maintenance', '0'),
('signup', '1'),
('top_text', ''),
('uploader', '1'),
('videos', '1');

-- --------------------------------------------------------

--
-- Table structure for table `strikes`
--

CREATE TABLE `strikes` (
  `username` varchar(20) NOT NULL,
  `issued_by` varchar(20) NOT NULL,
  `issued_on` int(11) NOT NULL,
  `video_links` text NOT NULL,
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `subscriber` varchar(20) NOT NULL,
  `subscription` varchar(20) NOT NULL,
  `submit_date` date NOT NULL DEFAULT '0000-00-00',
  `source` varchar(11) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `terminations`
--

CREATE TABLE `terminations` (
  `username` varchar(20) NOT NULL,
  `secret` varchar(100) NOT NULL,
  `issued` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `themes`
--

CREATE TABLE `themes` (
  `url` varchar(20) NOT NULL,
  `title` varchar(101) NOT NULL,
  `description` varchar(1100) NOT NULL,
  `category` int(11) NOT NULL,
  `logged_in` int(11) NOT NULL,
  `header` int(11) NOT NULL,
  `chrome` tinyint(1) NOT NULL,
  `firefox` tinyint(1) NOT NULL,
  `edge` tinyint(1) NOT NULL,
  `internet` tinyint(1) NOT NULL,
  `opera` tinyint(1) NOT NULL,
  `owner` varchar(20) NOT NULL,
  `upload_date` datetime NOT NULL,
  `installs` int(11) NOT NULL,
  `accepted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `url` varchar(11) NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '0 = Upload, 1 = Change',
  `user` varchar(50) NOT NULL,
  `filesize` int(11) NOT NULL,
  `filetype` varchar(50) NOT NULL,
  `modified` varchar(50) NOT NULL,
  `token` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `displayname` varchar(20) NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` varchar(64) NOT NULL,
  `reg_date` datetime NOT NULL,
  `last_login` datetime NOT NULL,
  `strikes` tinyint(1) NOT NULL DEFAULT '0',
  `videos_watched` int(11) NOT NULL DEFAULT '0',
  `channel_views` int(11) NOT NULL DEFAULT '0',
  `channel_comments` int(11) NOT NULL DEFAULT '0',
  `video_views` int(11) NOT NULL DEFAULT '0',
  `videos` int(11) NOT NULL DEFAULT '0',
  `favorites` int(11) NOT NULL DEFAULT '0',
  `subscribers` int(11) NOT NULL DEFAULT '0',
  `subscriptions` int(11) NOT NULL DEFAULT '0',
  `friends` int(11) NOT NULL DEFAULT '0',
  `birthday` date NOT NULL,
  `country` varchar(5) NOT NULL DEFAULT '',
  `website` varchar(128) NOT NULL DEFAULT '',
  `about` varchar(500) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `cover` mediumblob,
  `bg_version` int(11) NOT NULL DEFAULT '1',
  `banner_version` int(11) NOT NULL DEFAULT '1',
  `channel_type` int(11) NOT NULL DEFAULT '0',
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  `privacy` tinyint(1) NOT NULL DEFAULT '0' COMMENT '#0 = Public #1 = Unlisted #2 = Friends',
  `ban_reasons` varchar(50) NOT NULL DEFAULT '',
  `1st_latest_ip` varchar(128) NOT NULL,
  `2nd_latest_ip` varchar(128) NOT NULL DEFAULT '',
  `last_username_update` int(11) NOT NULL DEFAULT '0',
  `bg` varchar(6) NOT NULL DEFAULT 'ffffff',
  `chn_radius` int(11) NOT NULL DEFAULT '0',
  `avt_radius` int(11) DEFAULT '0',
  `nav` varchar(6) NOT NULL DEFAULT '89857F',
  `h_head` varchar(6) NOT NULL DEFAULT '666666',
  `h_head_fnt` varchar(6) NOT NULL DEFAULT 'ffffff',
  `h_in` varchar(6) NOT NULL DEFAULT 'eeeeee',
  `h_in_fnt` varchar(6) NOT NULL DEFAULT '6d6d6d',
  `n_head` varchar(6) NOT NULL DEFAULT '666666',
  `n_head_fnt` varchar(6) NOT NULL DEFAULT 'ffffff',
  `n_in` varchar(6) NOT NULL DEFAULT 'ffffff',
  `n_in_fnt` varchar(6) NOT NULL DEFAULT '000000',
  `links` varchar(6) NOT NULL DEFAULT '89857F',
  `font` int(11) NOT NULL DEFAULT '0',
  `b_avatar` varchar(7) NOT NULL DEFAULT '999999',
  `connect` varchar(32) NOT NULL DEFAULT '',
  `c_subscriber` tinyint(1) NOT NULL DEFAULT '1',
  `c_subscription` tinyint(1) NOT NULL DEFAULT '1',
  `c_friend` tinyint(1) NOT NULL DEFAULT '1',
  `c_featured` tinyint(1) NOT NULL DEFAULT '1',
  `featured_n_url` varchar(32) NOT NULL DEFAULT '',
  `featured_s_url` varchar(32) NOT NULL DEFAULT '',
  `snow` tinyint(1) NOT NULL DEFAULT '0',
  `mondo` tinyint(1) NOT NULL DEFAULT '0',
  `c_videos` tinyint(1) NOT NULL DEFAULT '1',
  `c_favorites` tinyint(1) NOT NULL DEFAULT '1',
  `c_comments` tinyint(1) NOT NULL DEFAULT '1',
  `c_featured_playlists` tinyint(1) NOT NULL DEFAULT '0',
  `c_featured_channels` tinyint(1) NOT NULL DEFAULT '0',
  `c_recent` tinyint(1) NOT NULL DEFAULT '1',
  `c_playlists` tinyint(1) NOT NULL DEFAULT '0',
  `playlists` varchar(256) NOT NULL DEFAULT '',
  `featured_channels` varchar(256) NOT NULL DEFAULT '',
  `bg_position` int(11) NOT NULL DEFAULT '0',
  `bg_repeat` int(11) DEFAULT '0',
  `bg_fixed` int(11) NOT NULL DEFAULT '0',
  `bg_stretch` int(11) NOT NULL DEFAULT '0',
  `h_trans` int(11) NOT NULL DEFAULT '0',
  `n_trans` int(11) DEFAULT '0',
  `channel_title` varchar(128) DEFAULT NULL,
  `channel_description` varchar(2500) DEFAULT NULL,
  `channel_tags` varchar(256) DEFAULT NULL,
  `i_name` varchar(128) DEFAULT '',
  `i_occupation` varchar(128) NOT NULL DEFAULT '',
  `i_schools` varchar(128) NOT NULL DEFAULT '',
  `i_interests` varchar(128) NOT NULL DEFAULT '',
  `i_movies` varchar(128) NOT NULL DEFAULT '',
  `i_music` varchar(128) NOT NULL DEFAULT '',
  `i_books` varchar(128) NOT NULL DEFAULT '',
  `a_name` tinyint(1) NOT NULL DEFAULT '1',
  `a_website` tinyint(1) NOT NULL DEFAULT '1',
  `a_description` tinyint(1) NOT NULL DEFAULT '1',
  `a_occupation` tinyint(1) NOT NULL DEFAULT '1',
  `a_schools` tinyint(1) NOT NULL DEFAULT '1',
  `a_interests` tinyint(1) NOT NULL DEFAULT '1',
  `a_movies` tinyint(1) NOT NULL DEFAULT '1',
  `a_music` tinyint(1) NOT NULL DEFAULT '1',
  `a_books` tinyint(1) DEFAULT '1',
  `a_last` tinyint(1) NOT NULL DEFAULT '1',
  `a_reg` tinyint(1) NOT NULL DEFAULT '1',
  `a_subs` tinyint(1) NOT NULL DEFAULT '1',
  `a_subs2` tinyint(1) NOT NULL DEFAULT '0',
  `a_country` tinyint(1) NOT NULL DEFAULT '1',
  `a_age` tinyint(1) NOT NULL DEFAULT '1',
  `ra_comments` tinyint(1) NOT NULL DEFAULT '1',
  `ra_favorites` tinyint(1) DEFAULT '1',
  `ra_friends` tinyint(1) NOT NULL DEFAULT '1',
  `activated` tinyint(1) NOT NULL DEFAULT '0',
  `channel_comment_privacy` tinyint(1) NOT NULL DEFAULT '0',
  `can_friend` tinyint(1) NOT NULL DEFAULT '1',
  `can_mention` tinyint(1) NOT NULL DEFAULT '1',
  `can_message` tinyint(1) NOT NULL DEFAULT '1',
  `featured_title` varchar(64) NOT NULL DEFAULT '',
  `theme` int(11) NOT NULL DEFAULT '0',
  `default_view` tinyint(1) NOT NULL DEFAULT '0',
  `c_all` tinyint(1) NOT NULL DEFAULT '1',
  `channel_version` int(11) NOT NULL DEFAULT '1',
  `subscriber_d` tinyint(1) NOT NULL DEFAULT '0',
  `subscription_d` tinyint(1) NOT NULL DEFAULT '0',
  `friends_d` tinyint(1) NOT NULL DEFAULT '0',
  `featured_d` tinyint(1) NOT NULL DEFAULT '1',
  `channel_d` tinyint(1) NOT NULL DEFAULT '1',
  `recent_d` tinyint(1) NOT NULL DEFAULT '1',
  `c_custom` tinyint(1) NOT NULL DEFAULT '0',
  `custom_d` tinyint(1) NOT NULL DEFAULT '1',
  `modules_vertical_r` varchar(24) NOT NULL DEFAULT 'cu,re,ft,s2,s1,fr,co',
  `modules_vertical_l` varchar(24) NOT NULL DEFAULT 'cu,re,ft,s2,s1,fr,co',
  `custom` varchar(1024) NOT NULL DEFAULT '',
  `partner` tinyint(1) NOT NULL DEFAULT '0',
  `nouveau` tinyint(1) NOT NULL DEFAULT '0',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `is_mod` tinyint(1) NOT NULL DEFAULT '0',
  `shadowbanned` tinyint(1) NOT NULL DEFAULT '0',
  `adsense` varchar(100) NOT NULL DEFAULT '',
  `playerSize` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users_block`
--

CREATE TABLE `users_block` (
  `blocker` varchar(20) NOT NULL,
  `blocked` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users_oldnames`
--

CREATE TABLE `users_oldnames` (
  `displayname` varchar(20) NOT NULL,
  `username` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users_remembers`
--

CREATE TABLE `users_remembers` (
  `uid` varchar(20) NOT NULL,
  `code` varchar(32) NOT NULL,
  `browser` varchar(16) NOT NULL,
  `last_login` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_remembers`
--

INSERT INTO `users_remembers` (`uid`, `code`, `browser`, `last_login`) VALUES
('turkadam', 'WgEbn_KBrwjfChH1andZjQz_me938QKR', 'Chrome', '2025-12-01'),
('TheEpikWebDev', 'ViAMEIIxOWrfMltUIb8wGPyaE9GuKlmu', 'Firefox', '2025-12-01'),
('turkadam', 'PjLKd5tHehvnK04x0P0k5ro0oX8SPTb2', 'Chrome', '2025-12-02'),
('Zack9191', 'zolFXo-5TfQr9Oa9jrcSLO7Jr29S2AGu', 'Chrome', '2025-12-02'),
('Zack9191', 'u8aUtqvxRobLT2DeVFLNljY-I6FIAN4O', 'Chrome', '2025-12-02'),
('Zack9191', 'NSpfSTdrFN2UZFe0MSII5iYEgzDUxU70', 'Chrome', '2025-12-02'),
('copy2123', 'iekBBqD0v3ZEek2I0399LKSjaTZnWE27', 'Firefox', '2025-12-02'),
('Zack9191', '7GqYy7gakYDJwnmqaorGCvg_m9CWE8r1', 'Chrome', '2025-12-02');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `url` varchar(11) NOT NULL,
  `file` varchar(80) NOT NULL,
  `hd` tinyint(1) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `tags` varchar(250) DEFAULT NULL,
  `category` int(11) NOT NULL DEFAULT '1',
  `privacy` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = Pub, 1 = Pri, 2 = Unli',
  `uploaded_by` varchar(20) NOT NULL,
  `uploaded_on` datetime NOT NULL,
  `thumbnail` mediumblob,
  `views` int(11) NOT NULL DEFAULT '0',
  `displayviews` int(11) NOT NULL DEFAULT '0',
  `watched` mediumint(9) NOT NULL DEFAULT '0',
  `comments` int(11) NOT NULL DEFAULT '0',
  `responses` int(11) NOT NULL DEFAULT '0',
  `favorites` int(11) NOT NULL DEFAULT '0',
  `1_star` int(11) NOT NULL DEFAULT '0',
  `2_star` int(11) NOT NULL DEFAULT '0',
  `3_star` int(11) NOT NULL DEFAULT '0',
  `4_star` int(11) NOT NULL DEFAULT '0',
  `5_star` int(11) NOT NULL DEFAULT '0',
  `length` int(11) NOT NULL DEFAULT '0',
  `s_comments` tinyint(1) NOT NULL DEFAULT '1',
  `s_ratings` tinyint(1) NOT NULL DEFAULT '1',
  `s_responses` tinyint(1) NOT NULL DEFAULT '1',
  `s_related` tinyint(1) NOT NULL DEFAULT '1',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `frontpage` tinyint(1) NOT NULL DEFAULT '0',
  `most_popular` tinyint(1) NOT NULL DEFAULT '1',
  `banned_uploader` tinyint(1) NOT NULL DEFAULT '0',
  `shadowbanned_uploader` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `thumbs` tinyint(1) NOT NULL DEFAULT '-1',
  `show_ads` tinyint(1) NOT NULL DEFAULT '1',
  `aspect_ratio` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `videos_deleted`
--

CREATE TABLE `videos_deleted` (
  `id` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `videos_flags`
--

CREATE TABLE `videos_flags` (
  `url` varchar(11) NOT NULL,
  `by_user` varchar(20) NOT NULL,
  `reason` int(11) NOT NULL,
  `submit_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `videos_schedule`
--

CREATE TABLE `videos_schedule` (
  `id` varchar(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `videos_views`
--

CREATE TABLE `videos_views` (
  `vid` varchar(11) NOT NULL,
  `views` smallint(6) NOT NULL,
  `submit_date` date NOT NULL DEFAULT '0000-00-00',
  `source` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `videos_views_ips`
--

CREATE TABLE `videos_views_ips` (
  `vid` varchar(255) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `submit_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `videos_watched`
--

CREATE TABLE `videos_watched` (
  `vid` varchar(11) NOT NULL,
  `watchtime` mediumint(9) NOT NULL,
  `submit_date` date NOT NULL,
  `ip_list` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `video_comments`
--

CREATE TABLE `video_comments` (
  `id` int(11) NOT NULL,
  `url` varchar(11) NOT NULL,
  `comment` mediumtext NOT NULL,
  `by_user` varchar(20) NOT NULL,
  `date_sent` datetime NOT NULL,
  `rating` int(11) NOT NULL DEFAULT '0',
  `has_replies` tinyint(1) NOT NULL DEFAULT '0',
  `reply_to` int(11) NOT NULL DEFAULT '0',
  `seen` tinyint(1) NOT NULL DEFAULT '0',
  `raters` varchar(10000) NOT NULL DEFAULT '',
  `edited` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `video_favorites`
--

CREATE TABLE `video_favorites` (
  `url` varchar(11) NOT NULL,
  `favorite_by` varchar(20) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `video_ratings`
--

CREATE TABLE `video_ratings` (
  `url` varchar(11) NOT NULL,
  `user_rated` varchar(20) NOT NULL,
  `stars` tinyint(1) NOT NULL,
  `submit_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `video_responses`
--

CREATE TABLE `video_responses` (
  `id` int(11) NOT NULL,
  `url` varchar(11) NOT NULL,
  `url_response` varchar(11) NOT NULL,
  `date` datetime NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT '0',
  `accepted` tinyint(1) NOT NULL DEFAULT '0',
  `response_user` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wrong_logins`
--

CREATE TABLE `wrong_logins` (
  `ip` varchar(128) NOT NULL,
  `submit_date` datetime NOT NULL,
  `channel` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `achievement_text`
--
ALTER TABLE `achievement_text`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `achievement_users`
--
ALTER TABLE `achievement_users`
  ADD UNIQUE KEY `username` (`username`,`name`),
  ADD KEY `username_2` (`username`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `activations`
--
ALTER TABLE `activations`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `badboys`
--
ALTER TABLE `badboys`
  ADD UNIQUE KEY `ip` (`ip`,`submit_date`);

--
-- Indexes for table `ban_reasons`
--
ALTER TABLE `ban_reasons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bulletins`
--
ALTER TABLE `bulletins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `by_user` (`by_user`);

--
-- Indexes for table `channel_banners`
--
ALTER TABLE `channel_banners`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `channel_comments`
--
ALTER TABLE `channel_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `on_channel` (`on_channel`);

--
-- Indexes for table `contest_entries`
--
ALTER TABLE `contest_entries`
  ADD PRIMARY KEY (`url`);

--
-- Indexes for table `contest_votes`
--
ALTER TABLE `contest_votes`
  ADD PRIMARY KEY (`ip`);

--
-- Indexes for table `converting`
--
ALTER TABLE `converting`
  ADD PRIMARY KEY (`url`),
  ADD KEY `uploaded_on` (`uploaded_on`);

--
-- Indexes for table `feature_suggestions`
--
ALTER TABLE `feature_suggestions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forgot_password`
--
ALTER TABLE `forgot_password`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `friend_1` (`friend_1`,`friend_2`),
  ADD KEY `friend_1_2` (`friend_1`),
  ADD KEY `friend_2` (`friend_2`),
  ADD KEY `sent_on` (`sent_on`),
  ADD KEY `friend_1_3` (`friend_1`,`friend_2`,`status`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups_members`
--
ALTER TABLE `groups_members`
  ADD UNIQUE KEY `member` (`member`,`group_id`);

--
-- Indexes for table `groups_messages`
--
ALTER TABLE `groups_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups_topics`
--
ALTER TABLE `groups_topics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups_videos`
--
ALTER TABLE `groups_videos`
  ADD UNIQUE KEY `video` (`video`,`group_id`);

--
-- Indexes for table `iprange_bans`
--
ALTER TABLE `iprange_bans`
  ADD PRIMARY KEY (`bid`);

--
-- Indexes for table `mentions`
--
ALTER TABLE `mentions`
  ADD UNIQUE KEY `comment` (`video`,`username`),
  ADD UNIQUE KEY `channel` (`channel`,`username`);

--
-- Indexes for table `most_subscribed_month`
--
ALTER TABLE `most_subscribed_month`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `most_subscribed_week`
--
ALTER TABLE `most_subscribed_week`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `most_viewed_month`
--
ALTER TABLE `most_viewed_month`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `most_viewed_week`
--
ALTER TABLE `most_viewed_week`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`option_name`);

--
-- Indexes for table `playlists`
--
ALTER TABLE `playlists`
  ADD PRIMARY KEY (`purl`),
  ADD UNIQUE KEY `title` (`title`,`created_by`);

--
-- Indexes for table `playlists_videos`
--
ALTER TABLE `playlists_videos`
  ADD UNIQUE KEY `url` (`url`,`purl`);

--
-- Indexes for table `private_messages`
--
ALTER TABLE `private_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `from_user` (`from_user`),
  ADD KEY `to_user` (`to_user`),
  ADD KEY `seen` (`seen`);

--
-- Indexes for table `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recently_viewed`
--
ALTER TABLE `recently_viewed`
  ADD PRIMARY KEY (`url`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `strikes`
--
ALTER TABLE `strikes`
  ADD KEY `issued_by` (`issued_by`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `terminations`
--
ALTER TABLE `terminations`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `themes`
--
ALTER TABLE `themes`
  ADD PRIMARY KEY (`url`);
ALTER TABLE `themes` ADD FULLTEXT KEY `title` (`title`,`description`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`url`),
  ADD KEY `size` (`filesize`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `displayname` (`displayname`),
  ADD KEY `shadowbanned` (`shadowbanned`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `users_block`
--
ALTER TABLE `users_block`
  ADD KEY `blocker` (`blocker`),
  ADD KEY `blocked` (`blocked`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`url`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `featured` (`featured`),
  ADD KEY `uploaded_on` (`uploaded_on`),
  ADD KEY `privacy` (`privacy`,`banned_uploader`,`shadowbanned_uploader`,`status`),
  ADD KEY `aspect_ratio` (`aspect_ratio`);
ALTER TABLE `videos` ADD FULLTEXT KEY `ft_related` (`title`,`tags`,`description`);
ALTER TABLE `videos` ADD FULLTEXT KEY `FullText` (`title`,`description`);
ALTER TABLE `videos` ADD FULLTEXT KEY `FullText2` (`title`,`description`);
ALTER TABLE `videos` ADD FULLTEXT KEY `title` (`title`);
ALTER TABLE `videos` ADD FULLTEXT KEY `ft_title` (`title`);
ALTER TABLE `videos` ADD FULLTEXT KEY `ft_tags` (`tags`);
ALTER TABLE `videos` ADD FULLTEXT KEY `ft_description` (`description`);

--
-- Indexes for table `videos_deleted`
--
ALTER TABLE `videos_deleted`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `videos_flags`
--
ALTER TABLE `videos_flags`
  ADD UNIQUE KEY `url` (`url`,`by_user`);

--
-- Indexes for table `videos_schedule`
--
ALTER TABLE `videos_schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `videos_views`
--
ALTER TABLE `videos_views`
  ADD UNIQUE KEY `vid` (`vid`,`submit_date`,`source`),
  ADD KEY `views` (`views`);

--
-- Indexes for table `videos_views_ips`
--
ALTER TABLE `videos_views_ips`
  ADD PRIMARY KEY (`vid`,`ip`);

--
-- Indexes for table `videos_watched`
--
ALTER TABLE `videos_watched`
  ADD UNIQUE KEY `vid` (`vid`,`submit_date`),
  ADD KEY `watchtime` (`watchtime`);

--
-- Indexes for table `video_comments`
--
ALTER TABLE `video_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `url` (`url`),
  ADD KEY `reply_to` (`reply_to`),
  ADD KEY `seen` (`seen`),
  ADD KEY `date_sent` (`date_sent`),
  ADD KEY `by_user` (`by_user`),
  ADD KEY `rating` (`rating`);

--
-- Indexes for table `video_favorites`
--
ALTER TABLE `video_favorites`
  ADD UNIQUE KEY `url` (`url`,`favorite_by`),
  ADD KEY `date` (`date`),
  ADD KEY `favorite_by` (`favorite_by`);

--
-- Indexes for table `video_ratings`
--
ALTER TABLE `video_ratings`
  ADD UNIQUE KEY `url` (`url`,`user_rated`);

--
-- Indexes for table `video_responses`
--
ALTER TABLE `video_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `url` (`url`,`accepted`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ban_reasons`
--
ALTER TABLE `ban_reasons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulletins`
--
ALTER TABLE `bulletins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `channel_comments`
--
ALTER TABLE `channel_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feature_suggestions`
--
ALTER TABLE `feature_suggestions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` smallint(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups_messages`
--
ALTER TABLE `groups_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups_topics`
--
ALTER TABLE `groups_topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `iprange_bans`
--
ALTER TABLE `iprange_bans`
  MODIFY `bid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `private_messages`
--
ALTER TABLE `private_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video_comments`
--
ALTER TABLE `video_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video_responses`
--
ALTER TABLE `video_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
