-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 28 mars 2022 à 01:20
-- Version du serveur :  10.4.16-MariaDB
-- Version de PHP : 7.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `affiliate`
--

-- --------------------------------------------------------

--
-- Structure de la table `provinces`
--

CREATE TABLE `provinces` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

--
-- Déchargement des données de la table `provinces`
--

INSERT INTO `provinces` (`id`, `name`) VALUES
(1, 'ولاية الخرطوم'),
(2, 'ولاية الجزيرة'),
(3, 'ولاية البحر الأحمر'),
(4, 'ولاية كسلا'),
(5, 'ولاية القضارف'),
(6, 'ولاية سنار'),
(7, 'ولاية النيل الأبيض'),
(8, 'ولاية النيل الأزرق'),
(9, 'الولاية الشمالية'),
(10, 'ولاية نهر النيل'),
(11, 'ولاية شمال كردفان'),
(12, 'ولاية غرب كردفان'),
(13, 'ولاية جنوب كردفان'),
(14, 'ولاية شمال دارفور'),
(15, 'ولاية غرب دارفور'),
(16, 'ولاية جنوب دارفور'),
(17, 'ولاية شرق دارفور'),
(18, 'ولاية وسط دارفور');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `provinces`
--
ALTER TABLE `provinces`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3890;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
