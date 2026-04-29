<?php
class PasswordReset {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($userId, $email) {
        $token = bin2hex(random_bytes(32));

        // On calcule les dates côté PHP (timezone Europe/Paris dans index.php)
        // pour rester indépendant de la timezone MySQL (qui peut différer entre CLI et Apache).
        $now = date('Y-m-d H:i:s');
        $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $sql = "INSERT INTO password_reset (utilisateur_id, token, email, date_creation, date_expiration)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $token, $email, $now, $expiration]);

        return $token;
    }

    public function findValid($token) {
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM password_reset
                WHERE token = ? AND utilise = 0 AND date_expiration > ?
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$token, $now]);
        return $stmt->fetch();
    }

    public function markUsed($token) {
        $sql = "UPDATE password_reset SET utilise = 1 WHERE token = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$token]);
    }

    public function getRecent($limit = 20) {
        $sql = "SELECT pr.*, u.nom, u.prenom
                FROM password_reset pr
                JOIN utilisateur u ON pr.utilisateur_id = u.id
                ORDER BY pr.date_creation DESC
                LIMIT " . intval($limit);
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
