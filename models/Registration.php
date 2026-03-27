<?php
class Registration {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($activiteId, $participantId) {
        $sql = "INSERT INTO inscription (activite_id, participant_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$activiteId, $participantId]);
    }

    public function cancel($activiteId, $participantId) {
        $sql = "DELETE FROM inscription WHERE activite_id = ? AND participant_id = ? AND statut = 'inscrit'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$activiteId, $participantId]);
    }

    public function isRegistered($activiteId, $participantId) {
        $sql = "SELECT COUNT(*) as total FROM inscription
                WHERE activite_id = ? AND participant_id = ? AND statut = 'inscrit'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activiteId, $participantId]);
        $resultat = $stmt->fetch();

        if ($resultat['total'] > 0) {
            return true;
        }
        return false;
    }

    public function countByActivity($activiteId) {
        $sql = "SELECT COUNT(*) as total FROM inscription
                WHERE activite_id = ? AND statut = 'inscrit'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activiteId]);
        $resultat = $stmt->fetch();
        return $resultat['total'];
    }

    public function getByActivity($activiteId) {
        $sql = "SELECT i.*, u.nom, u.prenom, u.email
                FROM inscription i
                JOIN utilisateur u ON i.participant_id = u.id
                WHERE i.activite_id = ? AND i.statut = 'inscrit'
                ORDER BY i.date_inscription ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activiteId]);
        $inscrits = $stmt->fetchAll();
        return $inscrits;
    }

    public function getByUser($userId) {
        $sql = "SELECT i.*, a.titre, a.date_debut, a.date_fin, a.lieu, a.photo, a.statut as activite_statut,
                       c.nom as categorie_nom, c.icone as categorie_icone
                FROM inscription i
                JOIN activite a ON i.activite_id = a.id
                JOIN categorie c ON a.categorie_id = c.id
                WHERE i.participant_id = ? AND i.statut = 'inscrit'
                ORDER BY a.date_debut ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $inscriptions = $stmt->fetchAll();
        return $inscriptions;
    }

    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM inscription WHERE statut = 'inscrit'";
        $stmt = $this->db->query($sql);
        $resultat = $stmt->fetch();
        return $resultat['total'];
    }
}
