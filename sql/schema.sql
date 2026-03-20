-- ============================================
-- ActivityShare - Database Schema
-- ============================================

CREATE DATABASE IF NOT EXISTS activityshare CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE activityshare;

-- ============================================
-- Table : utilisateur
-- ============================================
CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('participant', 'organisateur', 'administrateur') DEFAULT 'participant',
    photo_profil VARCHAR(255) DEFAULT NULL,
    telephone VARCHAR(20) DEFAULT NULL,
    ville VARCHAR(100) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    actif TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

-- ============================================
-- Table : categorie
-- ============================================
CREATE TABLE categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    icone VARCHAR(50) DEFAULT NULL
) ENGINE=InnoDB;

-- ============================================
-- Table : activite
-- ============================================
CREATE TABLE activite (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organisateur_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    categorie_id INT NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    lieu VARCHAR(255) NOT NULL,
    adresse VARCHAR(500) DEFAULT NULL,
    nb_max_participants INT NOT NULL,
    type ENUM('public', 'prive') DEFAULT 'public',
    conditions_participation TEXT DEFAULT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    statut ENUM('active', 'annulee', 'terminee') DEFAULT 'active',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (categorie_id) REFERENCES categorie(id)
) ENGINE=InnoDB;

-- ============================================
-- Table : inscription
-- ============================================
CREATE TABLE inscription (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activite_id INT NOT NULL,
    participant_id INT NOT NULL,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('inscrit', 'annule') DEFAULT 'inscrit',
    FOREIGN KEY (activite_id) REFERENCES activite(id) ON DELETE CASCADE,
    FOREIGN KEY (participant_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    UNIQUE KEY unique_inscription (activite_id, participant_id)
) ENGINE=InnoDB;

-- ============================================
-- Table : liste_attente
-- ============================================
CREATE TABLE liste_attente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activite_id INT NOT NULL,
    participant_id INT NOT NULL,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    position INT NOT NULL,
    FOREIGN KEY (activite_id) REFERENCES activite(id) ON DELETE CASCADE,
    FOREIGN KEY (participant_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attente (activite_id, participant_id)
) ENGINE=InnoDB;

-- ============================================
-- Table : notification
-- ============================================
CREATE TABLE notification (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    type ENUM('confirmation_inscription', 'rappel', 'annulation', 'place_disponible') NOT NULL,
    titre VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    lue TINYINT(1) DEFAULT 0,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Table : faq
-- ============================================
CREATE TABLE faq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    reponse TEXT NOT NULL,
    ordre INT DEFAULT 0
) ENGINE=InnoDB;

-- ============================================
-- Table : contact_message
-- ============================================
CREATE TABLE contact_message (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    sujet VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    lu TINYINT(1) DEFAULT 0
) ENGINE=InnoDB;

-- ============================================
-- Données initiales
-- ============================================

INSERT INTO categorie (nom, icone) VALUES
('Sport', 'fa-futbol'),
('Cuisine', 'fa-utensils'),
('Randonnée', 'fa-person-hiking'),
('Lecture', 'fa-book'),
('Musique', 'fa-music'),
('Art', 'fa-palette'),
('Jeux', 'fa-gamepad'),
('Bien-être', 'fa-spa'),
('Technologie', 'fa-laptop'),
('Autre', 'fa-star');

-- Mot de passe : Admin123!
INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) VALUES
('Admin', 'ActivityShare', 'admin@activityshare.com', '$2y$10$8K1p/a0dL1LXMkgmV7YVhOzPfEX7GyBdKNOlPe5UWmKHX1XxK1H5y', 'administrateur');

INSERT INTO faq (question, reponse, ordre) VALUES
('Comment créer un compte ?', 'Cliquez sur le bouton "Inscription" en haut à droite de la page. Remplissez le formulaire avec vos informations personnelles et choisissez votre rôle (participant ou organisateur).', 1),
('Comment m\'inscrire à une activité ?', 'Rendez-vous sur la page de l\'activité qui vous intéresse et cliquez sur le bouton "S\'inscrire". Si l\'activité est complète, vous serez automatiquement placé sur la liste d\'attente.', 2),
('Comment créer une activité ?', 'Vous devez avoir un compte Organisateur. Connectez-vous, puis cliquez sur "Créer une activité" dans votre tableau de bord. Remplissez tous les champs requis.', 3),
('Puis-je annuler mon inscription ?', 'Oui, vous pouvez vous désinscrire à tout moment depuis la page de l\'activité ou depuis votre profil. La première personne en liste d\'attente sera automatiquement notifiée.', 4),
('Comment modifier mon profil ?', 'Connectez-vous à votre compte, puis accédez à "Mon Profil" via le menu. Vous pourrez modifier vos informations personnelles et votre photo.', 5),
('Que faire si j\'ai oublié mon mot de passe ?', 'Cliquez sur "Mot de passe oublié" sur la page de connexion. Un e-mail de réinitialisation vous sera envoyé.', 6);
