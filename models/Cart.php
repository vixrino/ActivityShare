<?php
class Cart {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByUser($userId) {
        $sql = "SELECT p.*, a.titre, a.prix, a.date_debut, a.lieu, a.photo,
                       c.nom AS categorie_nom, c.icone AS categorie_icone,
                       a.nb_max_participants,
                       (SELECT COUNT(*) FROM inscription i WHERE i.activite_id = a.id AND i.statut = 'inscrit') AS nb_inscrits
                FROM panier p
                JOIN activite a ON p.activite_id = a.id
                JOIN categorie c ON a.categorie_id = c.id
                WHERE p.utilisateur_id = ?
                ORDER BY p.date_ajout DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function add($userId, $activityId, $quantite = 1) {
        $sql = "INSERT INTO panier (utilisateur_id, activite_id, quantite)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE quantite = quantite + VALUES(quantite)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $activityId, $quantite]);
    }

    public function remove($userId, $activityId) {
        $sql = "DELETE FROM panier WHERE utilisateur_id = ? AND activite_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $activityId]);
    }

    public function updateQuantity($userId, $activityId, $quantite) {
        if ($quantite <= 0) {
            return $this->remove($userId, $activityId);
        }
        $sql = "UPDATE panier SET quantite = ? WHERE utilisateur_id = ? AND activite_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$quantite, $userId, $activityId]);
    }

    public function clear($userId) {
        $sql = "DELETE FROM panier WHERE utilisateur_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }

    public function count($userId) {
        $sql = "SELECT COALESCE(SUM(quantite), 0) AS total FROM panier WHERE utilisateur_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return intval($row['total']);
    }

    public function total($userId) {
        $sql = "SELECT COALESCE(SUM(p.quantite * a.prix), 0) AS total
                FROM panier p
                JOIN activite a ON p.activite_id = a.id
                WHERE p.utilisateur_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return floatval($row['total']);
    }

    public function exists($userId, $activityId) {
        $sql = "SELECT id FROM panier WHERE utilisateur_id = ? AND activite_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $activityId]);
        return $stmt->fetch() !== false;
    }
}
