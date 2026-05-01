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

CREATE TABLE categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    icone VARCHAR(50) DEFAULT NULL
) ENGINE=InnoDB;

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
    prix DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    photo VARCHAR(255) DEFAULT NULL,
    statut ENUM('active', 'annulee', 'terminee') DEFAULT 'active',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (categorie_id) REFERENCES categorie(id)
) ENGINE=InnoDB;

CREATE TABLE paiement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    reference VARCHAR(50) NOT NULL UNIQUE,
    montant_total DECIMAL(10,2) NOT NULL,
    titulaire_carte VARCHAR(100) NOT NULL,
    derniers_chiffres VARCHAR(4) NOT NULL,
    methode VARCHAR(20) NOT NULL DEFAULT 'carte',
    statut ENUM('en_attente','confirme','annule','echoue') NOT NULL DEFAULT 'confirme',
    adresse_facturation VARCHAR(255) DEFAULT NULL,
    ville_facturation VARCHAR(100) DEFAULT NULL,
    code_postal VARCHAR(20) DEFAULT NULL,
    pays VARCHAR(100) DEFAULT NULL,
    date_paiement DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE paiement_ligne (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paiement_id INT NOT NULL,
    activite_id INT DEFAULT NULL,
    titre VARCHAR(255) NOT NULL,
    prix_unitaire DECIMAL(8,2) NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    FOREIGN KEY (paiement_id) REFERENCES paiement(id) ON DELETE CASCADE,
    FOREIGN KEY (activite_id) REFERENCES activite(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE inscription (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activite_id INT NOT NULL,
    participant_id INT NOT NULL,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('inscrit', 'annule') DEFAULT 'inscrit',
    paiement_id INT DEFAULT NULL,
    FOREIGN KEY (activite_id) REFERENCES activite(id) ON DELETE CASCADE,
    FOREIGN KEY (participant_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (paiement_id) REFERENCES paiement(id) ON DELETE SET NULL,
    UNIQUE KEY unique_inscription (activite_id, participant_id)
) ENGINE=InnoDB;

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

CREATE TABLE notification (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    type ENUM('confirmation_inscription', 'rappel', 'annulation', 'place_disponible', 'paiement', 'message', 'forum', 'abonnement', 'notation') NOT NULL,
    titre VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    lue TINYINT(1) DEFAULT 0,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE faq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    reponse TEXT NOT NULL,
    ordre INT DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE contact_message (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    sujet VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    lu TINYINT(1) DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE password_reset (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    token VARCHAR(128) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_expiration DATETIME NOT NULL,
    utilise TINYINT(1) DEFAULT 0,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE contenu_editorial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cle VARCHAR(50) NOT NULL UNIQUE,
    titre VARCHAR(255) NOT NULL,
    contenu LONGTEXT NOT NULL,
    date_maj DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE panier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    activite_id INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_panier (utilisateur_id, activite_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (activite_id) REFERENCES activite(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE message_prive (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expediteur_id INT NOT NULL,
    destinataire_id INT NOT NULL,
    contenu TEXT NOT NULL,
    lu TINYINT(1) DEFAULT 0,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expediteur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (destinataire_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE activite_chat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activite_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    contenu TEXT NOT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (activite_id) REFERENCES activite(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE forum_categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    icone VARCHAR(50) DEFAULT 'fa-comments',
    ordre INT DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE forum_topic (
    id INT AUTO_INCREMENT PRIMARY KEY,
    forum_categorie_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    epingle TINYINT(1) DEFAULT 0,
    ferme TINYINT(1) DEFAULT 0,
    nb_vues INT DEFAULT 0,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (forum_categorie_id) REFERENCES forum_categorie(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE forum_message (
    id INT AUTO_INCREMENT PRIMARY KEY,
    forum_topic_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    contenu TEXT NOT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (forum_topic_id) REFERENCES forum_topic(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE abonnement (
    suiveur_id INT NOT NULL,
    suivi_id INT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (suiveur_id, suivi_id),
    UNIQUE KEY uniq_abonnement (suiveur_id, suivi_id),
    FOREIGN KEY (suiveur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (suivi_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE notation_activite (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activite_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    note TINYINT UNSIGNED NOT NULL,
    commentaire TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_note_activite (activite_id, utilisateur_id),
    FOREIGN KEY (activite_id) REFERENCES activite(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE notation_organisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organisateur_id INT NOT NULL,
    evaluateur_id INT NOT NULL,
    activite_id INT NOT NULL,
    note TINYINT UNSIGNED NOT NULL,
    commentaire TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_note_organisateur (organisateur_id, evaluateur_id, activite_id),
    FOREIGN KEY (organisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (evaluateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (activite_id) REFERENCES activite(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE tag (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(60) NOT NULL UNIQUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE activite_tag (
    activite_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (activite_id, tag_id),
    FOREIGN KEY (activite_id) REFERENCES activite(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tag(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE activite_vue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activite_id INT NOT NULL,
    utilisateur_id INT DEFAULT NULL,
    ip VARCHAR(45) DEFAULT NULL,
    date_vue DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (activite_id) REFERENCES activite(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE SET NULL,
    INDEX idx_activite_date (activite_id, date_vue)
) ENGINE=InnoDB;

CREATE TABLE login_attempt (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) DEFAULT NULL,
    ip VARCHAR(45) NOT NULL,
    succes TINYINT(1) DEFAULT 0,
    date_tentative DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip_date (ip, date_tentative),
    INDEX idx_email_date (email, date_tentative)
) ENGINE=InnoDB;

CREATE TABLE security_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT DEFAULT NULL,
    ip VARCHAR(45) DEFAULT NULL,
    action VARCHAR(80) NOT NULL,
    details TEXT DEFAULT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE SET NULL,
    INDEX idx_action_date (action, date_creation)
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

INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) VALUES
('Admin', 'ActivityShare', 'admin@activityshare.com', '$2y$10$8K1p/a0dL1LXMkgmV7YVhOzPfEX7GyBdKNOlPe5UWmKHX1XxK1H5y', 'administrateur');

INSERT INTO faq (question, reponse, ordre) VALUES
('Comment créer un compte ?', 'Cliquez sur le bouton "Inscription" en haut à droite de la page. Remplissez le formulaire avec vos informations personnelles et choisissez votre rôle (participant ou organisateur).', 1),
('Comment m\'inscrire à une activité ?', 'Rendez-vous sur la page de l\'activité qui vous intéresse et cliquez sur "S\'inscrire". Si l\'activité est payante, vous serez redirigé vers le paiement.', 2),
('Comment créer une activité ?', 'Vous devez avoir un compte Organisateur. Connectez-vous, puis cliquez sur "Créer une activité" dans votre tableau de bord.', 3),
('Puis-je annuler mon inscription ?', 'Oui, vous pouvez vous désinscrire à tout moment depuis la page de l\'activité.', 4),
('Comment modifier mon profil ?', 'Connectez-vous, puis accédez à "Mon Profil" via le menu.', 5),
('Que faire si j\'ai oublié mon mot de passe ?', 'Cliquez sur "Mot de passe oublié" sur la page de connexion.', 6);

INSERT INTO contenu_editorial (cle, titre, contenu) VALUES
('cgu', 'Conditions Générales d\'Utilisation',
'<h2>1. Objet</h2>
<p>Les présentes Conditions Générales d\'Utilisation (CGU) régissent l\'accès et l\'utilisation de la plateforme ActivityShare.</p>
<h2>2. Description du service</h2>
<p>ActivityShare est une plateforme permettant aux utilisateurs de proposer et de participer à des activités locales.</p>
<h2>3. Paiement</h2>
<p>Certaines activités peuvent être payantes. Le paiement s\'effectue via la plateforme.</p>'),
('mentions-legales', 'Mentions Légales',
'<h2>Éditeur du site</h2>
<p><strong>ActivityShare</strong> — Projet universitaire ISEP — Groupe G8A (Équipe Webkit).</p>
<h2>Hébergement</h2>
<p>Ce site est hébergé dans le cadre d\'un projet académique.</p>');

INSERT INTO forum_categorie (nom, description, icone, ordre) VALUES
('Annonces', 'Annonces officielles de l\'équipe ActivityShare', 'fa-bullhorn', 1),
('Discussions générales', 'Tout ce qui concerne ActivityShare et ses activités', 'fa-comments', 2),
('Bons plans', 'Partagez vos bons plans et idées d\'activités', 'fa-lightbulb', 3),
('Entraide', 'Posez vos questions à la communauté', 'fa-life-ring', 4);

INSERT INTO tag (nom, slug) VALUES
('Sport', 'sport'),
('Culture', 'culture'),
('Détente', 'detente'),
('Famille', 'famille'),
('Apéro', 'apero'),
('Plein air', 'plein-air'),
('Soirée', 'soiree'),
('Gratuit', 'gratuit'),
('Débutant', 'debutant'),
('Expert', 'expert');
