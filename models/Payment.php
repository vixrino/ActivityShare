<?php
class Payment {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data, $lignes) {
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO paiement
                        (utilisateur_id, reference, montant_total, titulaire_carte, derniers_chiffres,
                         methode, statut, adresse_facturation, ville_facturation, code_postal, pays)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['utilisateur_id'],
                $data['reference'],
                $data['montant_total'],
                $data['titulaire_carte'],
                $data['derniers_chiffres'],
                $data['methode'] ?? 'carte',
                $data['statut'] ?? 'confirme',
                $data['adresse_facturation'] ?? null,
                $data['ville_facturation'] ?? null,
                $data['code_postal'] ?? null,
                $data['pays'] ?? null,
            ]);
            $paiementId = $this->db->lastInsertId();

            $sqlLigne = "INSERT INTO paiement_ligne (paiement_id, activite_id, titre, prix_unitaire, quantite)
                         VALUES (?, ?, ?, ?, ?)";
            $stmtLigne = $this->db->prepare($sqlLigne);
            foreach ($lignes as $ligne) {
                $stmtLigne->execute([
                    $paiementId,
                    $ligne['activite_id'],
                    $ligne['titre'],
                    $ligne['prix_unitaire'],
                    $ligne['quantite'],
                ]);
            }

            $this->db->commit();
            return $paiementId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function find($id) {
        $sql = "SELECT * FROM paiement WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByReference($reference) {
        $sql = "SELECT * FROM paiement WHERE reference = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$reference]);
        return $stmt->fetch();
    }

    public function getLines($paiementId) {
        $sql = "SELECT * FROM paiement_ligne WHERE paiement_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$paiementId]);
        return $stmt->fetchAll();
    }

    public function getByUser($userId) {
        $sql = "SELECT * FROM paiement WHERE utilisateur_id = ? ORDER BY date_paiement DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function generateReference() {
        return 'ASH-' . strtoupper(bin2hex(random_bytes(4))) . '-' . date('ymd');
    }
}
