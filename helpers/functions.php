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
