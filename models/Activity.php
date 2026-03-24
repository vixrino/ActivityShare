<?php
class Activity {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function find($id) {
        $sql = "SELECT a.*, c.nom as categorie_nom, c.icone as categorie_icone,
                       u.nom as organisateur_nom, u.prenom as organisateur_prenom, u.email as organisateur_email
                FROM activite a
                JOIN categorie c ON a.categorie_id = c.id
                JOIN utilisateur u ON a.organisateur_id = u.id
                WHERE a.id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $activite = $stmt->fetch();
        return $activite;
    }

    public function getAll($limit = null, $offset = 0, $filters = []) {
        $conditions = [];
        $conditions[] = "a.statut = 'active'";
        $conditions[] = "a.date_debut >= NOW()";
        $params = [];

        if (!empty($filters['categorie'])) {
            $conditions[] = "a.categorie_id = ?";
            $params[] = $filters['categorie'];
        }

        if (!empty($filters['type'])) {
            $conditions[] = "a.type = ?";
            $params[] = $filters['type'];
        }

        if (!empty($filters['recherche'])) {
            $conditions[] = "(a.titre LIKE ? OR a.description LIKE ? OR a.lieu LIKE ?)";
            $terme = '%' . $filters['recherche'] . '%';
            $params[] = $terme;
            $params[] = $terme;
            $params[] = $terme;
        }

        if (!empty($filters['ville'])) {
            $conditions[] = "a.lieu LIKE ?";
            $params[] = '%' . $filters['ville'] . '%';
        }

        if (!empty($filters['date_debut'])) {
            $conditions[] = "a.date_debut >= ?";
            $params[] = $filters['date_debut'];
        }

        if (!empty($filters['date_fin'])) {
            $conditions[] = "a.date_fin <= ?";
            $params[] = $filters['date_fin'];
        }

        $sql = "SELECT a.*, c.nom as categorie_nom, c.icone as categorie_icone,
                       u.nom as organisateur_nom, u.prenom as organisateur_prenom,
                       (SELECT COUNT(*) FROM inscription i WHERE i.activite_id = a.id AND i.statut = 'inscrit') as nb_inscrits
                FROM activite a
                JOIN categorie c ON a.categorie_id = c.id
                JOIN utilisateur u ON a.organisateur_id = u.id
                WHERE " . implode(' AND ', $conditions) . "
                ORDER BY a.date_debut ASC";

        if ($limit !== null) {
            $sql = $sql . " LIMIT " . intval($limit) . " OFFSET " . intval($offset);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $activites = $stmt->fetchAll();
        return $activites;
    }

    public function countAll($filters = []) {
        $conditions = [];
        $conditions[] = "a.statut = 'active'";
        $conditions[] = "a.date_debut >= NOW()";
        $params = [];

        if (!empty($filters['categorie'])) {
            $conditions[] = "a.categorie_id = ?";
            $params[] = $filters['categorie'];
        }

        if (!empty($filters['recherche'])) {
            $conditions[] = "(a.titre LIKE ? OR a.description LIKE ? OR a.lieu LIKE ?)";
            $terme = '%' . $filters['recherche'] . '%';
            $params[] = $terme;
            $params[] = $terme;
            $params[] = $terme;
        }

        $sql = "SELECT COUNT(*) as total FROM activite a WHERE " . implode(' AND ', $conditions);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $resultat = $stmt->fetch();
        return $resultat['total'];
    }

    public function getByOrganisateur($organisateurId) {
        $sql = "SELECT a.*, c.nom as categorie_nom, c.icone as categorie_icone,
                       (SELECT COUNT(*) FROM inscription i WHERE i.activite_id = a.id AND i.statut = 'inscrit') as nb_inscrits
                FROM activite a
                JOIN categorie c ON a.categorie_id = c.id
                WHERE a.organisateur_id = ?
                ORDER BY a.date_debut DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$organisateurId]);
        $activites = $stmt->fetchAll();
        return $activites;
    }

    public function create($data) {
        $sql = "INSERT INTO activite (organisateur_id, titre, description, categorie_id, date_debut, date_fin,
                                      lieu, adresse, nb_max_participants, type, conditions_participation, photo)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['organisateur_id'],
            $data['titre'],
            $data['description'],
            $data['categorie_id'],
            $data['date_debut'],
            $data['date_fin'],
            $data['lieu'],
            $data['adresse'],
            $data['nb_max_participants'],
            $data['type'],
            $data['conditions_participation'],
            $data['photo'],
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

        $sql = "UPDATE activite SET " . implode(', ', $parties) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($valeurs);
    }

    public function delete($id) {
        $sql = "UPDATE activite SET statut = 'annulee' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getRecent($limit = 6) {
        $sql = "SELECT a.*, c.nom as categorie_nom, c.icone as categorie_icone,
                       u.nom as organisateur_nom, u.prenom as organisateur_prenom,
                       (SELECT COUNT(*) FROM inscription i WHERE i.activite_id = a.id AND i.statut = 'inscrit') as nb_inscrits
                FROM activite a
                JOIN categorie c ON a.categorie_id = c.id
                JOIN utilisateur u ON a.organisateur_id = u.id
                WHERE a.statut = 'active' AND a.date_debut >= NOW()
                ORDER BY a.date_creation DESC
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        $activites = $stmt->fetchAll();
        return $activites;
    }

    public function getAllAdmin() {
        $sql = "SELECT a.*, c.nom as categorie_nom,
                       u.nom as organisateur_nom, u.prenom as organisateur_prenom,
                       (SELECT COUNT(*) FROM inscription i WHERE i.activite_id = a.id AND i.statut = 'inscrit') as nb_inscrits
                FROM activite a
                JOIN categorie c ON a.categorie_id = c.id
                JOIN utilisateur u ON a.organisateur_id = u.id
                ORDER BY a.date_creation DESC";

        $stmt = $this->db->query($sql);
        $activites = $stmt->fetchAll();
        return $activites;
    }

    public function countAllAdmin() {
        $sql = "SELECT COUNT(*) as total FROM activite";
        $stmt = $this->db->query($sql);
        $resultat = $stmt->fetch();
        return $resultat['total'];
    }

    public function countActive() {
        $sql = "SELECT COUNT(*) as total FROM activite WHERE statut = 'active' AND date_debut >= NOW()";
        $stmt = $this->db->query($sql);
        $resultat = $stmt->fetch();
        return $resultat['total'];
    }
}
