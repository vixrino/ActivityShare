<?php
class ContactMessage {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data) {
        $sql = "INSERT INTO contact_message (nom, email, sujet, message) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nom'],
            $data['email'],
            $data['sujet'],
            $data['message'],
        ]);
    }

    public function getAll() {
        $sql = "SELECT * FROM contact_message ORDER BY date_envoi DESC";
        $stmt = $this->db->query($sql);
        $messages = $stmt->fetchAll();
        return $messages;
    }

    public function countUnread() {
        $sql = "SELECT COUNT(*) as total FROM contact_message WHERE lu = 0";
        $stmt = $this->db->query($sql);
        $resultat = $stmt->fetch();
        return $resultat['total'];
    }

    public function markAsRead($id) {
        $sql = "UPDATE contact_message SET lu = 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
