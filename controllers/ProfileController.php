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
            $data = [
                'nom' => sanitize($_POST['nom'] ?? ''),
                'prenom' => sanitize($_POST['prenom'] ?? ''),
                'telephone' => sanitize($_POST['telephone'] ?? ''),
                'ville' => sanitize($_POST['ville'] ?? ''),
                'bio' => sanitize($_POST['bio'] ?? ''),
            ];

            if (empty($data['nom']) || empty($data['prenom'])) {
                $errors[] = 'Le nom et le prénom sont requis.';
            }

            if (!empty($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] === UPLOAD_ERR_OK) {
                $photoPath = uploadImage($_FILES['photo_profil'], 'profiles');
                if ($photoPath) {
                    $data['photo_profil'] = $photoPath;
                }
            }

            if (!empty($_POST['nouveau_mot_de_passe'])) {
                if (strlen($_POST['nouveau_mot_de_passe']) < 8) {
                    $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
                } elseif ($_POST['nouveau_mot_de_passe'] !== ($_POST['confirmer_mot_de_passe'] ?? '')) {
                    $errors[] = 'Les mots de passe ne correspondent pas.';
                } else {
                    $userModel->updatePassword($_SESSION['user_id'], $_POST['nouveau_mot_de_passe']);
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
}
