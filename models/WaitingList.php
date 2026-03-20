<?php
class WaitingList {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function add($activiteId, $participantId) {
        $position = $this->getNextPosition($activiteId);
        $stmt = $this->db->prepare("
            INSERT INTO liste_attente (activite_id, participant_id, position) VALUES (?, ?, ?)
        ");
        return $stmt->execute([$activiteId, $participantId, $position]);
    }

    public function remove($activiteId, $participantId) {
        $stmt = $this->db->prepare("
            DELETE FROM liste_attente WHERE activite_id = ? AND participant_id = ?
        ");
        return $stmt->execute([$activiteId, $participantId]);
    }

    public function isOnWaitingList($activiteId, $participantId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM liste_attente
            WHERE activite_id = ? AND participant_id = ?
        ");
        $stmt->execute([$activiteId, $participantId]);
        return $stmt->fetch()['total'] > 0;
    }

    public function getFirst($activiteId) {
        $stmt = $this->db->prepare("
            SELECT la.*, u.nom, u.prenom, u.email
            FROM liste_attente la
            JOIN utilisateur u ON la.participant_id = u.id
            WHERE la.activite_id = ?
            ORDER BY la.position ASC
            LIMIT 1
        ");
        $stmt->execute([$activiteId]);
        return $stmt->fetch();
    }

    public function getByActivity($activiteId) {
        $stmt = $this->db->prepare("
            SELECT la.*, u.nom, u.prenom, u.email
            FROM liste_attente la
            JOIN utilisateur u ON la.participant_id = u.id
            WHERE la.activite_id = ?
            ORDER BY la.position ASC
        ");
        $stmt->execute([$activiteId]);
        return $stmt->fetchAll();
    }

    public function getPosition($activiteId, $participantId) {
        $stmt = $this->db->prepare("
            SELECT position FROM liste_attente
            WHERE activite_id = ? AND participant_id = ?
        ");
        $stmt->execute([$activiteId, $participantId]);
        $result = $stmt->fetch();
        return $result ? $result['position'] : null;
    }

    private function getNextPosition($activiteId) {
        $stmt = $this->db->prepare("
            SELECT COALESCE(MAX(position), 0) + 1 as next_pos FROM liste_attente WHERE activite_id = ?
        ");
        $stmt->execute([$activiteId]);
        return $stmt->fetch()['next_pos'];
    }

    public function promoteFirst($activiteId) {
        $first = $this->getFirst($activiteId);
        if ($first) {
            $registration = new Registration();
            $registration->create($activiteId, $first['participant_id']);
            $this->remove($activiteId, $first['participant_id']);

            $notification = new Notification();
            $notification->create([
                'utilisateur_id' => $first['participant_id'],
                'type' => 'place_disponible',
                'titre' => 'Place disponible !',
                'message' => 'Une place s\'est libérée et vous avez été automatiquement inscrit à l\'activité.',
            ]);

            return $first;
        }
        return null;
    }
}
