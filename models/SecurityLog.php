<?php
class SecurityLog {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function log($userId, $ip, $action, $details = '') {
        $sql = "INSERT INTO security_log (utilisateur_id, ip, action, details)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $ip, $action, $details]);
    }

    public function recent($limit = 100, $action = null) {
        $sql = "SELECT s.*, u.prenom, u.nom, u.email
                FROM security_log s
                LEFT JOIN utilisateur u ON s.utilisateur_id = u.id";
        $params = [];
        if ($action) {
            $sql .= " WHERE s.action = ?";
            $params[] = $action;
        }
        $sql .= " ORDER BY s.date_creation DESC LIMIT " . intval($limit);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countByAction($action, $hours = 24) {
        $sql = "SELECT COUNT(*) FROM security_log
                WHERE action = ?
                  AND date_creation > DATE_SUB(NOW(), INTERVAL ? HOUR)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$action, $hours]);
        return intval($stmt->fetchColumn());
    }
}
