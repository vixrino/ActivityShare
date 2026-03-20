<?php
class Activity {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function find($id) {
        $stmt = $this->db->prepare("
            SELECT a.*, c.nom as categorie_nom, c.icone as categorie_icone,
                   u.nom as organisateur_nom, u.prenom as organisateur_prenom, u.email as organisateur_email
            FROM activite a
            JOIN categorie c ON a.categorie_id = c.id
            JOIN utilisateur u ON a.organisateur_id = u.id
            WHERE a.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAll($limit = null, $offset = 0, $filters = []) {
        $where = ["a.statut = 'active'", "a.date_debut >= NOW()"];
        $params = [];

        if (!empty($filters['categorie'])) {
            $where[] = "a.categorie_id = ?";
            $params[] = $filters['categorie'];
        }
        if (!empty($filters['type'])) {
            $where[] = "a.type = ?";
            $params[] = $filters['type'];
        }
        if (!empty($filters['recherche'])) {
            $where[] = "(a.titre LIKE ? OR a.description LIKE ? OR a.lieu LIKE ?)";
            $term = '%' . $filters['recherche'] . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }
        if (!empty($filters['ville'])) {
            $where[] = "a.lieu LIKE ?";
            $params[] = '%' . $filters['ville'] . '%';
        }
        if (!empty($filters['date_debut'])) {
            $where[] = "a.date_debut >= ?";
            $params[] = $filters['date_debut'];
        }
        if (!empty($filters['date_fin'])) {
            $where[] = "a.date_fin <= ?";
            $params[] = $filters['date_fin'];
        }

        $sql = "
            SELECT a.*, c.nom as categorie_nom, c.icone as categorie_icone,
                   u.nom as organisateur_nom, u.prenom as organisateur_prenom,
                   (SELECT COUNT(*) FROM inscription i WHERE i.activite_id = a.id AND i.statut = 'inscrit') as nb_inscrits
            FROM activite a
            JOIN categorie c ON a.categorie_id = c.id
            JOIN utilisateur u ON a.organisateur_id = u.id
            WHERE " . implode(' AND ', $where) . "
            ORDER BY a.date_debut ASC
        ";

        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countAll($filters = []) {
        $where = ["a.statut = 'active'", "a.date_debut >= NOW()"];
        $params = [];

        if (!empty($filters['categorie'])) {
            $where[] = "a.categorie_id = ?";
            $params[] = $filters['categorie'];
        }
        if (!empty($filters['recherche'])) {
            $where[] = "(a.titre LIKE ? OR a.description LIKE ? OR a.lieu LIKE ?)";
            $term = '%' . $filters['recherche'] . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql = "SELECT COUNT(*) as total FROM activite a WHERE " . implode(' AND ', $where);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['total'];
    }

    public function getByOrganisateur($organisateurId) {
        $stmt = $this->db->prepare("
            SELECT a.*, c.nom as categorie_nom, c.icone as categorie_icone,
                   (SELECT COUNT(*) FROM inscription i WHERE i.activite_id = a.id AND i.statut = 'inscrit') as nb_inscrits
            FROM activite a
            JOIN categorie c ON a.categorie_id = c.id
            WHERE a.organisateur_id = ?
            ORDER BY a.date_debut DESC
        ");
        $stmt->execute([$organisateurId]);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO activite (organisateur_id, titre, description, categorie_id, date_debut, date_fin,
                                  lieu, adresse, nb_max_participants, type, conditions_participation, photo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['organisateur_id'],
            $data['titre'],
            $data['description'],
            $data['categorie_id'],
            $data['date_debut'],
            $data['date_fin'],
            $data['lieu'],
            $data['adresse'] ?? null,
            $data['nb_max_participants'],
            $data['type'] ?? 'public',
            $data['conditions_participation'] ?? null,
            $data['photo'] ?? null,
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $stmt = $this->db->prepare("UPDATE activite SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($values);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE activite SET statut = 'annulee' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getRecent($limit = 6) {
        $stmt = $this->db->prepare("
            SELECT a.*, c.nom as categorie_nom, c.icone as categorie_icone,
                   u.nom as organisateur_nom, u.prenom as organisateur_prenom,
                   (SELECT COUNT(*) FROM inscription i WHERE i.activite_id = a.id AND i.statut = 'inscrit') as nb_inscrits
            FROM activite a
            JOIN categorie c ON a.categorie_id = c.id
            JOIN utilisateur u ON a.organisateur_id = u.id
            WHERE a.statut = 'active' AND a.date_debut >= NOW()
            ORDER BY a.date_creation DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getAllAdmin() {
        $stmt = $this->db->query("
            SELECT a.*, c.nom as categorie_nom,
                   u.nom as organisateur_nom, u.prenom as organisateur_prenom,
                   (SELECT COUNT(*) FROM inscription i WHERE i.activite_id = a.id AND i.statut = 'inscrit') as nb_inscrits
            FROM activite a
            JOIN categorie c ON a.categorie_id = c.id
            JOIN utilisateur u ON a.organisateur_id = u.id
            ORDER BY a.date_creation DESC
        ");
        return $stmt->fetchAll();
    }

    public function countAllAdmin() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM activite");
        return $stmt->fetch()['total'];
    }

    public function countActive() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM activite WHERE statut = 'active' AND date_debut >= NOW()");
        return $stmt->fetch()['total'];
    }
}
