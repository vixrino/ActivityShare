<?php
class Forum {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getCategories() {
        $sql = "SELECT fc.*,
                       (SELECT COUNT(*) FROM forum_topic ft WHERE ft.forum_categorie_id = fc.id) AS nb_topics,
                       (SELECT COUNT(*) FROM forum_message fm
                            JOIN forum_topic ft ON fm.forum_topic_id = ft.id
                            WHERE ft.forum_categorie_id = fc.id) AS nb_messages
                FROM forum_categorie fc
                ORDER BY fc.ordre ASC, fc.id ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function findCategory($id) {
        $sql = "SELECT * FROM forum_categorie WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getTopics($categorieId) {
        $sql = "SELECT ft.*, u.prenom, u.nom, u.photo_profil,
                       (SELECT COUNT(*) FROM forum_message fm WHERE fm.forum_topic_id = ft.id) AS nb_reponses,
                       (SELECT MAX(fm.date_envoi) FROM forum_message fm WHERE fm.forum_topic_id = ft.id) AS derniere_reponse
                FROM forum_topic ft
                JOIN utilisateur u ON ft.utilisateur_id = u.id
                WHERE ft.forum_categorie_id = ?
                ORDER BY ft.epingle DESC, COALESCE((SELECT MAX(fm.date_envoi) FROM forum_message fm WHERE fm.forum_topic_id = ft.id), ft.date_creation) DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categorieId]);
        return $stmt->fetchAll();
    }

    public function findTopic($id) {
        $sql = "SELECT ft.*, u.prenom, u.nom, u.photo_profil, u.role,
                       fc.nom AS categorie_nom, fc.id AS categorie_id, fc.icone AS categorie_icone
                FROM forum_topic ft
                JOIN utilisateur u ON ft.utilisateur_id = u.id
                JOIN forum_categorie fc ON ft.forum_categorie_id = fc.id
                WHERE ft.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createTopic($data) {
        $sql = "INSERT INTO forum_topic (forum_categorie_id, utilisateur_id, titre, contenu)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['forum_categorie_id'],
            $data['utilisateur_id'],
            $data['titre'],
            $data['contenu'],
        ]);
        return $this->db->lastInsertId();
    }

    public function getMessages($topicId) {
        $sql = "SELECT fm.*, u.prenom, u.nom, u.photo_profil, u.role
                FROM forum_message fm
                JOIN utilisateur u ON fm.utilisateur_id = u.id
                WHERE fm.forum_topic_id = ?
                ORDER BY fm.date_envoi ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$topicId]);
        return $stmt->fetchAll();
    }

    public function postMessage($topicId, $userId, $contenu) {
        $sql = "INSERT INTO forum_message (forum_topic_id, utilisateur_id, contenu)
                VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$topicId, $userId, $contenu]);
        return $this->db->lastInsertId();
    }

    public function incrementViews($topicId) {
        $sql = "UPDATE forum_topic SET nb_vues = nb_vues + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$topicId]);
    }

    public function deleteTopic($id) {
        $sql = "DELETE FROM forum_topic WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function deleteMessage($id) {
        $sql = "DELETE FROM forum_message WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function togglePin($topicId) {
        $sql = "UPDATE forum_topic SET epingle = NOT epingle WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$topicId]);
    }
}
