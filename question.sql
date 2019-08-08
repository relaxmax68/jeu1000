-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le :  jeu. 08 août 2019 à 14:46
-- Version du serveur :  10.3.17-MariaDB
-- Version de PHP :  7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `relaxmax_jeu`
--

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
-- Déchargement des données de la table `question`
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
(18, 3, 'Quelle est la différence entre le Web et l\'Internet ?', 'L\'internet regroupe le Web et tous les autres protocoles de transmission de données comme les courriels, les messageries instantanées, les', NULL),
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
(60, 2, 'Quel est le raccourci clavier qui permet d\'effectuer une sauvegarde rapide d\'un document sur lequel on travaille ?', 'CTRL + S', NULL),
(61, 2, 'À quoi sert l\'application VLC ?', 'À lire des médias audios et vidéos', NULL),
(62, 3, 'Qu\'est-ce qu\'un fichier avec une extension .exe ?', 'C\'est un fichier qui lance l\'installation d\'une application dans un système d\'exploitation Windows', NULL),
(63, 3, 'De bons mots de passe permettent d\'empêcher l\'usurpation de son identité. Mais contre quelles menaces réelles nous protègent-ils ? Citez en au moins deux.', '1 Le détournement d’argent 2 La protection de sa réputation numérique 3 La perte de données personnelles  4 La récupération de données sensibles', NULL),
(64, 4, 'Citez au moins une méthode pour obtenir un bon mot de passe.', 'La méthode phonétique, La méthode des premières lettres, la méthode mixte, la méthode longue', NULL),
(65, 3, 'Quel est l\'organisne français dont la mission est de protéger nos données numériques personnelles et d\'encadrer leurs utilisations ?', 'la CNIL', NULL),
(66, 3, 'Que représente le symbole C: dans un \"explorateur de fichier\" ?', 'Le disque dur principal sur lequel à été installé votre système d\'exploitation.', NULL),
(67, 2, 'Un Smartphone est-il un ordinateur comme un autre ?', 'OUI', NULL),
(68, 2, 'Citez 1 utilisation de la touche ENTRÉE (parfois symbolisée par une flèche avec un angle droit)', '1. le saut à la ligne 2. la validation d\'une donnée saisie au clavier 3. la validation d\'un formulaire ou d\'une boîte de dialogue…', NULL),
(69, 2, 'Qu\'elle est l\'origine historique de la touche Entrée du clavier ?', 'Cette touche déclenchait un «retour chariot» sur un clavier mécanique', NULL),
(70, 2, 'Que représente l\'acronyme GAFAM ?', 'Google Amazon Facebook Apple Microsoft', NULL),
(71, 2, 'Qu\'est ce que le «Cloud»', 'C\'est l\'ordinateur d\'un autre ;-)', NULL),
(72, 1, 'VRAI ou FAUX ? Les termes «racine» et «arborescence» font référence à l\'arboriculture mais jamais à l\'informatique ?', 'FAUX !', NULL),
(73, 1, 'Comment «rafraîchir» une page internet affichée à l\'écran de vore ordinateur ?', 'appuyer sur la touche F5 du clavier ou cliquer sur le symbole «flèche qui tourne dans le sens horaire»', NULL),
(74, 2, 'VRAI / FAUX ? Tout ce qui est visible sur internet n\'est qu\'une copie qui peut être modifiée voire trafiquée !', 'VRAI', NULL),
(75, 1, 'VRAI / FAUX Une combinaison de touches de clavier permet de protéger le clavier contre la salissure.', 'FAUX', NULL),
(76, 1, 'VRAI / FAUX on peut imprimer n\'importe quel document affiché à l\'écran de son ordinateur grâce à l\'appui d\'une seule touche ?', 'VRAI', NULL),
(77, 2, 'Qu\'est que le «pavé numérique» ?', 'C\'est un ensemble de touches regroupées à la droite du clavier sous forme de calculatrice et pour réaliser les mêmes opérations.', NULL),
(78, 1, 'Parfois un sablier ou un cercle tournant apparaît à la place du pointeur de la souris quand l\'ordinateur «réfléchit»… Si on la secoue légèrement la souris, l\'ordinateur ira plus vite', 'FAUX', NULL),
(79, 1, 'Tous les ordinateurs et les logiciels  se règlent grâce à des «paramètres» accessibles en cliquant sur le symbole représentant une petite roue crantée.', 'VRAI', NULL),
(80, 1, 'Un fichier informatique est toujours rangé dans un dossier ?', 'VRAI', NULL),
(81, 1, 'L\'informatique sert à tout. Il existe même des logiciels pour faire du jardinage ?', 'VRAI', NULL),
(82, 4, 'Dans un navigateur qu\'est-ce qu\'une barre d\'adresse ?', 'c\'est un champs qui soit affiche une adresse internet du site consulté, soit permet la directe d\'une adresse d\'un site internet.', NULL),
(83, 2, 'Dans un navigateur à quoi sert un «onglet» ?', 'À naviguer sur plusieurs sites en même temps.', NULL),
(84, 2, 'Quelle est la première chose à faire pour protéger ses données et qu\'on oublie tout le temps ?', 'la sauvegarde de ses données', NULL),
(85, 3, 'Comment se protéger véritablement  d\'une perte de ses données ?', 'La sauvegarde automatique de ses données car tôt ou tard on oublie de le faire manuellement.', NULL),
(86, 3, 'À quoi sert un gestionnaire de mots de passe ? Citez au moins 2 fonctions', 'Génération des MDP, Sauvegarde des MDP, Rappel des MDP, le remplissage automatique des formulaires d\'identification', NULL),
(87, 3, 'Quel réflexe doit-on avoir lorsqu\'on voit un message incompréhensible s\'afficher sur l\'écran de son ordinateur ?', 'Annuler l\'opération en cours, éventuellement après avoir opéré une copie écran que l\'on pourra envoyer à une personne compétente', NULL),
(88, 2, 'Une connexion internet à travers un câble est plus rapide et plus sûre qu\'une connexion «sans fil»', 'VRAI', NULL),
(89, 3, 'Je peux faire confiance à l\'expéditeur d\'un courriel si je le connais ?', 'FAUX', NULL),
(90, 3, 'C\'est quoi un PDF ?', 'C\'est un format de fichier qui permet de publier des documents texte non modifiables.', NULL),
(91, 3, 'Un «serveur» n\'est pas un ordinateur comme les autres.', 'FAUX c\'est juste un plus puissant et parfois plus spécialisé.', NULL),
(92, 3, 'Quelle est la différence entre un menu de fenêtre et un menu contextuel ?', 'L\'un est figé et est lié à une fenêtre, l\'autre évolue en fonction du «contexte» et est toujours accessible via le clic droit de la souris.', NULL),
(93, 3, 'Quel est l\'élément de base commun à tous les «tableurs» ?', 'La cellule', NULL),
(94, 3, 'Toutes les applications informatiques se présentent habituellement sous cette forme.', 'La fenêtre', NULL),
(95, 3, 'Combien d\'employés sont directement employés par la société Google ? 5000 ? 10000 ? 50000 ?', 'La société compte environ 50 000 employés. La plupart travaillent au siège mondial : le Googleplex, à Mountain View en Californie.', NULL),
(96, 1, 'Quel genre de données sont stockées dans un fichier avec l\'extension .mp4 ?', 'des vidéos principalement', NULL),
(97, 1, 'En quelle année est sorti le premier système d\'exploitation grand public Windows ?', 'Windows 95 en 1995', NULL),
(98, 2, 'En quelle année a été lancé le minitel ?', '1982', NULL),
(99, 1, 'Il n\'est pas possible de tester son débit internet avec son propre ordinateur', 'FAUX', NULL),
(100, 2, 'Je ne peux pas empêcher un enfant de faire ce qu\'il veut sur mon ordinateur sauf à l\'en interdire.', 'FAUX', NULL),
(101, 3, 'Comment protéger mes données informatiques des indiscrétions ? Citez au moins deux exemples', 'me déconnecter de mes sites, créer des comptes séparés, mettre des mots de passe, programmer un vérouillage automatique de mon écran, …', NULL),
(102, 3, 'Comment retrouver facilement ses fichiers dans son ordinateur ? Citez quelques habitudes à prendre', 'Renomer les fichiers. Classer les fichiers dans des dossiers séparés. Regrouper tous ses fichiers dans son répertoire personnel. Ne rien laisser dans le dossier Téléchargements…', NULL),
(103, 5, 'À quoi sert le cadenas vert ou fermé qui s\'affiche souvent à gauche de la barre d\'adresse ? Citez au moins deux garanties.', 'À certifier que le site internet consulté est bien le bon et qu\'on y est relié directement grâce à une communication chiffrée.', NULL),
(104, 5, 'À quoi servent les fonctions dans un tableur ?', 'À effectuer automatiquement une suite de calculs fastidieux ou complexe', NULL),
(105, 4, 'Qu\'est-ce qu\'un lien internet ?', 'C\'est un mot ou une phrase qui permet d\'accéder directement à un site internet grâce à un simple clic de souris', NULL),
(106, 4, 'Un robot programmé pour se connecter systématiquement à n\'importe quel ordinateur présent sur internet c\'est un virus.', 'FAUX', NULL),
(107, 3, 'Chaque ordinateur connecté sur internet a une adresse IP ?', 'VRAI', NULL),
(108, 3, 'Chaque adresse IP représente un ordinateur connecté à internet.', 'FAUX', NULL),
(109, 5, 'Citez plusieurs moyens de reconnaître un «Fake News»', 'Ne cite pas ses sources. Vérifier les sources. Confronter plusieurs sources.Consulter des sites anti Fake.Effectuer soi-même une recherche sur le fake news.', NULL),
(110, 3, 'Quand je supprime un mail stocké sur mon ordinateur, il est supprimé définitivement.', 'FAUX', NULL),
(111, 5, 'Quelle est la différence entre les protocoles POP et IMAP ?', 'IMAP synchronise les mails POP transfert les mails', NULL),
(112, 4, 'Quels sont les 2 possibilités de consulter ses mails ?', 'Webmail et logiciel de messagerie.', NULL),
(113, 4, 'D\'où proviennent les questions de ce jeu ? Réponse A : de l\'ordinateur relié à cet écran Réponse B : d\'un serveur sur le Web', 'B', NULL),
(114, 2, 'Citez au moins deux navigateurs internet.', 'Edge Chrome Firefox Opéra', NULL),
(115, 3, 'À quoi sert la barre d\'état ? Ou est-elle située ?', 'Au bas d\'une fenêtre. Elle donne un certain nombre d\'informations sur l\'application qui est en cours d\'exécution dans la fenêtre correspondante', NULL),
(116, 3, 'À quoi cela sert-il d\'ouvrir plusieurs fenêtres à la fois ?', 'À utiliser plusieurs applications à la fois et pouvoir interagir entre elles : transférer des fichiers, des données, perdre moins de temps de téléchargement…', NULL),
(117, 3, 'Comment doit-on réaliser la combimaison de touches ALT + TAB ? À quoi sert-elle ?', 'On reste appuyé sur TAB pendant qu\'on appuie brièvement sur la touche TAB pour passer rapidement d\'une ferêtre à l\'autre.', NULL),
(118, 4, 'Qu\'est-ce qu\'une «capture d\'écran»', 'c\'est une photo instantanée de l\'écran enregistrée dans un fichier image', NULL),
(119, 3, 'Dans une fenêtre où se situe l\'«ascenseur» ? À quoi sert-il ?', 'À gauche d\'une fenêtre. Il sert à visualiser la partie cachée d\'une application en faisant «défiler» l\'écran vers le haut.', NULL),
(120, 4, 'SUite à une mauvaise manipulation j\'ai perdu la fenêtre dans laquelle j\'étais en train de travailler. Comment puis-je la retrouver ?', 'combinaison ALT + TAB, grâce aux raccourcis d\'application, en réduisant ou fermant les autres fenêtres…', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_B6F7494E5FB14BA7` (`level_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `question`
--
ALTER TABLE `question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `FK_B6F7494E5FB14BA7` FOREIGN KEY (`level_id`) REFERENCES `level` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
