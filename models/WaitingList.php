<?php
class WaitingList {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function add($activiteId, $participantId) {
        $position = $this->getNextPosition($activiteId);

        $sql = "INSERT INTO liste_attente (activite_id, participant_id, position) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$activiteId, $participantId, $position]);
    }

    public function remove($activiteId, $participantId) {
        $sql = "DELETE FROM liste_attente WHERE activite_id = ? AND participant_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$activiteId, $participantId]);
    }

    public function isOnWaitingList($activiteId, $participantId) {
        $sql = "SELECT COUNT(*) as total FROM liste_attente
                WHERE activite_id = ? AND participant_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activiteId, $participantId]);
        $resultat = $stmt->fetch();

        if ($resultat['total'] > 0) {
            return true;
        }
        return false;
    }

    public function getFirst($activiteId) {
        $sql = "SELECT la.*, u.nom, u.prenom, u.email
                FROM liste_attente la
                JOIN utilisateur u ON la.participant_id = u.id
                WHERE la.activite_id = ?
                ORDER BY la.position ASC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activiteId]);
        $premier = $stmt->fetch();
        return $premier;
    }

    public function getByActivity($activiteId) {
        $sql = "SELECT la.*, u.nom, u.prenom, u.email
                FROM liste_attente la
                JOIN utilisateur u ON la.participant_id = u.id
                WHERE la.activite_id = ?
                ORDER BY la.position ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activiteId]);
        $liste = $stmt->fetchAll();
        return $liste;
    }

    public function getPosition($activiteId, $participantId) {
        $sql = "SELECT position FROM liste_attente
                WHERE activite_id = ? AND participant_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activiteId, $participantId]);
        $resultat = $stmt->fetch();

        if ($resultat) {
            return $resultat['position'];
        }
        return null;
    }

    private function getNextPosition($activiteId) {
        $sql = "SELECT MAX(position) as max_position FROM liste_attente WHERE activite_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activiteId]);
        $resultat = $stmt->fetch();

        if ($resultat['max_position'] === null) {
            return 1;
        }
        return $resultat['max_position'] + 1;
    }

    public function promoteFirst($activiteId) {
        $premier = $this->getFirst($activiteId);

        if ($premier) {
            $inscriptionModel = new Registration();
            $inscriptionModel->create($activiteId, $premier['participant_id']);

            $this->remove($activiteId, $premier['participant_id']);

            $notificationModel = new Notification();
            $notificationModel->create([
                'utilisateur_id' => $premier['participant_id'],
                'type' => 'place_disponible',
                'titre' => 'Place disponible !',
                'message' => 'Une place s\'est libérée et vous avez été automatiquement inscrit à l\'activité.',
            ]);

            return $premier;
        }

        return null;
    }
}
