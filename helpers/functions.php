<?php

function redirect($page, $params = []) {
    $url = 'index.php?page=' . $page;
    if (!empty($params)) {
        $url .= '&' . http_build_query($params);
    }
    header("Location: $url");
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isOrganisateur() {
    return isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['organisateur', 'administrateur']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'administrateur';
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
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
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
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->y > 0) return "il y a " . $diff->y . " an" . ($diff->y > 1 ? "s" : "");
    if ($diff->m > 0) return "il y a " . $diff->m . " mois";
    if ($diff->d > 0) return "il y a " . $diff->d . " jour" . ($diff->d > 1 ? "s" : "");
    if ($diff->h > 0) return "il y a " . $diff->h . " heure" . ($diff->h > 1 ? "s" : "");
    if ($diff->i > 0) return "il y a " . $diff->i . " minute" . ($diff->i > 1 ? "s" : "");
    return "à l'instant";
}

function uploadImage($file, $directory) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    if (!in_array($file['type'], $allowedTypes)) {
        return null;
    }

    if ($file['size'] > $maxSize) {
        return null;
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $uploadPath = __DIR__ . '/../uploads/' . $directory . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return 'uploads/' . $directory . '/' . $filename;
    }

    return null;
}

function getPlacesRestantes($activiteId) {
    $activity = new Activity();
    $act = $activity->find($activiteId);
    if (!$act) return 0;

    $registration = new Registration();
    $count = $registration->countByActivity($activiteId);
    return max(0, $act['nb_max_participants'] - $count);
}
