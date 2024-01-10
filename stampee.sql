-- phpMyAdmin SQL Dump
-- version 4.9.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jan 10, 2024 at 05:27 PM
-- Server version: 5.7.26
-- PHP Version: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `stampee`
--

-- --------------------------------------------------------

--
-- Table structure for table `dimension`
--

CREATE TABLE `dimension` (
  `id` int(11) NOT NULL,
  `nom` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dimension`
--

INSERT INTO `dimension` (`id`, `nom`) VALUES
(1, 'Rouleau'),
(2, 'Carnet'),
(3, 'Feuillet'),
(4, 'Bande'),
(5, 'Simple'),
(6, 'Autres');

-- --------------------------------------------------------

--
-- Table structure for table `enchere`
--

CREATE TABLE `enchere` (
  `id` int(11) NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `prix_plancher` float NOT NULL,
  `coup_de_coeur` tinyint(1) DEFAULT NULL,
  `timbre_id` int(11) NOT NULL,
  `createur_id` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `enchere`
--

INSERT INTO `enchere` (`id`, `date_debut`, `date_fin`, `prix_plancher`, `coup_de_coeur`, `timbre_id`, `createur_id`) VALUES
(73, '2024-01-05 17:04:00', '2024-01-27 17:04:00', 51, 1, 50, 'usager2@gmail.com'),
(78, '2024-01-06 15:02:00', '2024-01-20 15:02:00', 26, NULL, 94, 'usager1@gmail.com'),
(79, '2024-01-05 15:02:00', '2024-01-25 15:02:00', 37, NULL, 95, 'usager1@gmail.com'),
(80, '2024-01-05 15:03:00', '2024-01-19 15:03:00', 42.9, 1, 97, 'usager1@gmail.com'),
(81, '2024-01-05 15:07:00', '2024-01-28 15:07:00', 23, NULL, 64, 'usager2@gmail.com'),
(82, '2024-01-06 15:08:00', '2024-01-08 15:08:00', 42, NULL, 100, 'usager3@gmail.com'),
(83, '2024-01-05 15:09:00', '2024-01-09 15:09:00', 18, 1, 98, 'usager2@gmail.com'),
(84, '2024-01-05 15:10:00', '2024-01-22 15:10:00', 70, NULL, 99, 'usager2@gmail.com'),
(85, '2024-01-10 12:01:00', '2024-01-11 12:01:00', 12, NULL, 55, 'usager3@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `etat`
--

CREATE TABLE `etat` (
  `id` int(11) NOT NULL,
  `nom` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `etat`
--

INSERT INTO `etat` (`id`, `nom`) VALUES
(1, 'Parfait'),
(2, 'Excellent'),
(3, 'Bon'),
(4, 'Moyen'),
(5, 'Endommagé');

-- --------------------------------------------------------

--
-- Table structure for table `favori`
--

CREATE TABLE `favori` (
  `usager_id` varchar(45) NOT NULL,
  `enchere_id` int(11) NOT NULL,
  `timbre_id` int(11) NOT NULL,
  `createur_id` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `favori`
--

INSERT INTO `favori` (`usager_id`, `enchere_id`, `timbre_id`, `createur_id`) VALUES
('usager2@gmail.com', 82, 100, 'usager3@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

CREATE TABLE `image` (
  `id` int(11) NOT NULL,
  `timbre_id` int(11) NOT NULL,
  `nom` varchar(225) NOT NULL,
  `principal` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `image`
--

INSERT INTO `image` (`id`, `timbre_id`, `nom`, `principal`) VALUES
(134, 55, 'Renoncule_20240104215922_673563691.jpg', 1),
(135, 55, 'Renoncule_20240104215922_766415292.jpg', 0),
(136, 55, 'Renoncule_20240104215922_1084557258.jpg', 0),
(137, 50, 'Willie_O’Ree_-_change_20240104220236_255407537.jpg', 0),
(138, 50, 'Willie_O’Ree_-_change_20240104220236_1946009290.jpg', 1),
(139, 50, 'Willie_O’Ree_-_change_20240104220236_343880031.jpg', 0),
(159, 94, 'Fêtes_–_Scènes_d’hiver__20240105200214_1294124367.jpg', 0),
(160, 94, 'Fêtes_–_Scènes_d’hiver__20240105200214_389340120.jpg', 1),
(161, 94, 'Fêtes_–_Scènes_d’hiver__20240105200214_2119154056.jpg', 0),
(162, 95, 'Aïd_(2023)__20240105200247_12347602.jpg', 0),
(163, 95, 'Aïd_(2023)__20240105200247_816845224.jpg', 1),
(164, 95, 'Aïd_(2023)__20240105200247_1338308324.jpg', 0),
(165, 97, 'Denys_Arcand__20240105200332_1162993655.jpg', 0),
(166, 97, 'Denys_Arcand__20240105200332_740913601.jpg', 1),
(167, 97, 'Denys_Arcand__20240105200332_1438398667.jpg', 0),
(168, 64, 'Dirigeants_autochtones_20240105200523_8513439.jpg', 0),
(169, 64, 'Dirigeants_autochtones_20240105200523_1044883738.jpg', 1),
(170, 64, 'Dirigeants_autochtones_20240105200523_1321917245.jpg', 0),
(171, 100, 'Féministes_du_Québec__20240105200824_1433624978.jpg', 0),
(172, 100, 'Féministes_du_Québec__20240105200824_622641330.jpg', 1),
(173, 100, 'Féministes_du_Québec__20240105200824_31757095.jpg', 0),
(174, 98, 'Noël_(2023)_–_Vierge_à_l’Enfant__20240105200943_1896764742.jpg', 0),
(175, 98, 'Noël_(2023)_–_Vierge_à_l’Enfant__20240105200943_2139762176.jpg', 0),
(176, 98, 'Noël_(2023)_–_Vierge_à_l’Enfant__20240105200943_1867023774.jpg', 1),
(177, 99, 'Dirigeants_autochtones_–_Thelma_Chalifoux_20240105201017_1706742013.jpg', 0),
(178, 99, 'Dirigeants_autochtones_–_Thelma_Chalifoux_20240105201017_2000257993.jpg', 0),
(179, 99, 'Dirigeants_autochtones_–_Thelma_Chalifoux_20240105201017_1758326730.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `offre`
--

CREATE TABLE `offre` (
  `id` int(11) NOT NULL,
  `prix` float NOT NULL,
  `usager_id` varchar(45) NOT NULL,
  `enchere_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `offre`
--

INSERT INTO `offre` (`id`, `prix`, `usager_id`, `enchere_id`) VALUES
(30, 38, 'usager2@gmail.com', 79),
(31, 43, 'usager2@gmail.com', 82),
(32, 43, 'usager2@gmail.com', 80),
(33, 44, 'usager6@gmail.com', 80),
(34, 19, 'usager6@gmail.com', 83),
(35, 45, 'usager3@gmail.com', 80);

-- --------------------------------------------------------

--
-- Table structure for table `pays`
--

CREATE TABLE `pays` (
  `id` int(11) NOT NULL,
  `nom` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pays`
--

INSERT INTO `pays` (`id`, `nom`) VALUES
(1, 'Canada'),
(2, 'États-Unis'),
(3, 'Allemagne'),
(4, 'Royaume-Uni'),
(5, 'Turquie'),
(6, 'Syrie'),
(7, 'Inde'),
(8, 'Mexique'),
(9, 'Maroc'),
(10, 'Japon'),
(11, 'Islande');

-- --------------------------------------------------------

--
-- Table structure for table `privilege`
--

CREATE TABLE `privilege` (
  `id` int(11) NOT NULL,
  `nom` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `privilege`
--

INSERT INTO `privilege` (`id`, `nom`) VALUES
(1, 'admin'),
(2, 'usager');

-- --------------------------------------------------------

--
-- Table structure for table `timbre`
--

CREATE TABLE `timbre` (
  `id` int(11) NOT NULL,
  `nom` varchar(45) NOT NULL,
  `nom_2` varchar(100) DEFAULT NULL,
  `date_emission` date DEFAULT NULL,
  `couleur` tinyint(1) DEFAULT NULL,
  `tirage` int(11) DEFAULT NULL,
  `extrait` text,
  `certification` tinyint(1) DEFAULT NULL,
  `etat_id` int(11) NOT NULL,
  `dimension_id` int(11) NOT NULL,
  `pays_id` int(11) NOT NULL,
  `createur_id` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `timbre`
--

INSERT INTO `timbre` (`id`, `nom`, `nom_2`, `date_emission`, `couleur`, `tirage`, `extrait`, `certification`, `etat_id`, `dimension_id`, `pays_id`, `createur_id`) VALUES
(50, 'Willie O’Ree - change', 'Carnet de 6 timbres PermanentsMC au tarif du régime intérieur', '2022-06-21', 1, 100, 'Ce timbre rend hommage au pionnier Willie O’Ree, le premier Noir à disputer un match de la Ligue nationale de hockey (LNHMD). Il célèbre son héritage remarquable et ses réalisations exceptionnelles, qui ont fait du hockey un sport plus diversifié et inclusif.', 1, 1, 3, 1, 'usager2@gmail.com'),
(55, 'Renoncule', 'Timbres Permanents au tarif du régime intérieur – carnet de 10', '2006-11-07', 1, 1500, 'L’émission consacrée aux fleurs de cette année, qui comporte 2 timbres, met en vedette la Ranunculus asiaticus. Toujours très populaires, ces vignettes sont souvent utilisées pour les mariages, notamment sur les faire-part. Elles sont également fort appréciées des passionnés de jardinage. Ajoutez ce carnet de 10 timbres PermanentsMC au tarif du régime intérieur à votre collection ou offrez-le en cadeau.', 1, 3, 3, 4, 'usager3@gmail.com'),
(64, 'Dirigeants autochtones', 'Jose Kusugak : Carnet de 6 timbres Permanents au tarif du régime intérieur', '2012-10-04', 1, 1000, 'Célébrez Jose Kusugak, activiste inuit, linguiste et communicateur, avec ce carnet de 6 timbres Permanents au tarif du régime intérieur.', 1, 2, 1, 1, 'usager2@gmail.com'),
(94, 'Fêtes – Scènes d’hiver ', 'Carnet de 12 timbres Permanents au tarif du régime intérieur', '2001-03-22', 1, 1000, 'Postes Canada perpétue sa tradition annuelle d’émissions de timbres des Fêtes avec Scènes d’hiver des Fêtes : Carnet de 12 timbres PermanentsMC au tarif du régime intérieur.', 1, 3, 5, 2, 'usager1@gmail.com'),
(95, 'Aïd (2023) ', 'Timbres Permanents au tarif du régime intérieur – carnet de 6', '2001-06-07', 1, 20, 'Aïd Moubarak! Célébrez les fêtes musulmanes de l’Aïd al-Fitr et de l’Aïd al-Adha avec ce carnet de 6 timbres.', 1, 4, 4, 6, 'usager1@gmail.com'),
(97, 'Denys Arcand ', 'Carnet de 6 timbres Permanents au tarif du régime intérieur', '2013-04-11', 1, 1000, 'Ce timbre spécial célèbre le grand cinéaste canadien Denys Arcand, qui a écrit et réalisé plus d’une vingtaine de films, d’émissions de télévision et de documentaires. Reconnu pour sa curiosité intellectuelle et sa passion pour l’art, la politique et la vie, il a marqué l’industrie canadienne du divertissement par ses œuvres. Ce timbre rend hommage à son influence et à ses réalisations.', 1, 4, 3, 9, 'usager1@gmail.com'),
(98, 'Noël (2023) – Vierge à l’Enfant ', 'Carnet de 12 timbres Permanents au tarif du régime intérieur', '2018-12-01', 1, 10000, 'Ces timbres populaires sont mis en vente avant le début des festivités de sorte que vous puissiez vous les procurer pour égayer votre courrier des Fêtes.', 1, 2, 1, 5, 'usager2@gmail.com'),
(99, 'Dirigeants autochtones – Thelma Chalifoux', 'Carnet de 6 timbres Permanents au tarif du régime intérieur', '2020-08-06', 1, 20000, 'Ce timbre fait partie de la deuxième émission de la série sur les dirigeants autochtones rendant hommage aux personnes qui ont apporté des contributions importantes et changements positifs et durables à la société canadienne. En soulignant les réalisations de ces dirigeants des Premières Nations, des Inuits et des Métis modernes, la série célèbre leur engagement à préserver leurs cultures et à améliorer la qualité de vie des peuples autochtones au Canada.', 1, 2, 2, 1, 'usager2@gmail.com'),
(100, 'Féministes du Québec ', 'Carnet de 6 timbres Permanents au tarif du régime intérieur', '2012-11-23', 1, 33, 'Cette émission de timbre rend hommage aux féministes québécoises influentes qui ont été à l’avant-garde de la lutte pour les droits des femmes et des travailleurs et travailleuses, et pour combattre les inégalités sociales et économiques dans leur province.\r\n\r\n', 1, 1, 3, 1, 'usager3@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `usager`
--

CREATE TABLE `usager` (
  `id` varchar(45) NOT NULL,
  `password` varchar(225) NOT NULL,
  `alias` varchar(45) DEFAULT NULL,
  `privilege_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `usager`
--

INSERT INTO `usager` (`id`, `password`, `alias`, `privilege_id`) VALUES
('usager1@gmail.com', '$2y$10$66ZIcE1YkeDrIyZXnGXO4ugUTxytr8gwt5yevRtGv2wJ.hOlo2hj6', 'Usager1', 2),
('usager2@gmail.com', '$2y$10$061qGoSnAE7TfJ8APTpNrOEdQPuVyX5IfvjSQifv/zfC/VOyRT1HW', 'Usager2', 2),
('usager3@gmail.com', '$2y$10$hS.OYmgQdD1xyOTwYFwLg.dlQkWLi1fddX8rHVXfUlTTCutkipQmq', 'Usager3', 2),
('usager6@gmail.com', '$2y$10$8MVWrGtG3hUvuaKEbSswOOHiT.CzZ1CLSwG5llCO.CRWWEC7nFQS6', 'Usager6', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dimension`
--
ALTER TABLE `dimension`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enchere`
--
ALTER TABLE `enchere`
  ADD PRIMARY KEY (`id`,`timbre_id`,`createur_id`),
  ADD KEY `fk_enchere_timbre1_idx` (`timbre_id`,`createur_id`);

--
-- Indexes for table `etat`
--
ALTER TABLE `etat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favori`
--
ALTER TABLE `favori`
  ADD PRIMARY KEY (`usager_id`,`enchere_id`,`timbre_id`,`createur_id`),
  ADD KEY `fk_favori_usager1_idx` (`usager_id`),
  ADD KEY `fk_favori_enchere1_idx` (`enchere_id`,`timbre_id`,`createur_id`);

--
-- Indexes for table `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_image_timbre1_idx` (`timbre_id`);

--
-- Indexes for table `offre`
--
ALTER TABLE `offre`
  ADD PRIMARY KEY (`id`,`usager_id`,`enchere_id`),
  ADD KEY `fk_offre_usager1_idx` (`usager_id`),
  ADD KEY `fk_offre_enchere1_idx` (`enchere_id`);

--
-- Indexes for table `pays`
--
ALTER TABLE `pays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `privilege`
--
ALTER TABLE `privilege`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timbre`
--
ALTER TABLE `timbre`
  ADD PRIMARY KEY (`id`,`createur_id`),
  ADD KEY `fk_timbre_condition1_idx` (`etat_id`),
  ADD KEY `fk_timbre_dimension1_idx` (`dimension_id`),
  ADD KEY `fk_timbre_pays1_idx` (`pays_id`),
  ADD KEY `fk_timbre_usager1_idx` (`createur_id`);

--
-- Indexes for table `usager`
--
ALTER TABLE `usager`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom_usager_UNIQUE` (`id`),
  ADD KEY `fk_usager_privilege1_idx` (`privilege_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dimension`
--
ALTER TABLE `dimension`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `enchere`
--
ALTER TABLE `enchere`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `etat`
--
ALTER TABLE `etat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `image`
--
ALTER TABLE `image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=181;

--
-- AUTO_INCREMENT for table `offre`
--
ALTER TABLE `offre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `pays`
--
ALTER TABLE `pays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `privilege`
--
ALTER TABLE `privilege`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `timbre`
--
ALTER TABLE `timbre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enchere`
--
ALTER TABLE `enchere`
  ADD CONSTRAINT `fk_enchere_timbre1` FOREIGN KEY (`timbre_id`,`createur_id`) REFERENCES `timbre` (`id`, `createur_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `favori`
--
ALTER TABLE `favori`
  ADD CONSTRAINT `fk_favori_enchere1` FOREIGN KEY (`enchere_id`,`timbre_id`,`createur_id`) REFERENCES `enchere` (`id`, `timbre_id`, `createur_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_favori_usager1` FOREIGN KEY (`usager_id`) REFERENCES `usager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `image`
--
ALTER TABLE `image`
  ADD CONSTRAINT `fk_image_timbre1` FOREIGN KEY (`timbre_id`) REFERENCES `timbre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `offre`
--
ALTER TABLE `offre`
  ADD CONSTRAINT `fk_offre_enchere1` FOREIGN KEY (`enchere_id`) REFERENCES `enchere` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_offre_usager1` FOREIGN KEY (`usager_id`) REFERENCES `usager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `timbre`
--
ALTER TABLE `timbre`
  ADD CONSTRAINT `fk_timbre_condition1` FOREIGN KEY (`etat_id`) REFERENCES `etat` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_timbre_dimension1` FOREIGN KEY (`dimension_id`) REFERENCES `dimension` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_timbre_pays1` FOREIGN KEY (`pays_id`) REFERENCES `pays` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_timbre_usager1` FOREIGN KEY (`createur_id`) REFERENCES `usager` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `usager`
--
ALTER TABLE `usager`
  ADD CONSTRAINT `fk_usager_privilege1` FOREIGN KEY (`privilege_id`) REFERENCES `privilege` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
