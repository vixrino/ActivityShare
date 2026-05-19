<?php
class EditorialContent {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByKey($cle) {
        $sql = "SELECT * FROM contenu_editorial WHERE cle = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cle]);
        return $stmt->fetch();
    }

    public function getAll() {
        $sql = "SELECT * FROM contenu_editorial ORDER BY cle ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function update($cle, $titre, $contenu) {
        $sql = "UPDATE contenu_editorial SET titre = ?, contenu = ? WHERE cle = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$titre, $contenu, $cle]);
    }
}
