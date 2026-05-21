<?php
class Follow {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function follow($suiveurId, $suiviId) {
        if ($suiveurId == $suiviId) {
            return false;
        }
        $sql = "INSERT IGNORE INTO abonnement (suiveur_id, suivi_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$suiveurId, $suiviId]);
        return $stmt->rowCount() > 0;
    }

    public function unfollow($suiveurId, $suiviId) {
        $sql = "DELETE FROM abonnement WHERE suiveur_id = ? AND suivi_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$suiveurId, $suiviId]);
    }

    public function isFollowing($suiveurId, $suiviId) {
        $sql = "SELECT 1 FROM abonnement WHERE suiveur_id = ? AND suivi_id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$suiveurId, $suiviId]);
        return (bool)$stmt->fetchColumn();
    }

    public function countFollowers($userId) {
        $sql = "SELECT COUNT(*) FROM abonnement WHERE suivi_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return intval($stmt->fetchColumn());
    }

    public function countFollowing($userId) {
        $sql = "SELECT COUNT(*) FROM abonnement WHERE suiveur_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return intval($stmt->fetchColumn());
    }

    // Liste les utilisateurs qui suivent $userId
    public function getFollowers($userId) {
        $sql = "SELECT u.id, u.nom, u.prenom, u.photo_profil, u.role, u.ville, a.date_creation
                FROM abonnement a
                JOIN utilisateur u ON a.suiveur_id = u.id
                WHERE a.suivi_id = ? AND u.actif = 1
                ORDER BY a.date_creation DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // Liste les utilisateurs suivis par $userId
    public function getFollowing($userId) {
        $sql = "SELECT u.id, u.nom, u.prenom, u.photo_profil, u.role, u.ville, a.date_creation
                FROM abonnement a
                JOIN utilisateur u ON a.suivi_id = u.id
                WHERE a.suiveur_id = ? AND u.actif = 1
                ORDER BY a.date_creation DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
