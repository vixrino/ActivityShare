-- ============================================
-- ActivityShare - Migration Démo 3
-- Profils publics + système d'abonnement entre utilisateurs
-- ============================================

USE activityshare;

-- ============================================
-- Table abonnement : relation "qui suit qui"
-- ============================================
CREATE TABLE IF NOT EXISTS abonnement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    suiveur_id INT NOT NULL,
    suivi_id INT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_abonnement (suiveur_id, suivi_id),
    FOREIGN KEY (suiveur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (suivi_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Étend les types de notifications : 'abonnement'
-- ============================================
ALTER TABLE notification MODIFY type ENUM(
    'confirmation_inscription','rappel','annulation','place_disponible',
    'paiement','message','forum','abonnement'
) NOT NULL;
