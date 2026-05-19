<?php
class ActivityChat {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function send($activityId, $userId, $contenu) {
        $sql = "INSERT INTO activite_chat (activite_id, utilisateur_id, contenu)
                VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activityId, $userId, $contenu]);
        return $this->db->lastInsertId();
    }

    public function getByActivity($activityId, $limit = 200) {
        $sql = "SELECT ac.*, u.prenom, u.nom, u.photo_profil, u.role
                FROM activite_chat ac
                JOIN utilisateur u ON ac.utilisateur_id = u.id
                WHERE ac.activite_id = ?
                ORDER BY ac.date_envoi ASC
                LIMIT " . intval($limit);
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activityId]);
        return $stmt->fetchAll();
    }

    public function countByActivity($activityId) {
        $sql = "SELECT COUNT(*) AS total FROM activite_chat WHERE activite_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activityId]);
        $row = $stmt->fetch();
        return intval($row['total']);
    }
}
