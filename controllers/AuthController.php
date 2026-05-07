<?php
class AuthController {

    public function login() {
        if (isLoggedIn()) {
            redirect('home');
        }

        $errors = [];
        $ip = clientIp();
        $attempts = new LoginAttempt();
        $attempts->purgeOld();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfVerify();
            $email = inputString($_POST, 'email', 255);
            $password = isset($_POST['mot_de_passe']) ? (string)$_POST['mot_de_passe'] : '';

            // Protection brute force : on bloque AVANT toute vérification
            if ($attempts->isBlocked($email, $ip)) {
                logSecurity('login_blocked', 'IP ' . $ip . ' email ' . $email);
                $errors[] = 'Trop de tentatives échouées. Réessayez dans 15 minutes.';
            } elseif (empty($email) || empty($password)) {
                $errors[] = 'Veuillez remplir tous les champs.';
            } else {
                $userModel = new User();
                $user = $userModel->verify($email, $password);

                if ($user) {
                    if (!$user['actif']) {
                        $errors[] = 'Votre compte a été désactivé. Contactez un administrateur.';
                        $attempts->record($email, $ip, false);
                        logSecurity('login_disabled', 'user_id=' . $user['id']);
                    } else {
                        $attempts->record($email, $ip, true);
                        $attempts->clearFor($email, $ip);
                        session_regenerate_id(true);
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_nom'] = $user['nom'];
                        $_SESSION['user_prenom'] = $user['prenom'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['user_role'] = $user['role'];
                        $_SESSION['user_photo'] = $user['photo_profil'];
                        logSecurity('login_success', 'user_id=' . $user['id']);

                        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Bienvenue, ' . $user['prenom'] . ' !'];

                        if ($user['role'] === 'administrateur') {
                            redirect('admin');
                        } else {
                            redirect('home');
                        }
                    }
                } else {
                    $attempts->record($email, $ip, false);
                    logSecurity('login_failed', 'email=' . $email);
                    $errors[] = 'Email ou mot de passe incorrect.';

                    // Affiche un avertissement quand l'utilisateur s'approche du blocage
                    $left = LoginAttempt::MAX_ATTEMPTS_EMAIL - $attempts->countRecentFailuresByEmail($email);
                    if ($left > 0 && $left <= 2) {
                        $errors[] = 'Il vous reste ' . $left . ' tentative(s) avant blocage temporaire.';
                    }
                }
            }
        }

        $pageTitle = 'Connexion';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/auth/login.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function register() {
        if (isLoggedIn()) {
            redirect('home');
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfVerify();
            $nom = sanitize($_POST['nom']);
            $prenom = sanitize($_POST['prenom']);
            $email = sanitize($_POST['email']);
            $password = $_POST['mot_de_passe'];
            $passwordConfirm = $_POST['mot_de_passe_confirm'];
            $role = sanitize($_POST['role']);
            $telephone = sanitize($_POST['telephone']);
            $ville = sanitize($_POST['ville']);

            if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
                $errors[] = 'Veuillez remplir tous les champs obligatoires.';
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Adresse e-mail invalide.';
            }

            if (strlen($password) < 8) {
                $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
            }

            if ($password !== $passwordConfirm) {
                $errors[] = 'Les mots de passe ne correspondent pas.';
            }

            if ($role !== 'participant' && $role !== 'organisateur') {
                $errors[] = 'Rôle invalide.';
            }

            $userModel = new User();
            $utilisateurExistant = $userModel->findByEmail($email);
            if ($utilisateurExistant) {
                $errors[] = 'Cette adresse e-mail est déjà utilisée.';
            }

            if (empty($errors)) {
                $userId = $userModel->create([
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'mot_de_passe' => $password,
                    'role' => $role,
                    'telephone' => $telephone,
                    'ville' => $ville,
                ]);

                $_SESSION['user_id'] = $userId;
                $_SESSION['user_nom'] = $nom;
                $_SESSION['user_prenom'] = $prenom;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $role;
                $_SESSION['user_photo'] = null;

                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Inscription réussie ! Bienvenue sur ActivityShare.'];
                redirect('home');
            }
        }

        $pageTitle = 'Inscription';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/auth/register.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function logout() {
        $uid = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        logSecurity('logout', 'user_id=' . $uid);
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        secureSessionStart();
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Vous avez été déconnecté.'];
        redirect('home');
    }

    public function forgotPassword() {
        $errors = [];
        $success = false;
        $demoLink = null;
        $mailSent = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfVerify();
            $email = sanitize($_POST['email']);

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Veuillez entrer une adresse e-mail valide.';
            } else {
                $userModel = new User();
                $user = $userModel->findByEmail($email);

                if ($user) {
                    $resetModel = new PasswordReset();
                    $token = $resetModel->create($user['id'], $email);

                    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
                    $baseUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
                    $baseUrl = rtrim($baseUrl, '/');
                    $resetLink = $baseUrl . '/index.php?page=reinitialiser-mot-de-passe&token=' . $token;

                    $config = mailConfig();
                    if (!empty($config['MAIL_ENABLED'])) {
                        $htmlBody = '<div style="font-family:Arial,Helvetica,sans-serif;font-size:15px;color:#333;max-width:560px;margin:auto;">'
                            . '<h2 style="color:#4caf50;">ActivityShare</h2>'
                            . '<p>Bonjour <strong>' . sanitize($user['prenom']) . '</strong>,</p>'
                            . '<p>Vous avez demandé la réinitialisation de votre mot de passe.</p>'
                            . '<p>Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe (lien valable 1 heure) :</p>'
                            . '<p style="text-align:center;margin:30px 0;">'
                            .   '<a href="' . $resetLink . '" style="background:#4caf50;color:#fff;padding:12px 24px;text-decoration:none;border-radius:8px;font-weight:bold;">Réinitialiser mon mot de passe</a>'
                            . '</p>'
                            . '<p style="font-size:13px;color:#666;">Ou copiez ce lien dans votre navigateur :<br>'
                            . '<a href="' . $resetLink . '">' . $resetLink . '</a></p>'
                            . '<hr style="border:none;border-top:1px solid #eee;margin:24px 0;">'
                            . '<p style="font-size:12px;color:#999;">Si vous n\'êtes pas à l\'origine de cette demande, ignorez simplement cet e-mail.<br>'
                            . 'L\'équipe ActivityShare</p>'
                            . '</div>';

                        $textBody = "Bonjour " . $user['prenom'] . ",\n\n"
                            . "Vous avez demandé la réinitialisation de votre mot de passe.\n"
                            . "Cliquez sur ce lien (valable 1h) :\n" . $resetLink . "\n\n"
                            . "Si vous n'êtes pas à l'origine de cette demande, ignorez ce message.\n\n"
                            . "L'équipe ActivityShare";

                        $mailSent = sendMail(
                            $email,
                            'ActivityShare - Réinitialisation de votre mot de passe',
                            $htmlBody,
                            $textBody
                        );
                    }

                    if (!$mailSent) {
                        $demoLink = $resetLink;
                    }
                }

                $success = true;
            }
        }

