-- ============================================
-- ActivityShare - Migration Démo 4
-- Système de notation : activité + organisateur
-- ============================================

USE activityshare;

-- ============================================
-- Note d'une activité par un participant
-- ============================================
CREATE TABLE IF NOT EXISTS notation_activite (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activite_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    note TINYINT NOT NULL,
    commentaire TEXT DEFAULT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_note_activite (activite_id, utilisateur_id),
    FOREIGN KEY (activite_id) REFERENCES activite(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    CHECK (note BETWEEN 1 AND 5)
) ENGINE=InnoDB;

-- ============================================
-- Note d'un organisateur par un participant (avec contexte activité)
-- ============================================
CREATE TABLE IF NOT EXISTS notation_organisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organisateur_id INT NOT NULL,
    evaluateur_id INT NOT NULL,
    activite_id INT NOT NULL,
    note TINYINT NOT NULL,
    commentaire TEXT DEFAULT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_note_organisateur (evaluateur_id, organisateur_id, activite_id),
    FOREIGN KEY (organisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (evaluateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (activite_id) REFERENCES activite(id) ON DELETE CASCADE,
    CHECK (note BETWEEN 1 AND 5)
) ENGINE=InnoDB;

-- ============================================
-- Étend les types de notifications : 'notation'
-- ============================================
ALTER TABLE notification MODIFY type ENUM(
    'confirmation_inscription','rappel','annulation','place_disponible',
    'paiement','message','forum','abonnement','notation'
) NOT NULL;
