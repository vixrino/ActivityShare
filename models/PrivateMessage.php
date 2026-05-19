<?php
class PrivateMessage {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function send($expediteurId, $destinataireId, $contenu) {
        $sql = "INSERT INTO message_prive (expediteur_id, destinataire_id, contenu)
                VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$expediteurId, $destinataireId, $contenu]);
        return $this->db->lastInsertId();
    }

    public function getConversation($userId, $otherUserId) {
        $sql = "SELECT mp.*,
                       eu.prenom AS exp_prenom, eu.nom AS exp_nom, eu.photo_profil AS exp_photo
                FROM message_prive mp
                JOIN utilisateur eu ON mp.expediteur_id = eu.id
                WHERE (mp.expediteur_id = ? AND mp.destinataire_id = ?)
                   OR (mp.expediteur_id = ? AND mp.destinataire_id = ?)
                ORDER BY mp.date_envoi ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $otherUserId, $otherUserId, $userId]);
        return $stmt->fetchAll();
    }

    public function getConversations($userId) {
        $sql = "SELECT u.id, u.prenom, u.nom, u.photo_profil,
                       (SELECT contenu FROM message_prive
                        WHERE (expediteur_id = u.id AND destinataire_id = ?)
                           OR (expediteur_id = ? AND destinataire_id = u.id)
                        ORDER BY date_envoi DESC LIMIT 1) AS dernier_message,
                       (SELECT date_envoi FROM message_prive
                        WHERE (expediteur_id = u.id AND destinataire_id = ?)
                           OR (expediteur_id = ? AND destinataire_id = u.id)
                        ORDER BY date_envoi DESC LIMIT 1) AS derniere_date,
                       (SELECT COUNT(*) FROM message_prive
                        WHERE expediteur_id = u.id AND destinataire_id = ? AND lu = 0) AS non_lus
                FROM utilisateur u
                WHERE u.id IN (
                    SELECT DISTINCT IF(expediteur_id = ?, destinataire_id, expediteur_id)
                    FROM message_prive
                    WHERE expediteur_id = ? OR destinataire_id = ?
                )
                ORDER BY derniere_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $userId, $userId, $userId, $userId, $userId, $userId, $userId]);
        return $stmt->fetchAll();
    }

    public function markRead($expediteurId, $destinataireId) {
        $sql = "UPDATE message_prive SET lu = 1
                WHERE expediteur_id = ? AND destinataire_id = ? AND lu = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$expediteurId, $destinataireId]);
    }

    public function countUnread($userId) {
        $sql = "SELECT COUNT(*) AS total FROM message_prive WHERE destinataire_id = ? AND lu = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return intval($row['total']);
    }
}
