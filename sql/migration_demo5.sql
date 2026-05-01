-- ============================================
-- ActivityShare - Migration Démo 5
-- Tags, statistiques, partage, sécurité (brute force, logs)
-- ============================================

USE activityshare;

-- ============================================
-- Tags : système de tags sur les activités
-- ============================================
CREATE TABLE IF NOT EXISTS tag (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(60) NOT NULL UNIQUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS activite_tag (
    activite_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (activite_id, tag_id),
    FOREIGN KEY (activite_id) REFERENCES activite(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tag(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Statistiques : compteur de vues sur les activités
-- ============================================
CREATE TABLE IF NOT EXISTS activite_vue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activite_id INT NOT NULL,
    utilisateur_id INT DEFAULT NULL,
    ip VARCHAR(45) DEFAULT NULL,
    date_vue DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (activite_id) REFERENCES activite(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE SET NULL,
    INDEX idx_activite_date (activite_id, date_vue)
) ENGINE=InnoDB;

-- ============================================
-- Sécurité : tentatives de connexion (brute force)
-- ============================================
CREATE TABLE IF NOT EXISTS login_attempt (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) DEFAULT NULL,
    ip VARCHAR(45) NOT NULL,
    succes TINYINT(1) DEFAULT 0,
    date_tentative DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip_date (ip, date_tentative),
    INDEX idx_email_date (email, date_tentative)
) ENGINE=InnoDB;

-- ============================================
-- Sécurité : logs d'événements sensibles
-- ============================================
CREATE TABLE IF NOT EXISTS security_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT DEFAULT NULL,
    ip VARCHAR(45) DEFAULT NULL,
    action VARCHAR(80) NOT NULL,
    details TEXT DEFAULT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE SET NULL,
    INDEX idx_action_date (action, date_creation)
) ENGINE=InnoDB;

-- Quelques tags par défaut
INSERT IGNORE INTO tag (nom, slug) VALUES
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
