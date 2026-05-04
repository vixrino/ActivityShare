<?php
// ============================================
// ActivityShare - Helpers de sécurité
//   * Sessions durcies
//   * Headers HTTP
//   * Jetons CSRF
//   * Validation d'uploads
//   * Brute force login
//   * Journalisation
// ============================================

/**
 * Démarre la session avec des paramètres durcis : cookie HttpOnly,
 * SameSite=Lax, Secure si HTTPS. La régénération de l'ID est faite
 * périodiquement pour limiter les attaques de fixation de session.
 */
function secureSessionStart() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');

    session_start();

    // Lien session ↔ user-agent : détection grossière de vol de session
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if (!isset($_SESSION['_ua_hash'])) {
        $_SESSION['_ua_hash'] = hash('sha256', $ua);
    } elseif ($_SESSION['_ua_hash'] !== hash('sha256', $ua)) {
        // user-agent différent : on coupe la session
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        session_start();
        $_SESSION['_ua_hash'] = hash('sha256', $ua);
    }

    // Régénère l'identifiant de session toutes les 20 minutes
    if (!isset($_SESSION['_regen_at'])) {
        $_SESSION['_regen_at'] = time();
    } elseif (time() - $_SESSION['_regen_at'] > 1200) {
        session_regenerate_id(true);
        $_SESSION['_regen_at'] = time();
    }
}

/**
 * Envoie les headers HTTP de sécurité. Le CSP est volontairement souple
 * pour rester compatible avec FontAwesome (CDN) et les images uploadées.
 */
function sendSecurityHeaders() {
    if (headers_sent()) {
        return;
    }
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

    // CSP : protège contre XSS sans casser les ressources externes utilisées
    $csp = "default-src 'self'; "
        . "img-src 'self' data: https://api.qrserver.com; "
        . "font-src 'self' https://cdnjs.cloudflare.com data:; "
        . "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; "
        . "script-src 'self' 'unsafe-inline'; "
        . "frame-ancestors 'self'; "
        . "base-uri 'self'; "
        . "form-action 'self'";
    header('Content-Security-Policy: ' . $csp);

    // HSTS uniquement en HTTPS
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
    if ($https) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

// ============================================
// CSRF
// ============================================

function csrfToken() {
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrfField() {
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Vérifie le jeton CSRF posté. À appeler en tête des actions POST sensibles.
 * Le formulaire de login est exclu car protégé par le rate-limiting,
 * et la session n'est pas encore identifiée à ce stade.
 */
function csrfVerify() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return true;
    }
    $sent = isset($_POST['_csrf']) ? (string)$_POST['_csrf'] : '';
    $expected = isset($_SESSION['_csrf']) ? (string)$_SESSION['_csrf'] : '';
    if ($sent === '' || $expected === '' || !hash_equals($expected, $sent)) {
        http_response_code(419);
        if (function_exists('logSecurity')) {
            logSecurity('csrf_failed', $_SERVER['REQUEST_URI'] ?? '');
        }
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Session expirée. Merci de recommencer.'];
        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        header('Location: ' . $referer);
        exit;
    }
    return true;
}

// ============================================
// Adresse IP / journalisation
// ============================================

function clientIp() {
    $candidates = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($candidates as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = trim(explode(',', $_SERVER[$key])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return '0.0.0.0';
}

function logSecurity($action, $details = '') {
    // Évite une dépendance dure au modèle (utilisé tôt dans le bootstrap).
    try {
        if (class_exists('SecurityLog')) {
            $model = new SecurityLog();
            $model->log(
                isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null,
                clientIp(),
                $action,
                $details
            );
        }
    } catch (\Throwable $e) {
        error_log('[ActivityShare] logSecurity error : ' . $e->getMessage());
    }
}

// ============================================
// Upload sécurisé (image)
// ============================================

/**
 * Validation stricte d'une image uploadée. Vérifie le MIME réel via finfo,
 * la taille, l'extension whitelisted et utilise un nom de fichier aléatoire.
 * Retourne le chemin web (uploads/...) ou null en cas d'échec.
 */
function secureImageUpload($file, $directory) {
    if (empty($file) || !is_array($file)) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    if (!is_uploaded_file($file['tmp_name'])) return null;

    $maxSize = 5 * 1024 * 1024; // 5 Mo
    if ($file['size'] <= 0 || $file['size'] > $maxSize) return null;

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!isset($allowed[$mime])) return null;
    $ext = $allowed[$mime];

    // Vérifie aussi que c'est bien une image (anti spoof)
    $dim = @getimagesize($file['tmp_name']);
    if ($dim === false || empty($dim[0]) || empty($dim[1])) return null;

    $safeDir = preg_replace('/[^a-z0-9_-]/i', '', $directory);
    $targetDir = __DIR__ . '/../uploads/' . $safeDir;
    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0755, true);
    }
    $name = bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
    $target = $targetDir . '/' . $name;

    if (!move_uploaded_file($file['tmp_name'], $target)) return null;
    @chmod($target, 0644);

    return 'uploads/' . $safeDir . '/' . $name;
}
