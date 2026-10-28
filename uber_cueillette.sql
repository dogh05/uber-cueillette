-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 05 avr. 2026 à 01:01
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `uber_cueillette`
--

-- --------------------------------------------------------

--
-- Structure de la table `agriculteur`
--

CREATE TABLE `agriculteur` (
  `id_agriculteur` int(11) NOT NULL,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `CIN` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `adresse` varchar(50) DEFAULT NULL,
  `pseudo` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `agriculteur`
--

-- Account seed data removed for public sharing.


-- --------------------------------------------------------

--
-- Structure de la table `candidate`
--

CREATE TABLE `candidate` (
  `id_candidate` int(11) NOT NULL,
  `id_offre` int(11) NOT NULL,
  `id_ouvrier` int(11) NOT NULL,
  `decision` varchar(10) DEFAULT 'en_attente',
  `date_candidature` timestamp NOT NULL DEFAULT current_timestamp(),
  `note` int(11) DEFAULT NULL,
  `commentaire` varchar(50) DEFAULT NULL,
  `remuneration` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gouvernement`
--

CREATE TABLE `gouvernement` (
  `id_gouvernorat` int(11) NOT NULL,
  `libelle` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `gouvernement`
--

INSERT INTO `gouvernement` (`id_gouvernorat`, `libelle`) VALUES
(1, 'Tunis'),
(2, 'Ariana'),
(3, 'Ben Arous'),
(4, 'Manouba'),
(5, 'Nabeul'),
(6, 'Zaghouan'),
(7, 'Bizerte'),
(8, 'Béja'),
(9, 'Jendouba'),
(10, 'Le Kef'),
(11, 'Siliana'),
(12, 'Sousse'),
(13, 'Monastir'),
(14, 'Mahdia'),
(15, 'Sfax'),
(16, 'Kairouan'),
(17, 'Kasserine'),
(18, 'Sidi Bouzid'),
(19, 'Gabès'),
(20, 'Médenine'),
(21, 'Tataouine'),
(22, 'Gafsa'),
(23, 'Tozeur'),
(24, 'Kébili');

-- --------------------------------------------------------

--
-- Structure de la table `offre`
--

CREATE TABLE `offre` (
  `id_offre` int(11) NOT NULL,
  `id_type_fruit` int(11) NOT NULL,
  `id_gouvernorat` int(11) NOT NULL,
  `adresse` varchar(50) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `nombre_ouvriers` int(11) NOT NULL,
  `prix_journee` float NOT NULL,
  `date_limitte` date NOT NULL,
  `id_agriculteur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ouvrier`
--

CREATE TABLE `ouvrier` (
  `id_ouvrier` int(11) NOT NULL,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `CIN` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `photo` longblob DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `pseudo` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ouvrier`
--

-- Account seed data removed for public sharing.


-- --------------------------------------------------------

--
-- Structure de la table `type_fruit`
--

CREATE TABLE `type_fruit` (
  `id_type_fruit` int(11) NOT NULL,
  `libelle` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `type_fruit`
--

INSERT INTO `type_fruit` (`id_type_fruit`, `libelle`) VALUES
(1, 'Olives'),
(2, 'Agrumes'),
(3, 'Tomates'),
(4, 'Raisins'),
(5, 'Pommes'),
(6, 'Poires'),
(7, 'Pêches'),
(8, 'Abricots');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `agriculteur`
--
ALTER TABLE `agriculteur`
  ADD PRIMARY KEY (`id_agriculteur`),
  ADD UNIQUE KEY `CIN` (`CIN`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `pseudo` (`pseudo`);

--
-- Index pour la table `candidate`
--
ALTER TABLE `candidate`
  ADD PRIMARY KEY (`id_candidate`),
  ADD UNIQUE KEY `unique_candidature` (`id_offre`,`id_ouvrier`),
  ADD KEY `id_ouvrier` (`id_ouvrier`);

--
-- Index pour la table `gouvernement`
--
ALTER TABLE `gouvernement`
  ADD PRIMARY KEY (`id_gouvernorat`);

--
-- Index pour la table `offre`
--
ALTER TABLE `offre`
  ADD PRIMARY KEY (`id_offre`),
  ADD KEY `id_type_fruit` (`id_type_fruit`),
  ADD KEY `id_gouvernorat` (`id_gouvernorat`),
  ADD KEY `id_agriculteur` (`id_agriculteur`);

--
-- Index pour la table `ouvrier`
--
ALTER TABLE `ouvrier`
  ADD PRIMARY KEY (`id_ouvrier`),
  ADD UNIQUE KEY `CIN` (`CIN`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `pseudo` (`pseudo`);

--
-- Index pour la table `type_fruit`
--
ALTER TABLE `type_fruit`
  ADD PRIMARY KEY (`id_type_fruit`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `agriculteur`
--
ALTER TABLE `agriculteur`
  MODIFY `id_agriculteur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `candidate`
--
ALTER TABLE `candidate`
  MODIFY `id_candidate` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `gouvernement`
--
ALTER TABLE `gouvernement`
  MODIFY `id_gouvernorat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `offre`
--
ALTER TABLE `offre`
  MODIFY `id_offre` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `ouvrier`
--
ALTER TABLE `ouvrier`
  MODIFY `id_ouvrier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `type_fruit`
--
ALTER TABLE `type_fruit`
  MODIFY `id_type_fruit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `candidate`
--
ALTER TABLE `candidate`
  ADD CONSTRAINT `candidate_ibfk_1` FOREIGN KEY (`id_offre`) REFERENCES `offre` (`id_offre`),
  ADD CONSTRAINT `candidate_ibfk_2` FOREIGN KEY (`id_ouvrier`) REFERENCES `ouvrier` (`id_ouvrier`);

--
-- Contraintes pour la table `offre`
--
ALTER TABLE `offre`
  ADD CONSTRAINT `offre_ibfk_1` FOREIGN KEY (`id_type_fruit`) REFERENCES `type_fruit` (`id_type_fruit`),
  ADD CONSTRAINT `offre_ibfk_2` FOREIGN KEY (`id_gouvernorat`) REFERENCES `gouvernement` (`id_gouvernorat`),
  ADD CONSTRAINT `offre_ibfk_3` FOREIGN KEY (`id_agriculteur`) REFERENCES `agriculteur` (`id_agriculteur`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
