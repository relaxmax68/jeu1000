-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Client :  localhost:3306
-- Généré le :  Dim 16 Juin 2019 à 08:07
-- Version du serveur :  5.7.26-0ubuntu0.18.04.1
-- Version de PHP :  7.2.19-0ubuntu0.18.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `jeu.humandata.info`
--

-- --------------------------------------------------------

--
-- Structure de la table `jeu`
--

CREATE TABLE `jeu` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `score` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jeu_player`
--

CREATE TABLE `jeu_player` (
  `jeu_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `level`
--

CREATE TABLE `level` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration` int(11) DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `level`
--

INSERT INTO `level` (`id`, `name`, `duration`, `status`) VALUES
(1, 'Question Bleue', 30, 'primary'),
(2, 'Question Blanche', 30, 'secondary'),
(3, 'Question Rouge', 30, 'danger'),
(4, 'Banco', 60, 'success'),
(5, 'Super Banco', 60, 'dark'),
(6, 'Question de repêchage', 15, 'warning');

-- --------------------------------------------------------

--
-- Structure de la table `migration_versions`
--

CREATE TABLE `migration_versions` (
  `version` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `migration_versions`
--

INSERT INTO `migration_versions` (`version`, `executed_at`) VALUES
('20190614140820', '2019-06-14 14:08:38'),
('20190614163156', '2019-06-14 16:32:03'),
('20190614165216', '2019-06-14 16:52:23'),
('20190615163652', '2019-06-15 16:37:01');

-- --------------------------------------------------------

--
-- Structure de la table `player`
--

CREATE TABLE `player` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `score` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `question`
--

CREATE TABLE `question` (
  `id` int(11) NOT NULL,
  `level_id` int(11) DEFAULT NULL,
  `question` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `points` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `question`
--

INSERT INTO `question` (`id`, `level_id`, `question`, `answer`, `points`) VALUES
(1, 2, 'Quelles sont les opérations à effectuer pour éteindre son ordinateur', 'Cliquer sur le menu Démarrer puis cliquer sur Arrêter', 1),
(2, 1, 'Comment déplacer une fenêtre avec sa souris ?', 'Il faut cliquer sur la barre du haut de la fenêtre et maintenir le click jusqu\'à l\'endroit souhaité', 5),
(3, 2, 'Un écran est composé de petits points qui permettent d\'afficher les images. Comment s\'appellent ces points ?', 'Des pixels', 5),
(4, 2, 'C\'est un ensemble de programmes qui permet à un ordinateur d\'assurer une tâche ou une fonction en particulier', 'un logiciel', 5),
(5, 2, 'Classe ces périphériques informatiques du plus ancien au plus récent : clé USB - Souris - Écran - Webcam', 'Écran - Souris -Webcam - clé USB', 5),
(6, 1, 'À quoi sert la combinaison de touches CTRL + Z', 'À annuler la dernière action/modification ou à revenir d\'une action en arrière dans un logiciel de bureautique', 10),
(7, 4, 'Que signifie WWW ?', 'World Wide Web', 20),
(8, 6, 'Entre l\'antivirus Avast, Microsoft Excel, CD-Rom et Internet Explorer Quel élément n\'appartient pas à la famille des «software» ?', 'le CD-ROM', 0),
(9, 3, 'À quoi sert une adresse IP ?', 'À identifier chaque ordinateur (ou objet) connecté à internet', 10),
(10, 2, 'Citez moi  trois noms de logiciels de bureautique.', 'Excel Word Publisher Writer Calc Number…', 5),
(11, 2, 'Quel est l\'intrus ? clé USB - carte CD - Poste de travail - Disque Dur', 'Poste de travail', 5),
(12, 2, 'Comment appelle-t\'on les différents composants de l\'ordinateur ?', 'Les périphériques', 5),
(13, 6, 'Un ordinateur d\'entrée de gamme peut-il suffir à surfer sur internet, rédiger des courriels et jouer à des jeux simples ?', 'oui', NULL),
(14, 3, 'Que signifie le A de ADSL ?', 'Asymmetrical', 10),
(15, 3, 'À quoi sert un «Firewall»', 'À bloquer des connexions internet non souhaitées ou sollicitées et empêcher ainsi des pirates de prendre le contrôle partiel ou total de l\'ordinateur', 10),
(16, 3, 'Qu\'est-ce que la touche \"magique\" et à quoi sert-elle ?', 'C\'est la touche généralement symbolisée par l\'icône Windows. Elle permet d\'accéder au menu démarrer et d\'accéder facilement à tous les logiciels et fonctions de l\'ordinateur.', 10),
(17, 1, 'C\'est quoi un internaute ?', 'Une personne qui «navigue» sur le web.', NULL),
(18, 1, 'Quelle est la différence entre le Web et l\'Internet ?', 'L\'internet regroupe le Web et tous les autres protocoles de transmission de données comme les courriels, les messageries instantanées, les', NULL),
(19, 1, 'À quoi sert un logiciel de messagerie ?', 'À consulter, rédiger et envoyer des courriels', NULL),
(20, 1, 'Quel pays est désigné par les extensions de nom de domaine se terminant par .ru  ?', 'La Russie', NULL),
(21, 1, 'Que signifie le .gouv d\'une adresse internet ?', 'un site gouvernemental', NULL),
(22, 1, 'Que désigne-t\'on généralement par le terme «réseaux sociaux» ?', 'Un service internet qui rassemble des milliers de personnes pour s\'échanger des messages, des photos, des vidéos…', NULL),
(23, 5, 'Quel est le nom du premier réseau de communication informatique crée en 1969 et ancêtre du WWW ?', 'ARPANET', NULL),
(24, 1, 'En quelle année, le «Web» est-il devenu un réseau mondial ? 1976 - 1980 - 1990 - 2004 ?', '1990', NULL),
(25, 3, 'Qu\'est-ce qu\'une URL ?', 'C\'est un système d\'adressage unique qui permet à un internaute d\'accéder à un site Web', NULL),
(26, 1, 'Quelle est la différence entre un «navigateur» et un «moteur de recherche» ?', 'Le premier est un logiciel lancé dans un ordinateur pour accéder au Web, le second permet d\'y effectuer des recherches', NULL),
(27, 3, 'Qui est Bill Gates ?', 'C\'est un entrepreneur américain qui a créé en 1975 avec son ami Paul Allen la société Microsoft pour commercialiser le système d\'exploitation Windows.', NULL),
(28, 4, 'Qui est Steve Jobs ?', 'C\'est un entrepreneur américain qui a cofondé la société Apple. Il a été le premier à saisir les potentialités de l\'interface graphique/souris ce qui lui a permis de commercialiser le premier Macintosh, puis le smartphone, et la tablette.', NULL),
(29, 1, 'Citez au moins deux systèmes d\'exploitation.', 'Windows iOS Linux Android', NULL),
(30, 2, 'Qu\'est-ce qu\'un logiciel de traitement de texte ?', 'C\'est une application qui permet de rédiger et de mettre en forme des documents écrits.', NULL),
(31, 2, 'Qui signifie FAI ?', 'Fournisseur d\'Accès à Internet', NULL),
(32, 3, 'En informatique, qu\'est-ce qu\'un «Client»', 'C\'est un ordinateur qui se connecte à un autre ordinateur pour bénéficier de différents services : téléchargement de données, transmission de messages, affichage de médias…', NULL),
(33, 2, 'C\'est quoi Gmail ?', 'C\'est un service de messagerie électronique appartenant à la société américaine Google', NULL),
(34, 2, 'Que siqnifie l\'acronyme GPS ?', 'Global Positionning System', NULL),
(35, 2, 'Quelles données sont contenues dans un fichier avec l\'extension JPEG ?', 'des données relatives à une image', NULL),
(36, 2, 'Qu\'appellent t\'on généralement ves «identifants»', 'L\'association nom d\'utilisateur + mot de passe vous permettant de vous identifier su un site internet.', NULL),
(37, 1, 'Citez moi au moins trois noms de moteurs de recherche.', 'Google Startpage Yahoo Bing', NULL),
(38, 3, 'Qu\'est-ce qu\'un serveur SMTP ?', 'C\'est un serveur dont la fonction est l\'envoi des courriels.', NULL),
(39, 6, 'Citez moi 5 sites internet connus', '…', NULL),
(40, 3, 'Qu\'est-ce qu\'un antivirus ?', 'C\'est un logiciel qui inspecte en permanence les données entrant et sortant de votre ordinateur pour essayer d\'identifier un virus.', NULL),
(41, 2, 'Qui a crée le moteur de recherche Bing ?', 'Microsoft', NULL),
(42, 3, 'Comment sont representées les données informatiques quelle qu\'elles soient ?', 'Par une suite de chiffres entre 0 et 1', NULL),
(43, 1, 'Je peux me connecter indifféremment à n\'importe quel réseau wifi ou ethernet ? VRAI ou FAUX ?', 'FAUX. Certains réseaux publics peuvent être accessibles plus facilement aux «pirates»', NULL),
(44, 3, 'Pour sécuriser son ordinateur, combien de sessions faut-il configurer au minimum ?', '2. Une pour soi et une pour les autres personnes qui auraient besoin d\'accéder à mon ordinateur.', NULL),
(45, 1, 'NathalieRueDesPonts1965 est-il un mot de passe robuste ?', 'Non. Il est facilement devinable si on effectue quelques recherches sur cette personne', NULL),
(46, 1, 'NathalieRueDesPonts1965 est-il un mot de passe robuste ?', 'Non. Il est facilement devinable si on effectue quelques recherches sur cette personne', NULL),
(47, 3, 'Quel est le meilleur endroit pour garder ses mots de passe ?', 'Les enregistrer dans un gestionnaire de mots de passe et sauvegarder son fichier crypté à différents endroits : Cloud, Disque Dur…', NULL),
(48, 3, 'Qu\'est-ce qui n\'est pas un logiciel ? Photoshop Excel Facebook Chrome ?', 'Facebook', NULL),
(49, 3, 'Que vous évoque 176.187.24.243 ?', 'une adresse IP', NULL),
(50, 1, 'Comment s\'appelle le mode de fonctionnement d\'un smartphone ou d\'une tablette désactivant toutes les communications sans fil ?', 'le mode avion', NULL),
(51, 1, 'Quelles touches du clavier permettent d\'obtenir des lettres majuscules', 'Les touches SHIFT et CAPS LOCK', NULL),
(52, 1, 'VRAI ou FAUX ? La société IBM a été crée en 1911 ?', 'VRAI', NULL),
(53, 1, 'Que représente un «bit»', 'C\'est l\'information minimale que peut traiter un ordinateur sous la forme 0 ou 1 : VRAI ou FAUX', NULL),
(54, 3, 'Sur un écran d\'ordinateur qu\'appelle t\'on le «focus» ?', 'C\'est un champs de saisie qui a été activé par l\'utilisateur afin d\'entrer des données. On peut circuler entre les focus grâce à la touche TAB.', NULL),
(55, 3, 'À quoi sert la touche TAB ?', 'C\'est l\'ancienne touche de «tabulation» qui date des débuts de l\'informatique et qui avait pour fonction principale d\'aligner les mots à la façon d\'un tableau.', NULL),
(56, 3, 'De quelle manière puis-je effectuer l\'opération «copier» «coller» sur un ordinateur ?', 'Avec la souris ou la combinaison de touches CTR + X / CTR + V', NULL),
(57, 3, 'Parfois, les souris comportent un troisième bouton entre les deux boutons habituels. À quoi sert-il ?', 'À ouvrir des liens internet dans un autre onglet ou une autre page pour conserver la page actuellement consultée.', NULL),
(58, 2, 'Quel est le raccourci clavier qui permet de sélectionner tout en même temps ?', 'CTRL + A', NULL),
(59, 2, 'Quel est le raccourci clavier qui permet de lancer une recherche sur une page ?', 'CTRL + F', NULL),
(60, 2, 'Quel est le raccourci clavier qui permet d\'effectuer une sauvegarde rapide d\'un document sur lequel on travaille ?', 'CTRL + S', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `step`
--

CREATE TABLE `step` (
  `id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `suite` int(11) NOT NULL,
  `jeu_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `jeu`
--
ALTER TABLE `jeu`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `jeu_player`
--
ALTER TABLE `jeu_player`
  ADD PRIMARY KEY (`jeu_id`,`player_id`),
  ADD KEY `IDX_2B4DE5488C9E392E` (`jeu_id`),
  ADD KEY `IDX_2B4DE54899E6F5DF` (`player_id`);

--
-- Index pour la table `level`
--
ALTER TABLE `level`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `migration_versions`
--
ALTER TABLE `migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `player`
--
ALTER TABLE `player`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_B6F7494E5FB14BA7` (`level_id`);

--
-- Index pour la table `step`
--
ALTER TABLE `step`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_43B9FE3C1E27F6BF` (`question_id`),
  ADD KEY `IDX_43B9FE3C8C9E392E` (`jeu_id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `jeu`
--
ALTER TABLE `jeu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `level`
--
ALTER TABLE `level`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `player`
--
ALTER TABLE `player`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `question`
--
ALTER TABLE `question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;
--
-- AUTO_INCREMENT pour la table `step`
--
ALTER TABLE `step`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `jeu_player`
--
ALTER TABLE `jeu_player`
  ADD CONSTRAINT `FK_2B4DE5488C9E392E` FOREIGN KEY (`jeu_id`) REFERENCES `jeu` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_2B4DE54899E6F5DF` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `FK_B6F7494E5FB14BA7` FOREIGN KEY (`level_id`) REFERENCES `level` (`id`);

--
-- Contraintes pour la table `step`
--
ALTER TABLE `step`
  ADD CONSTRAINT `FK_43B9FE3C1E27F6BF` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`),
  ADD CONSTRAINT `FK_43B9FE3C8C9E392E` FOREIGN KEY (`jeu_id`) REFERENCES `jeu` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
