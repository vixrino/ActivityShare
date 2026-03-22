<?php

function redirect($page, $params = []) {
    $url = 'index.php?page=' . $page;
    if (!empty($params)) {
        foreach ($params as $cle => $valeur) {
            $url = $url . '&' . $cle . '=' . $valeur;
        }
    }
    header("Location: $url");
    exit;
}

function isLoggedIn() {
    if (isset($_SESSION['user_id'])) {
        return true;
    }
    return false;
}

function isOrganisateur() {
    if (isset($_SESSION['user_role'])) {
        if ($_SESSION['user_role'] === 'organisateur' || $_SESSION['user_role'] === 'administrateur') {
            return true;
        }
    }
    return false;
}

function isAdmin() {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'administrateur') {
        return true;
    }
    return false;
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Vous devez être connecté pour accéder à cette page.'];
        redirect('connexion');
    }
}

function requireOrganisateur() {
    requireLogin();
    if (!isOrganisateur()) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Accès réservé aux organisateurs.'];
        redirect('home');
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Accès réservé aux administrateurs.'];
        redirect('home');
    }
}

function sanitize($data) {
    $data = trim($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function formatDate($date) {
    $d = new DateTime($date);
    $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::SHORT);
    return $formatter->format($d);
}

function formatDateShort($date) {
    $d = new DateTime($date);
    return $d->format('d/m/Y');
}

function formatTime($date) {
    $d = new DateTime($date);
    return $d->format('H:i');
}

function timeAgo($datetime) {
    $maintenant = new DateTime();
    $passe = new DateTime($datetime);
    $diff = $maintenant->diff($passe);

    if ($diff->y > 0) {
        return "il y a " . $diff->y . " an(s)";
    }
    if ($diff->m > 0) {
        return "il y a " . $diff->m . " mois";
    }
    if ($diff->d > 0) {
        return "il y a " . $diff->d . " jour(s)";
    }
    if ($diff->h > 0) {
        return "il y a " . $diff->h . " heure(s)";
    }
    if ($diff->i > 0) {
        return "il y a " . $diff->i . " minute(s)";
    }
    return "à l'instant";
}

function uploadImage($file, $directory) {
    $typesAutorises = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $tailleMax = 5 * 1024 * 1024;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    if (!in_array($file['type'], $typesAutorises)) {
        return null;
    }

    if ($file['size'] > $tailleMax) {
        return null;
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $nomFichier = uniqid() . '_' . time() . '.' . $extension;
    $cheminUpload = __DIR__ . '/../uploads/' . $directory . '/' . $nomFichier;

    if (move_uploaded_file($file['tmp_name'], $cheminUpload)) {
        return 'uploads/' . $directory . '/' . $nomFichier;
    }

    return null;
}

function getPlacesRestantes($activiteId) {
    $activiteModel = new Activity();
    $activite = $activiteModel->find($activiteId);

    if (!$activite) {
        return 0;
    }

    $inscriptionModel = new Registration();
    $nbInscrits = $inscriptionModel->countByActivity($activiteId);
    $places = $activite['nb_max_participants'] - $nbInscrits;

    if ($places < 0) {
        $places = 0;
    }

    return $places;
}
