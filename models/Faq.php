<?php
class Faq {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $sql = "SELECT * FROM faq ORDER BY ordre ASC";
        $stmt = $this->db->query($sql);
        $faqs = $stmt->fetchAll();
        return $faqs;
    }

    public function find($id) {
        $sql = "SELECT * FROM faq WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $faq = $stmt->fetch();
        return $faq;
    }

    public function create($data) {
        $sql = "INSERT INTO faq (question, reponse, ordre) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['question'],
            $data['reponse'],
            $data['ordre'],
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE faq SET question = ?, reponse = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['question'],
            $data['reponse'],
            $id,
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM faq WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
