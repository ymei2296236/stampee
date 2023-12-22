-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 21, 2023 at 08:56 PM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 8.0.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

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
(1, 'rouleau'),
(2, 'carnet'),
(3, 'feuillet'),
(4, 'bande'),
(5, 'simple'),
(6, 'autres');

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
(28, '2023-12-14 00:00:00', '2023-12-17 00:00:00', 13.35, NULL, 50, 'usager2@gmail.com'),
(48, '2023-12-21 14:00:00', '2023-12-23 14:00:00', 16.5, NULL, 70, 'usager1@gmail.com'),
(49, '2023-12-22 14:01:00', '2023-12-28 14:01:00', 23, NULL, 53, 'usager1@gmail.com'),
(50, '2023-12-21 14:04:00', '2023-12-29 14:04:00', 15.9, NULL, 71, 'usager2@gmail.com'),
(52, '2023-12-24 14:08:00', '2023-12-29 14:08:00', 45, NULL, 72, 'usager3@gmail.com');

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
(1, 'parfait'),
(2, 'excellent'),
(3, 'bon'),
(4, 'moyen'),
(5, 'endommagé');

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
(32, 50, 'Willie O’Ree _20231209212412_1805702381.jpg', 1),
(33, 50, 'Willie O’Ree _20231209212412_286785568.jpg', 0),
(34, 50, 'Willie O’Ree _20231209212412_1254081298.jpg', 0),
(36, 53, 'Dirigeants autochtones _20231210214511_1403176022.jpg', 1),
(37, 53, 'Dirigeants autochtones _20231210214511_1979419090.jpg', 0),
(38, 53, 'Dirigeants autochtones _20231210214511_707379733.jpg', 0),
(42, 55, 'Renoncule_20231211200608_1899987206.jpg', 1),
(43, 55, 'Renoncule_20231211200608_1890416677.jpg', 0),
(44, 55, 'Renoncule_20231211200608_1070822799.jpg', 0),
(53, 65, 'test_20231221193102_1128919082.jpg', 1),
(58, 70, 'Oiseaux_des_Fêtes_–_Geai_bleu_20231221200030_487147225.jpg', 1),
(59, 70, 'Oiseaux_des_Fêtes_–_Geai_bleu_20231221200030_836001814.jpg', 0),
(60, 70, 'Oiseaux_des_Fêtes_–_Geai_bleu_20231221200030_1256942602.jpg', 0),
(61, 71, 'Pochette_trimestrielle_du_collectionneur_20231221200426_780436851.jpg', 1),
(62, 71, 'Pochette_trimestrielle_du_collectionneur_20231221200426_941671026.jpg', 0),
(63, 72, 'Calla__20231221200845_1870357675.jpg', 0),
(64, 72, 'Calla__20231221200845_481147538.jpg', 0),
(65, 72, 'Calla__20231221200845_2087263620.jpg', 1);

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
(13, 17, 'usager3@gmail.com', 48),
(14, 18, 'usager3@gmail.com', 48),
(15, 16, 'usager3@gmail.com', 50);

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
  `extrait` text DEFAULT NULL,
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
(50, 'Willie O’Ree ', 'Carnet de 6 timbres PermanentsMC au tarif du régime intérieur', '2022-06-21', 1, 100000, 'Célébrez Jose Kusugak, activiste inuit, linguiste et communicateur, avec ce carnet de 6 timbres PermanentsMC au tarif du régime intérieur.</p>                 Le recto du carnet met en vedette un agrandissement du timbre. L’intérieur du livret présente une photo d’enfance de Jose (à droite) avec d’autres membres de sa famille, vers 1955.', 1, 3, 3, 1, 'usager2@gmail.com'),
(53, 'Dirigeants autochtones ', 'Jose Kusugak : Carnet de 6 timbres Permanents au tarif du régime intérieur', '1998-02-04', 1, 2000, 'Célébrez Jose Kusugak, activiste inuit, linguiste et communicateur, avec ce carnet de 6 timbres PermanentsMC au tarif du régime intérieur.', NULL, 1, 4, 2, 'usager1@gmail.com'),
(55, 'Renoncule', 'Timbres Permanents au tarif du régime intérieur – carnet de 10', '2006-11-07', 1, 1500, 'L’émission consacrée aux fleurs de cette année, qui comporte 2 timbres, met en vedette la Ranunculus asiaticus. Toujours très populaires, ces vignettes sont souvent utilisées pour les mariages, notamment sur les faire-part. Elles sont également fort appréciées des passionnés de jardinage. Ajoutez ce carnet de 10 timbres PermanentsMC au tarif du régime intérieur à votre collection ou offrez-le en cadeau.', 1, 1, 3, 4, 'usager3@gmail.com'),
(65, 'test', '', '2001-01-01', NULL, 0, '', NULL, 1, 2, 1, 'usager2@gmail.com'),
(70, 'Oiseaux des Fêtes – Geai bleu', 'Carnet de 6 timbres au tarif des envois à destination des États-Unis', '2001-01-01', 1, 0, 'Depuis 1964, Postes Canada émet des timbres des Fêtes chaque année. Joignez-vous aux célébrations des Fêtes avec ce carnet de 6 timbres au tarif des envois à destination des États-Unis de notre émission Oiseaux des Fêtes mettant en vedette le geai bleu.', 1, 2, 1, 1, 'usager1@gmail.com'),
(71, 'Pochette trimestrielle du collectionneur', ' janvier – mars 2022', '2004-10-20', NULL, 7000, 'Cette pochette spéciale contient une sélection de timbres et de blocs-feuillets des émissions du trimestre. À l’intérieur, vous trouverez des produits philatéliques des émissions Eleanor Collins, Jubilé de platine et Calla. Cette pièce-souvenir unique commémore des moments historiques, des individus et la riche diversité du Canada.', NULL, 3, 3, 8, 'usager2@gmail.com'),
(72, 'Calla ', 'Bloc-feuillet avec surcharge', '2010-07-10', 1, 500, 'Ce bloc-feuillet avec surcharge spécial mettant en vedette les 2 timbres de l’émission florale de 2022 consacrée à la calla est lancé en prévision de l’exposition philatélique internationale CAPEX 22, qui aura lieu à Toronto du 9 au 12 juin 2022. L’image qui l’orne est composée d’une calla blanche fermée et d’une calla rose ouverte. Le logo de CAPEX 22 figure dans le coin inférieur gauche.', 1, 2, 3, 4, 'usager3@gmail.com');

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
('usager6@gmail.com', '$2y$10$iWrzJ1NBIEnFjbWPRjvRl.sxZjQ8OZe4vKxyxbhPXeMYLjZrRl48G', 'Usager6', 2);

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
  ADD KEY `fk_timbre_dimension1_idx` (`dimension_id`),
  ADD KEY `fk_timbre_pays1_idx` (`pays_id`),
  ADD KEY `fk_timbre_usager1_idx` (`createur_id`),
  ADD KEY `fk_timbre_condition1_idx` (`etat_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `etat`
--
ALTER TABLE `etat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `image`
--
ALTER TABLE `image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `offre`
--
ALTER TABLE `offre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