        $pageTitle = 'Mot de passe oublié';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/auth/forgot-password.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function resetPassword() {
        $errors = [];
        $success = false;
        $token = $_GET['token'] ?? $_POST['token'] ?? '';

        if (empty($token)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Lien de réinitialisation invalide.'];
            redirect('connexion');
        }

        $resetModel = new PasswordReset();
        $reset = $resetModel->findValid($token);

        if (!$reset) {
            $errors[] = 'Ce lien est invalide ou a expiré.';
        }

        if (empty($errors) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfVerify();
            $mdp = $_POST['mot_de_passe'] ?? '';
            $confirm = $_POST['confirmer_mot_de_passe'] ?? '';

            if (strlen($mdp) < 8) {
                $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
            }
            if ($mdp !== $confirm) {
                $errors[] = 'Les mots de passe ne correspondent pas.';
            }

            if (empty($errors)) {
                $userModel = new User();
                $userModel->updatePassword($reset['utilisateur_id'], $mdp);
                $resetModel->markUsed($token);
                logSecurity('password_reset', 'user_id=' . $reset['utilisateur_id']);

                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Mot de passe réinitialisé. Vous pouvez vous connecter.'];
                redirect('connexion');
            }
        }

        $pageTitle = 'Nouveau mot de passe';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/auth/reset-password.php';
        include __DIR__ . '/../views/layout/footer.php';
    }
}
