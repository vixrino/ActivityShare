<?php
class ContactMessage {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO contact_message (nom, email, sujet, message) VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['nom'],
            $data['email'],
            $data['sujet'],
            $data['message'],
        ]);
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM contact_message ORDER BY date_envoi DESC");
        return $stmt->fetchAll();
    }

    public function countUnread() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM contact_message WHERE lu = 0");
        return $stmt->fetch()['total'];
    }

    public function markAsRead($id) {
        $stmt = $this->db->prepare("UPDATE contact_message SET lu = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
