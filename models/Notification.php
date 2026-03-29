<?php
class Notification {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data) {
        $sql = "INSERT INTO notification (utilisateur_id, type, titre, message) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['utilisateur_id'],
            $data['type'],
            $data['titre'],
            $data['message'],
        ]);
    }

    public function getByUser($userId, $limit = 20) {
        $sql = "SELECT * FROM notification WHERE utilisateur_id = ? ORDER BY date_creation DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit]);
        $notifications = $stmt->fetchAll();
        return $notifications;
    }

    public function countUnread($userId) {
        $sql = "SELECT COUNT(*) as total FROM notification WHERE utilisateur_id = ? AND lue = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $resultat = $stmt->fetch();
        return $resultat['total'];
    }

    public function markAsRead($id) {
        $sql = "UPDATE notification SET lue = 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function markAllAsRead($userId) {
        $sql = "UPDATE notification SET lue = 1 WHERE utilisateur_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }
}
