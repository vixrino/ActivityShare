<?php
class LoginAttempt {
    private $db;
    const WINDOW_SECONDS = 900;   // 15 minutes
    const MAX_ATTEMPTS_IP = 10;   // par IP
    const MAX_ATTEMPTS_EMAIL = 5; // par email

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function record($email, $ip, $success) {
        $sql = "INSERT INTO login_attempt (email, ip, succes) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email, $ip, $success ? 1 : 0]);
    }

    public function countRecentFailuresByIp($ip) {
        $sql = "SELECT COUNT(*) FROM login_attempt
                WHERE ip = ? AND succes = 0
                  AND date_tentative > DATE_SUB(NOW(), INTERVAL " . self::WINDOW_SECONDS . " SECOND)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ip]);
        return intval($stmt->fetchColumn());
    }

    public function countRecentFailuresByEmail($email) {
        if (!$email) return 0;
        $sql = "SELECT COUNT(*) FROM login_attempt
                WHERE email = ? AND succes = 0
                  AND date_tentative > DATE_SUB(NOW(), INTERVAL " . self::WINDOW_SECONDS . " SECOND)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return intval($stmt->fetchColumn());
    }

    public function isBlocked($email, $ip) {
        return $this->countRecentFailuresByIp($ip) >= self::MAX_ATTEMPTS_IP
            || ($email && $this->countRecentFailuresByEmail($email) >= self::MAX_ATTEMPTS_EMAIL);
    }

    public function clearFor($email, $ip) {
        $sql = "DELETE FROM login_attempt WHERE (email = ? OR ip = ?) AND succes = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email, $ip]);
    }

    public function purgeOld() {
        $sql = "DELETE FROM login_attempt
                WHERE date_tentative < DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        $this->db->exec($sql);
    }
}
