<?php
class ActivityView {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Enregistre une vue. Évite les doublons rapprochés (un utilisateur
     * ou une IP n'est comptabilisé qu'une fois par 30 minutes).
     */
    public function record($activiteId, $userId, $ip) {
        $sql = "SELECT COUNT(*) FROM activite_vue
                WHERE activite_id = ?
                  AND (
                    (utilisateur_id IS NOT NULL AND utilisateur_id = ?)
                    OR (utilisateur_id IS NULL AND ip = ?)
                  )
                  AND date_vue > DATE_SUB(NOW(), INTERVAL 30 MINUTE)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activiteId, $userId, $ip]);
        if (intval($stmt->fetchColumn()) > 0) {
            return false;
        }

        $sql = "INSERT INTO activite_vue (activite_id, utilisateur_id, ip)
                VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activiteId, $userId ?: null, $ip]);
        return true;
    }

    public function countForActivity($activiteId) {
        $sql = "SELECT COUNT(*) FROM activite_vue WHERE activite_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activiteId]);
        return intval($stmt->fetchColumn());
    }

    public function countLast30Days($activiteId) {
        $sql = "SELECT COUNT(*) FROM activite_vue
                WHERE activite_id = ?
                  AND date_vue > DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activiteId]);
        return intval($stmt->fetchColumn());
    }

    public function dailySeries($activiteId, $days = 14) {
        $sql = "SELECT DATE(date_vue) AS jour, COUNT(*) AS total
                FROM activite_vue
                WHERE activite_id = ?
                  AND date_vue > DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY DATE(date_vue)
                ORDER BY jour ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activiteId, $days]);
        return $stmt->fetchAll();
    }

    public function totalForOrganizer($organisateurId) {
        $sql = "SELECT COUNT(*) FROM activite_vue v
                JOIN activite a ON a.id = v.activite_id
                WHERE a.organisateur_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$organisateurId]);
        return intval($stmt->fetchColumn());
    }
}
