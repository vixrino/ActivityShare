<?php
class Category {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $sql = "SELECT * FROM categorie ORDER BY nom";
        $stmt = $this->db->query($sql);
        $categories = $stmt->fetchAll();
        return $categories;
    }

    public function find($id) {
        $sql = "SELECT * FROM categorie WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $categorie = $stmt->fetch();
        return $categorie;
    }
}
