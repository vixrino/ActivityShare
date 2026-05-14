<?php
class ProfileController {

    public function show() {
        requireLogin();

        $userModel = new User();
        $user = $userModel->find($_SESSION['user_id']);

        $notificationModel = new Notification();
        $notifications = $notificationModel->getByUser($_SESSION['user_id']);
        $unreadCount = $notificationModel->countUnread($_SESSION['user_id']);

        if (isset($_GET['mark_read'])) {
            $notificationModel->markAllAsRead($_SESSION['user_id']);
            redirect('profil');
        }

        $followModel = new Follow();
        $ratingModel = new Rating();
        $nbFollowers = $followModel->countFollowers($_SESSION['user_id']);
        $nbFollowing = $followModel->countFollowing($_SESSION['user_id']);
        $activitesAEvaluer = $ratingModel->pendingForUser($_SESSION['user_id']);
        $noteMoyenneOrga = $ratingModel->getOrganizerRating($_SESSION['user_id']);

        $pageTitle = 'Mon Profil';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/profile/show.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function edit() {
        requireLogin();

        $userModel = new User();
        $user = $userModel->find($_SESSION['user_id']);
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfVerify();
            $nom = sanitize($_POST['nom']);
            $prenom = sanitize($_POST['prenom']);
            $telephone = sanitize($_POST['telephone']);
            $ville = sanitize($_POST['ville']);
            $bio = sanitize($_POST['bio']);

            $data = [
                'nom' => $nom,
                'prenom' => $prenom,
                'telephone' => $telephone,
                'ville' => $ville,
                'bio' => $bio,
            ];

            if (empty($nom) || empty($prenom)) {
                $errors[] = 'Le nom et le prénom sont requis.';
            }

            if (!empty($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] === UPLOAD_ERR_OK) {
                $photoPath = uploadImage($_FILES['photo_profil'], 'profiles');
                if ($photoPath) {
                    $data['photo_profil'] = $photoPath;
                }
            }

            if (!empty($_POST['nouveau_mot_de_passe'])) {
                $nouveauMdp = $_POST['nouveau_mot_de_passe'];
                $confirmerMdp = $_POST['confirmer_mot_de_passe'];

                if (strlen($nouveauMdp) < 8) {
                    $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
                } else if ($nouveauMdp !== $confirmerMdp) {
                    $errors[] = 'Les mots de passe ne correspondent pas.';
                } else {
                    $userModel->updatePassword($_SESSION['user_id'], $nouveauMdp);
                    logSecurity('password_changed', 'user_id=' . $_SESSION['user_id']);
                }
            }

            if (empty($errors)) {
                $userModel->update($_SESSION['user_id'], $data);

                $_SESSION['user_nom'] = $data['nom'];
                $_SESSION['user_prenom'] = $data['prenom'];

                if (isset($data['photo_profil'])) {
                    $_SESSION['user_photo'] = $data['photo_profil'];
                }

                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Profil mis à jour avec succès.'];
                redirect('profil');
            }
        }

        $pageTitle = 'Modifier mon profil';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/profile/edit.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function myActivities() {
        requireOrganisateur();

        $activityModel = new Activity();
        $activites = $activityModel->getByOrganisateur($_SESSION['user_id']);

        $pageTitle = 'Mes activités';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/profile/my-activities.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function myRegistrations() {
        requireLogin();

        $registrationModel = new Registration();
        $inscriptions = $registrationModel->getByUser($_SESSION['user_id']);

        $pageTitle = 'Mes inscriptions';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/profile/my-registrations.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    // ============================================
    // Profils publics, annuaire et abonnements
    // ============================================

    public function directory() {
        $userModel = new User();
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';
        $role = isset($_GET['role']) ? trim($_GET['role']) : '';
        $members = $userModel->listPublic($search, $role, 60);

        $followingMap = [];
        if (isLoggedIn()) {
            $following = (new Follow())->getFollowing($_SESSION['user_id']);
            foreach ($following as $f) {
                $followingMap[intval($f['id'])] = true;
            }
        }

        $pageTitle = 'Annuaire des membres';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/profile/directory.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function publicProfile() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $userModel = new User();
        $user = $userModel->find($id);

        if (!$user || !$user['actif']) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Profil introuvable.'];
            redirect('membres');
        }

        // Si l'utilisateur consulte son propre profil → renvoie vers la version privée
        if (isLoggedIn() && intval($_SESSION['user_id']) === intval($user['id'])) {
            redirect('profil');
        }

        $followModel = new Follow();
        $ratingModel = new Rating();
        $activityModel = new Activity();

        $profile = $user;
        $nbFollowers = $followModel->countFollowers($user['id']);
        $nbFollowing = $followModel->countFollowing($user['id']);
        $isFollowing = isLoggedIn() ? $followModel->isFollowing($_SESSION['user_id'], $user['id']) : false;

        $activites = ($user['role'] === 'organisateur' || $user['role'] === 'administrateur')
            ? $activityModel->getByOrganisateur($user['id'])
            : [];

        $noteOrganisateur = ($user['role'] === 'organisateur' || $user['role'] === 'administrateur')
            ? $ratingModel->getOrganizerRating($user['id'])
            : ['total' => 0, 'moyenne' => 0];
        $avisOrganisateur = ($user['role'] === 'organisateur' || $user['role'] === 'administrateur')
            ? $ratingModel->getOrganizerReviews($user['id'], 6)
            : [];

        $pageTitle = sanitize($user['prenom'] . ' ' . $user['nom']);
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/profile/public.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function follow() {
        requireLogin();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id <= 0 || $id === intval($_SESSION['user_id'])) {
            redirect('membres');
        }

        $userModel = new User();
        $cible = $userModel->find($id);
        if (!$cible) {
            redirect('membres');
        }

        $followModel = new Follow();
        $followModel->follow($_SESSION['user_id'], $id);

        $notif = new Notification();
        $notif->create([
            'utilisateur_id' => $id,
            'type' => 'abonnement',
            'titre' => 'Nouvel abonné',
            'message' => sanitize($_SESSION['user_prenom']) . ' suit désormais votre profil.',
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Vous suivez désormais ' . sanitize($cible['prenom']) . '.'];
        redirect('utilisateur', ['id' => $id]);
    }

    public function unfollow() {
        requireLogin();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id <= 0) redirect('membres');

        $followModel = new Follow();
        $followModel->unfollow($_SESSION['user_id'], $id);
        $_SESSION['flash'] = ['type' => 'info', 'message' => 'Vous ne suivez plus cet utilisateur.'];
        redirect('utilisateur', ['id' => $id]);
    }

    public function followers() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $userModel = new User();
        $profile = $userModel->find($id);
        if (!$profile) redirect('membres');

        $users = (new Follow())->getFollowers($id);
        $mode = 'followers';

        $pageTitle = 'Abonnés de ' . sanitize($profile['prenom'] . ' ' . $profile['nom']);
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/profile/follow-list.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function following() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $userModel = new User();
        $profile = $userModel->find($id);
        if (!$profile) redirect('membres');

        $users = (new Follow())->getFollowing($id);
        $mode = 'following';

        $pageTitle = 'Abonnements de ' . sanitize($profile['prenom'] . ' ' . $profile['nom']);
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/profile/follow-list.php';
        include __DIR__ . '/../views/layout/footer.php';
    }
}
