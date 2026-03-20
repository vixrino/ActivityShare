<?php
class Notification {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO notification (utilisateur_id, type, titre, message)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['utilisateur_id'],
            $data['type'],
            $data['titre'],
            $data['message'],
        ]);
    }

    public function getByUser($userId, $limit = 20) {
        $stmt = $this->db->prepare("
            SELECT * FROM notification
            WHERE utilisateur_id = ?
            ORDER BY date_creation DESC
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    public function countUnread($userId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM notification
            WHERE utilisateur_id = ? AND lue = 0
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch()['total'];
    }

    public function markAsRead($id) {
        $stmt = $this->db->prepare("UPDATE notification SET lue = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function markAllAsRead($userId) {
        $stmt = $this->db->prepare("UPDATE notification SET lue = 1 WHERE utilisateur_id = ?");
        return $stmt->execute([$userId]);
    }
}
