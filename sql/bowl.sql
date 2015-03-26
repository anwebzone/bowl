-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Värd: 127.0.0.1
-- Tid vid skapande: 26 mars 2015 kl 15:42
-- Serverversion: 5.6.17
-- PHP-version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databas: `bowl`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `answers`
--

CREATE TABLE IF NOT EXISTS `answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `q_id` int(11) NOT NULL,
  `creator` char(30) DEFAULT NULL,
  `text` text,
  `created` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `q_id` (`q_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `questions`
--

CREATE TABLE IF NOT EXISTS `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `creator` char(30) NOT NULL,
  `title` text NOT NULL,
  `text` text NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `question_tags`
--

CREATE TABLE IF NOT EXISTS `question_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tags_id` int(11) DEFAULT NULL,
  `q_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tags_id` (`tags_id`),
  KEY `q_id` (`q_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `sub_answers`
--

CREATE TABLE IF NOT EXISTS `sub_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `a_id` int(11) DEFAULT NULL,
  `creator` char(30) DEFAULT NULL,
  `text` text,
  `created` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `a_id` (`a_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(50) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- Dumpning av Data i tabell `tags`
--

INSERT INTO `tags` (`id`, `name`, `text`) VALUES
(13, 'Allmänt', 'Här hittar du alla frågor och diskussioner som inte riktigt passar in i någon annan tag.'),
(14, 'Bowlingklot', 'Här hittar du alla frågor och diskussioner om bowlingklot. '),
(15, 'Seriespel', 'Här hittar du alla frågor och diskussioner om seriespel.'),
(16, 'Bowlingtävlingar', 'Här hittar du alla frågor och diskussioner om bowlingtävlingar.'),
(17, 'Bowlingskor', 'Här hittar du alla frågor och diskussioner om bowlingskor.'),
(18, 'Tillbehör', 'Här hittar du alla frågor och diskussioner om tillbehör som t.ex tejp, fingerinsatser, handledsstöd med mera.'),
(19, 'Träning', 'Här hittar du frågor och diskussioner om bowlingträning.'),
(20, 'Swebowl', 'Här hittar du alla frågor och diskussioner om Swebowl - Svenska Bowlingförbundet.'),
(21, 'Oljeprofiler', 'Här hittar du frågor och diskussioner om olika oljeprofiler.'),
(22, 'Klotväskor', 'Här hittar du frågor och diskussioner om klotväskor.'),
(23, 'Övrigt', 'Här hittar du frågor och diskussioner som inte hör till bowling. '),
(24, 'Bowl', 'Frågor och diskussioner gällande sidan bowl. Förslag på förbättringar med mera.');

-- --------------------------------------------------------

--
-- Tabellstruktur `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acronym` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `presentation` text NOT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `active` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `acronym` (`acronym`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dumpning av Data i tabell `user`
--

INSERT INTO `user` (`id`, `acronym`, `email`, `name`, `password`, `presentation`, `created`, `updated`, `deleted`, `active`) VALUES
(14, 'john', 'john.doe@dbwebb.se', 'John doe', '$2y$10$Y4lItWYsi/S/usry/PNhq.luXaAVquKHsRTI.wW9yh4of7unFx1Ii', 'Denna användare har inte skrivit något i sin presentation ännu.', '2015-03-26 14:32:18', NULL, NULL, '2015-03-26 14:37:27');

-- --------------------------------------------------------

--
-- Ersättningsstruktur för vy `vquestions`
--
CREATE TABLE IF NOT EXISTS `vquestions` (
`id` int(11)
,`creator` char(30)
,`title` text
,`text` text
,`created` int(11)
,`tag_name` text
);
-- --------------------------------------------------------

--
-- Struktur för vy `vquestions`
--
DROP TABLE IF EXISTS `vquestions`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vquestions` AS select `q`.`id` AS `id`,`q`.`creator` AS `creator`,`q`.`title` AS `title`,`q`.`text` AS `text`,`q`.`created` AS `created`,group_concat(`t`.`name` separator ',') AS `tag_name` from ((`questions` `q` left join `question_tags` `qtag` on((`q`.`id` = `qtag`.`q_id`))) left join `tags` `t` on((`qtag`.`tags_id` = `t`.`id`))) group by `q`.`id`;

--
-- Restriktioner för dumpade tabeller
--

--
-- Restriktioner för tabell `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`q_id`) REFERENCES `questions` (`id`);

--
-- Restriktioner för tabell `question_tags`
--
ALTER TABLE `question_tags`
  ADD CONSTRAINT `question_tags_ibfk_1` FOREIGN KEY (`tags_id`) REFERENCES `tags` (`id`),
  ADD CONSTRAINT `question_tags_ibfk_2` FOREIGN KEY (`q_id`) REFERENCES `questions` (`id`);

--
-- Restriktioner för tabell `sub_answers`
--
ALTER TABLE `sub_answers`
  ADD CONSTRAINT `sub_answers_ibfk_1` FOREIGN KEY (`a_id`) REFERENCES `answers` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
