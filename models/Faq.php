<?php
class Faq {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM faq ORDER BY ordre ASC");
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM faq WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO faq (question, reponse, ordre) VALUES (?, ?, ?)");
        return $stmt->execute([$data['question'], $data['reponse'], $data['ordre'] ?? 0]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE faq SET question = ?, reponse = ? WHERE id = ?");
        return $stmt->execute([$data['question'], $data['reponse'], $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM faq WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
