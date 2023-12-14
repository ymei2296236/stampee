-- phpMyAdmin SQL Dump
-- version 4.9.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Dec 13, 2023 at 11:26 PM
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
(28, '2023-12-14 00:00:00', '2023-12-17 00:00:00', 11.25, NULL, 50, 'usager2@gmail.com'),
(29, '2023-12-14 00:00:00', '2023-12-20 00:00:00', 23.4, NULL, 53, 'usager1@gmail.com'),
(30, '2023-12-14 00:00:00', '2023-12-22 00:00:00', 2, NULL, 54, 'usager2@gmail.com');

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
  `nom` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `image`
--

INSERT INTO `image` (`id`, `timbre_id`, `nom`) VALUES
(32, 50, 'Willie O’Ree _20231209212412_1805702381.jpg'),
(33, 50, 'Willie O’Ree _20231209212412_286785568.jpg'),
(34, 50, 'Willie O’Ree _20231209212412_1254081298.jpg'),
(36, 53, 'Dirigeants autochtones _20231210214511_1403176022.jpg'),
(37, 53, 'Dirigeants autochtones _20231210214511_1979419090.jpg'),
(38, 53, 'Dirigeants autochtones _20231210214511_707379733.jpg'),
(39, 54, 'Fondation communautaire _20231211195907_461085222.jpg'),
(40, 54, 'Fondation communautaire _20231211195907_2035619749.jpg'),
(41, 54, 'Fondation communautaire _20231211195907_600925195.jpg'),
(42, 55, 'Renoncule_20231211200608_1899987206.jpg'),
(43, 55, 'Renoncule_20231211200608_1890416677.jpg'),
(44, 55, 'Renoncule_20231211200608_1070822799.jpg');

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
  `nom` varchar(100) NOT NULL,
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
(50, 'Willie O’Ree ', 'Carnet de 6 timbres PermanentsMC au tarif du régime intérieur', '2022-06-21', 1, 100000, 'Célébrez Jose Kusugak, activiste inuit, linguiste et communicateur, avec ce carnet de 6 timbres PermanentsMC au tarif du régime intérieur.</p>                 Le recto du carnet met en vedette un agrandissement du timbre. L’intérieur du livret présente une photo d’enfance de Jose (à droite) avec d’autres membres de sa famille, vers 1955.', 1, 1, 3, 1, 'usager2@gmail.com'),
(53, 'Dirigeants autochtones ', 'Jose Kusugak : Carnet de 6 timbres Permanents au tarif du régime intérieur', '1998-02-04', 1, 2000, 'Célébrez Jose Kusugak, activiste inuit, linguiste et communicateur, avec ce carnet de 6 timbres PermanentsMC au tarif du régime intérieur.', 1, 3, 4, 2, 'usager1@gmail.com'),
(54, 'Fondation communautaire ', 'Don de 1 $ par carnet de 10 timbres Permanents au tarif du régime intérieur', '2017-01-01', 1, 1500, 'Notre timbre-poste philanthropique annuel est arrivé. Le dollar supplémentaire que vous déboursez à l’achat du carnet de 10 timbres est versé à la Fondation communautaire, laquelle appuie des organismes sans but lucratif locaux et nationaux qui créent des espaces où les jeunes peuvent s’épanouir.', 1, 2, 5, 5, 'usager2@gmail.com'),
(55, 'Renoncule', 'Timbres Permanents au tarif du régime intérieur – carnet de 10', '2006-11-07', 1, 1500, 'L’émission consacrée aux fleurs de cette année, qui comporte 2 timbres, met en vedette la Ranunculus asiaticus. Toujours très populaires, ces vignettes sont souvent utilisées pour les mariages, notamment sur les faire-part. Elles sont également fort appréciées des passionnés de jardinage. Ajoutez ce carnet de 10 timbres PermanentsMC au tarif du régime intérieur à votre collection ou offrez-le en cadeau.', 1, 3, 3, 4, 'usager3@gmail.com'),
(56, 'Oiseaux des Fêtes – Geai bleu', 'Carnet de 6 timbres au tarif des envois à destination des États-Unis', '2013-10-11', NULL, 12000, '', 1, 1, 2, 6, 'usager1@gmail.com');

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
('usager8@gmail.com', '$2y$10$X6.GwwyqPYpoImw76rMI4eoVxZ.u2Vk3X1vnojuDm2KhzkxGX6HrW', 'Usager8', 2),
('usager9@gmail.com', '$2y$10$SSrxxd7AbpPcwjBEm/ZocuB8yccG4ZuDrYqEA.ZUrJMngTxbTWPT2', 'Usager9', 2);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `etat`
--
ALTER TABLE `etat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `image`
--
ALTER TABLE `image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `offre`
--
ALTER TABLE `offre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

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
