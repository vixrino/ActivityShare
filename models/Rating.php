<?php
class Rating {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // ============================================
    // Éligibilité : on peut noter si on a participé
    // (statut 'inscrit') et que l'activité est terminée.
    // ============================================
    public function canRate($userId, $activiteId) {
        $sql = "SELECT a.organisateur_id, a.statut, a.date_fin
                FROM inscription i
                JOIN activite a ON i.activite_id = a.id
                WHERE i.activite_id = ? AND i.participant_id = ? AND i.statut = 'inscrit'
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activiteId, $userId]);
        $row = $stmt->fetch();
        if (!$row) {
            return false;
        }
        if (intval($row['organisateur_id']) === intval($userId)) {
            // l'organisateur ne se note pas lui-même
            return false;
        }
        $terminee = ($row['statut'] === 'terminee') || (strtotime($row['date_fin']) < time());
        return $terminee;
    }

    // ============================================
    // Notation d'une activité
    // ============================================
    public function rateActivity($userId, $activiteId, $note, $commentaire = '') {
        $note = max(1, min(5, intval($note)));
        $sql = "INSERT INTO notation_activite (activite_id, utilisateur_id, note, commentaire)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE note = VALUES(note), commentaire = VALUES(commentaire), date_creation = NOW()";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$activiteId, $userId, $note, $commentaire]);
    }

    public function getActivityRating($activiteId) {
        $sql = "SELECT COUNT(*) AS total, ROUND(AVG(note), 1) AS moyenne
                FROM notation_activite WHERE activite_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activiteId]);
        $row = $stmt->fetch();
        return [
            'total' => intval($row['total']),
            'moyenne' => $row['moyenne'] !== null ? floatval($row['moyenne']) : 0.0,
        ];
    }

    public function getActivityReviews($activiteId, $limit = 20) {
        $sql = "SELECT n.*, u.id AS utilisateur_id, u.prenom, u.nom, u.photo_profil
                FROM notation_activite n
                JOIN utilisateur u ON n.utilisateur_id = u.id
                WHERE n.activite_id = ?
                ORDER BY n.date_creation DESC
                LIMIT " . intval($limit);
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activiteId]);
        return $stmt->fetchAll();
    }

    public function findActivityRating($userId, $activiteId) {
        $sql = "SELECT * FROM notation_activite WHERE activite_id = ? AND utilisateur_id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activiteId, $userId]);
        return $stmt->fetch();
    }

    // ============================================
    // Notation d'un organisateur (rattachée à une activité)
    // ============================================
    public function rateOrganizer($evaluateurId, $organisateurId, $activiteId, $note, $commentaire = '') {
        if (intval($evaluateurId) === intval($organisateurId)) {
            return false;
        }
        $note = max(1, min(5, intval($note)));
        $sql = "INSERT INTO notation_organisateur (organisateur_id, evaluateur_id, activite_id, note, commentaire)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE note = VALUES(note), commentaire = VALUES(commentaire), date_creation = NOW()";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$organisateurId, $evaluateurId, $activiteId, $note, $commentaire]);
    }

    public function getOrganizerRating($organisateurId) {
        $sql = "SELECT COUNT(*) AS total, ROUND(AVG(note), 1) AS moyenne
                FROM notation_organisateur WHERE organisateur_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$organisateurId]);
        $row = $stmt->fetch();
        return [
            'total' => intval($row['total']),
            'moyenne' => $row['moyenne'] !== null ? floatval($row['moyenne']) : 0.0,
        ];
    }

    public function getOrganizerReviews($organisateurId, $limit = 10) {
        $sql = "SELECT n.*, u.id AS evaluateur_id, u.prenom, u.nom, u.photo_profil, a.titre AS activite_titre
                FROM notation_organisateur n
                JOIN utilisateur u ON n.evaluateur_id = u.id
                JOIN activite a ON n.activite_id = a.id
                WHERE n.organisateur_id = ?
                ORDER BY n.date_creation DESC
                LIMIT " . intval($limit);
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$organisateurId]);
        return $stmt->fetchAll();
    }

    public function findOrganizerRating($evaluateurId, $organisateurId, $activiteId) {
        $sql = "SELECT * FROM notation_organisateur
                WHERE evaluateur_id = ? AND organisateur_id = ? AND activite_id = ?
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$evaluateurId, $organisateurId, $activiteId]);
        return $stmt->fetch();
    }

    // ============================================
    // Pour le profil : activités terminées du user
    // avec l'état de notation (activité + organisateur).
    // ============================================
    public function pendingForUser($userId) {
        $sql = "SELECT a.id, a.titre, a.date_fin, a.photo, a.organisateur_id,
                       o.prenom AS orga_prenom, o.nom AS orga_nom, o.photo_profil AS orga_photo,
                       (SELECT note FROM notation_activite WHERE activite_id = a.id AND utilisateur_id = ? LIMIT 1) AS note_activite,
                       (SELECT note FROM notation_organisateur WHERE activite_id = a.id AND evaluateur_id = ? AND organisateur_id = a.organisateur_id LIMIT 1) AS note_organisateur
                FROM inscription i
                JOIN activite a ON i.activite_id = a.id
                JOIN utilisateur o ON a.organisateur_id = o.id
                WHERE i.participant_id = ? AND i.statut = 'inscrit'
                  AND (a.statut = 'terminee' OR a.date_fin < NOW())
                ORDER BY a.date_fin DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $userId, $userId]);
        return $stmt->fetchAll();
    }
}
