<?php
class Registration {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($activiteId, $participantId) {
        $stmt = $this->db->prepare("
            INSERT INTO inscription (activite_id, participant_id) VALUES (?, ?)
        ");
        return $stmt->execute([$activiteId, $participantId]);
    }

    public function cancel($activiteId, $participantId) {
        $stmt = $this->db->prepare("
            DELETE FROM inscription WHERE activite_id = ? AND participant_id = ? AND statut = 'inscrit'
        ");
        return $stmt->execute([$activiteId, $participantId]);
    }

    public function isRegistered($activiteId, $participantId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM inscription
            WHERE activite_id = ? AND participant_id = ? AND statut = 'inscrit'
        ");
        $stmt->execute([$activiteId, $participantId]);
        return $stmt->fetch()['total'] > 0;
    }

    public function countByActivity($activiteId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM inscription
            WHERE activite_id = ? AND statut = 'inscrit'
        ");
        $stmt->execute([$activiteId]);
        return $stmt->fetch()['total'];
    }

    public function getByActivity($activiteId) {
        $stmt = $this->db->prepare("
            SELECT i.*, u.nom, u.prenom, u.email
            FROM inscription i
            JOIN utilisateur u ON i.participant_id = u.id
            WHERE i.activite_id = ? AND i.statut = 'inscrit'
            ORDER BY i.date_inscription ASC
        ");
        $stmt->execute([$activiteId]);
        return $stmt->fetchAll();
    }

    public function getByUser($userId) {
        $stmt = $this->db->prepare("
            SELECT i.*, a.titre, a.date_debut, a.date_fin, a.lieu, a.photo, a.statut as activite_statut,
                   c.nom as categorie_nom, c.icone as categorie_icone
            FROM inscription i
            JOIN activite a ON i.activite_id = a.id
            JOIN categorie c ON a.categorie_id = c.id
            WHERE i.participant_id = ? AND i.statut = 'inscrit'
            ORDER BY a.date_debut ASC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM inscription WHERE statut = 'inscrit'");
        return $stmt->fetch()['total'];
    }
}
