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
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    $data = trim((string)$data);
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Récupère et nettoie une valeur GET/POST (string), supprime les caractères
 * de contrôle et limite la taille. Pour usage en stockage interne avant
 * affichage (l'échappement HTML se fait au moment du rendu via sanitize()).
 */
function inputString($source, $key, $maxLen = 1000) {
    $value = isset($source[$key]) ? (string)$source[$key] : '';
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
    $value = trim($value);
    if (mb_strlen($value) > $maxLen) {
        $value = mb_substr($value, 0, $maxLen);
    }
    return $value;
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

/**
 * Upload image : délègue à secureImageUpload() qui vérifie le MIME réel
 * via finfo, la taille, l'extension whitelisted et utilise un nom de fichier
 * aléatoire. Cf. helpers/security.php.
 */
function uploadImage($file, $directory) {
    return secureImageUpload($file, $directory);
}

function formatPrice($prix) {
    $prix = floatval($prix);
    if ($prix <= 0) {
        return 'Gratuit';
    }
    return number_format($prix, 2, ',', ' ') . ' €';
}

function cartCount() {
    if (!isLoggedIn()) {
        return 0;
    }
    $cartModel = new Cart();
    return $cartModel->count($_SESSION['user_id']);
}

function unreadMessages() {
    if (!isLoggedIn()) {
        return 0;
    }
    $messageModel = new PrivateMessage();
    return $messageModel->countUnread($_SESSION['user_id']);
}

function richSanitize($contenu) {
    $contenu = trim($contenu);
    $allowed = '<h1><h2><h3><h4><p><br><strong><em><ul><ol><li><a><blockquote><hr>';
    return strip_tags($contenu, $allowed);
}

function mailConfig() {
    static $config = null;
    if ($config === null) {
        $path = __DIR__ . '/../config/mail.php';
        if (file_exists($path)) {
            $config = require $path;
        } else {
            $config = ['MAIL_ENABLED' => false];
        }
    }
    return $config;
}

function sendMail($to, $subject, $body, $altBody = '') {
    $config = mailConfig();
    if (empty($config['MAIL_ENABLED'])) {
        return false;
    }

    require_once __DIR__ . '/../vendor/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/../vendor/PHPMailer/SMTP.php';
    require_once __DIR__ . '/../vendor/PHPMailer/Exception.php';

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $config['MAIL_HOST'];
        $mail->Port       = (int)$config['MAIL_PORT'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['MAIL_USERNAME'];
        $mail->Password   = $config['MAIL_PASSWORD'];
        if (!empty($config['MAIL_ENCRYPTION'])) {
            $mail->SMTPSecure = $config['MAIL_ENCRYPTION'];
        }
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($config['MAIL_FROM'], $config['MAIL_FROM_NAME'] ?? '');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody !== '' ? $altBody : strip_tags($body);

        return $mail->send();
    } catch (\Exception $e) {
        error_log('[ActivityShare] Echec envoi mail : ' . $mail->ErrorInfo);
        return false;
    }
}

// Rendu d'une note sous forme d'étoiles (5 max).
// $note est un float (ex: 4.3), $total est le nombre d'avis.
function renderStars($note, $total = null) {
    $note = floatval($note);
    $pleines = (int)floor($note);
    $demi = ($note - $pleines) >= 0.5;
    $vides = 5 - $pleines - ($demi ? 1 : 0);

    $html = '<span class="stars" aria-label="Note ' . number_format($note, 1, ',', '') . ' sur 5">';
    for ($i = 0; $i < $pleines; $i++) {
        $html .= '<i class="fas fa-star" aria-hidden="true"></i>';
    }
    if ($demi) {
        $html .= '<i class="fas fa-star-half-alt" aria-hidden="true"></i>';
    }
    for ($i = 0; $i < $vides; $i++) {
        $html .= '<i class="far fa-star" aria-hidden="true"></i>';
    }
    $html .= '</span>';

    if ($total !== null) {
        if ($total > 0) {
            $html .= ' <span class="stars-meta">' . number_format($note, 1, ',', '') . ' / 5 (' . intval($total) . ' avis)</span>';
        } else {
            $html .= ' <span class="stars-meta">Aucun avis</span>';
        }
    }
    return $html;
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
