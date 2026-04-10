<?php
class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function find($id) {
        $sql = "SELECT * FROM utilisateur WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user;
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM utilisateur WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user;
    }

    public function create($data) {
        $sql = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role, telephone, ville)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);

        $motDePasseHash = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);

        $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $motDePasseHash,
            $data['role'],
            $data['telephone'],
            $data['ville'],
        ]);

        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $parties = [];
        $valeurs = [];

        foreach ($data as $colonne => $valeur) {
            $parties[] = "$colonne = ?";
            $valeurs[] = $valeur;
        }

        $valeurs[] = $id;

        $sql = "UPDATE utilisateur SET " . implode(', ', $parties) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($valeurs);
    }

    public function updatePassword($id, $nouveauMotDePasse) {
        $sql = "UPDATE utilisateur SET mot_de_passe = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $hash = password_hash($nouveauMotDePasse, PASSWORD_DEFAULT);
        return $stmt->execute([$hash, $id]);
    }

    public function getAll() {
        $sql = "SELECT * FROM utilisateur ORDER BY date_inscription DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM utilisateur";
        $stmt = $this->db->query($sql);
        $resultat = $stmt->fetch();
        return $resultat['total'];
    }

    public function countByRole($role) {
        $sql = "SELECT COUNT(*) as total FROM utilisateur WHERE role = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$role]);
        $resultat = $stmt->fetch();
        return $resultat['total'];
    }

    public function toggleActive($id) {
        $sql = "UPDATE utilisateur SET actif = NOT actif WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function verify($email, $motDePasse) {
        $user = $this->findByEmail($email);

        if ($user && password_verify($motDePasse, $user['mot_de_passe'])) {
            return $user;
        }

        return false;
    }
}
