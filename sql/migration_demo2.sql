-- ============================================
-- ActivityShare - Migration Démo Client #2
-- À exécuter sur la base existante.
-- Ajoute : prix activité, paiement, panier, reset mot de passe,
-- contenu éditorial, messagerie, chat activité, forum.
-- ============================================

USE activityshare;

-- ============================================
-- Activités : prix
-- ============================================
ALTER TABLE activite ADD COLUMN prix DECIMAL(8,2) NOT NULL DEFAULT 0.00 AFTER conditions_participation;

-- ============================================
-- Étendre les types de notifications (paiement, message, forum)
-- ============================================
ALTER TABLE notification MODIFY type ENUM(
    'confirmation_inscription','rappel','annulation','place_disponible',
    'paiement','message','forum'
) NOT NULL;

-- ============================================
-- Réinitialisation du mot de passe
-- ============================================
CREATE TABLE IF NOT EXISTS password_reset (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    token VARCHAR(128) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_expiration DATETIME NOT NULL,
    utilise TINYINT(1) DEFAULT 0,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Contenu éditorial (CGU + Mentions légales modifiables par l'admin)
-- ============================================
CREATE TABLE IF NOT EXISTS contenu_editorial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cle VARCHAR(50) NOT NULL UNIQUE,
    titre VARCHAR(255) NOT NULL,
    contenu LONGTEXT NOT NULL,
    date_maj DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO contenu_editorial (cle, titre, contenu) VALUES
('cgu', 'Conditions Générales d''Utilisation',
'<h2>1. Objet</h2>
<p>Les présentes Conditions Générales d''Utilisation (CGU) régissent l''accès et l''utilisation de la plateforme ActivityShare. En utilisant ce service, vous acceptez sans réserve les présentes conditions.</p>
<h2>2. Description du service</h2>
<p>ActivityShare est une plateforme permettant aux utilisateurs de proposer et de participer à des activités locales (sport, cuisine, randonnée, lecture, etc.).</p>
<h2>3. Inscription</h2>
<p>L''inscription est gratuite et nécessite la fourniture d''informations personnelles exactes. Chaque utilisateur est responsable de la confidentialité de ses identifiants.</p>
<h2>4. Paiement des activités</h2>
<p>Certaines activités peuvent être payantes. Le paiement s''effectue via la plateforme. Les remboursements suivent la politique précisée par chaque organisateur.</p>
<h2>5. Responsabilités</h2>
<p>Chaque utilisateur s''engage à respecter les autres membres et les conditions définies par les organisateurs.</p>
<h2>6. Protection des données</h2>
<p>Conformément au RGPD, vos données personnelles sont traitées de manière confidentielle.</p>'),
('mentions-legales', 'Mentions Légales',
'<h2>Éditeur du site</h2>
<p><strong>ActivityShare</strong> — Projet universitaire ISEP — Groupe G8A (Équipe Webkit).</p>
<p>APP Informatique S2 2025/2026.</p>
<h2>Équipe de développement</h2>
<ul>
<li>FALLET Justin</li>
<li>AMRANI Karim</li>
<li>ANANTARAJAH Vineu</li>
<li>GROLLEAU Louis</li>
<li>TEYSSIER Lucas</li>
</ul>
<h2>Hébergement</h2>
<p>Ce site est hébergé dans le cadre d''un projet académique.</p>
<h2>Propriété intellectuelle</h2>
<p>L''ensemble du contenu de ce site (textes, images, logo, maquettes) est la propriété du groupe G8A.</p>
<h2>Contact</h2>
<p>contact@activityshare.com</p>');

-- ============================================
-- Paiement et panier
-- ============================================
CREATE TABLE IF NOT EXISTS panier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    activite_id INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_panier (utilisateur_id, activite_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (activite_id) REFERENCES activite(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS paiement (
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

CREATE TABLE IF NOT EXISTS paiement_ligne (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paiement_id INT NOT NULL,
    activite_id INT DEFAULT NULL,
    titre VARCHAR(255) NOT NULL,
    prix_unitaire DECIMAL(8,2) NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    FOREIGN KEY (paiement_id) REFERENCES paiement(id) ON DELETE CASCADE,
    FOREIGN KEY (activite_id) REFERENCES activite(id) ON DELETE SET NULL
) ENGINE=InnoDB;

ALTER TABLE inscription ADD COLUMN paiement_id INT DEFAULT NULL AFTER statut;
ALTER TABLE inscription ADD CONSTRAINT fk_inscription_paiement
    FOREIGN KEY (paiement_id) REFERENCES paiement(id) ON DELETE SET NULL;

-- ============================================
-- Messagerie privée
-- ============================================
CREATE TABLE IF NOT EXISTS message_prive (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expediteur_id INT NOT NULL,
    destinataire_id INT NOT NULL,
    contenu TEXT NOT NULL,
    lu TINYINT(1) DEFAULT 0,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expediteur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (destinataire_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Chat par activité
-- ============================================
CREATE TABLE IF NOT EXISTS activite_chat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activite_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    contenu TEXT NOT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (activite_id) REFERENCES activite(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Forum
-- ============================================
CREATE TABLE IF NOT EXISTS forum_categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    icone VARCHAR(50) DEFAULT 'fa-comments',
    ordre INT DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS forum_topic (
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

CREATE TABLE IF NOT EXISTS forum_message (
    id INT AUTO_INCREMENT PRIMARY KEY,
    forum_topic_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    contenu TEXT NOT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (forum_topic_id) REFERENCES forum_topic(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO forum_categorie (nom, description, icone, ordre) VALUES
('Annonces', 'Annonces officielles de l''équipe ActivityShare', 'fa-bullhorn', 1),
('Discussions générales', 'Tout ce qui concerne ActivityShare et ses activités', 'fa-comments', 2),
('Bons plans', 'Partagez vos bons plans et idées d''activités', 'fa-lightbulb', 3),
('Entraide', 'Posez vos questions à la communauté', 'fa-life-ring', 4);
