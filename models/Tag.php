<?php
class Tag {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function all() {
        $sql = "SELECT t.*,
                       (SELECT COUNT(*) FROM activite_tag at WHERE at.tag_id = t.id) AS nb_activites
                FROM tag t
                ORDER BY t.nom ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function popular($limit = 12) {
        $sql = "SELECT t.*,
                       (SELECT COUNT(*) FROM activite_tag at WHERE at.tag_id = t.id) AS nb_activites
                FROM tag t
                ORDER BY nb_activites DESC, t.nom ASC
                LIMIT " . intval($limit);
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function findBySlug($slug) {
        $sql = "SELECT * FROM tag WHERE slug = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public function getForActivity($activiteId) {
        $sql = "SELECT t.* FROM tag t
                JOIN activite_tag at ON at.tag_id = t.id
                WHERE at.activite_id = ?
                ORDER BY t.nom ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activiteId]);
        return $stmt->fetchAll();
    }

    public function syncForActivity($activiteId, $tagsString) {
        $names = array_filter(array_map('trim', preg_split('/[,;]/', (string)$tagsString)));
        $ids = [];
        foreach ($names as $name) {
            $name = mb_substr($name, 0, 50);
            if ($name === '') continue;
            $slug = $this->slugify($name);
            if ($slug === '') continue;
            $tag = $this->findOrCreate($name, $slug);
            if ($tag) $ids[] = intval($tag['id']);
        }

        // Remplace les associations existantes
        $this->db->prepare("DELETE FROM activite_tag WHERE activite_id = ?")->execute([$activiteId]);
        if (!empty($ids)) {
            $stmt = $this->db->prepare("INSERT IGNORE INTO activite_tag (activite_id, tag_id) VALUES (?, ?)");
            foreach (array_unique($ids) as $tagId) {
                $stmt->execute([$activiteId, $tagId]);
            }
        }
    }

    private function findOrCreate($nom, $slug) {
        $existing = $this->findBySlug($slug);
        if ($existing) return $existing;
        $stmt = $this->db->prepare("INSERT INTO tag (nom, slug) VALUES (?, ?)");
        try {
            $stmt->execute([$nom, $slug]);
            return ['id' => $this->db->lastInsertId(), 'nom' => $nom, 'slug' => $slug];
        } catch (PDOException $e) {
            return $this->findBySlug($slug);
        }
    }

    public function slugify($text) {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        if (function_exists('iconv')) {
            $text = @iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }
        $text = strtolower(trim($text, '-'));
        $text = preg_replace('~[^-a-z0-9]+~', '', $text);
        return $text;
    }
}
