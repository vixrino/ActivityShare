<?php
class ActivityController {
    public function index() {
        $activityModel = new Activity();
        $categoryModel = new Category();

        $filters = [];
        if (!empty($_GET['recherche'])) $filters['recherche'] = $_GET['recherche'];
        if (!empty($_GET['categorie'])) $filters['categorie'] = $_GET['categorie'];
        if (!empty($_GET['type'])) $filters['type'] = $_GET['type'];
        if (!empty($_GET['ville'])) $filters['ville'] = $_GET['ville'];
        if (!empty($_GET['date_debut'])) $filters['date_debut'] = $_GET['date_debut'];
        if (!empty($_GET['date_fin'])) $filters['date_fin'] = $_GET['date_fin'];

        $page = max(1, intval($_GET['p'] ?? 1));
        $perPage = 9;
        $offset = ($page - 1) * $perPage;

        $activites = $activityModel->getAll($perPage, $offset, $filters);
        $total = $activityModel->countAll($filters);
        $totalPages = ceil($total / $perPage);
        $categories = $categoryModel->getAll();

        $pageTitle = 'Activités';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/activities/index.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function show() {
        $id = intval($_GET['id'] ?? 0);
        $activityModel = new Activity();
        $activite = $activityModel->find($id);

        if (!$activite) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Activité introuvable.'];
            redirect('activites');
        }

        $registrationModel = new Registration();
        $waitingListModel = new WaitingList();

        $nbInscrits = $registrationModel->countByActivity($id);
        $placesRestantes = $activite['nb_max_participants'] - $nbInscrits;
        $inscrits = $registrationModel->getByActivity($id);
        $listeAttente = $waitingListModel->getByActivity($id);

        $isRegistered = false;
        $isOnWaitingList = false;
        $waitingPosition = null;

        if (isLoggedIn()) {
            $isRegistered = $registrationModel->isRegistered($id, $_SESSION['user_id']);
            $isOnWaitingList = $waitingListModel->isOnWaitingList($id, $_SESSION['user_id']);
            if ($isOnWaitingList) {
                $waitingPosition = $waitingListModel->getPosition($id, $_SESSION['user_id']);
            }
        }

        $pageTitle = $activite['titre'];
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/activities/show.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function create() {
        requireOrganisateur();

        $categoryModel = new Category();
        $categories = $categoryModel->getAll();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'organisateur_id' => $_SESSION['user_id'],
                'titre' => sanitize($_POST['titre'] ?? ''),
                'description' => sanitize($_POST['description'] ?? ''),
                'categorie_id' => intval($_POST['categorie_id'] ?? 0),
                'date_debut' => $_POST['date_debut'] ?? '',
                'date_fin' => $_POST['date_fin'] ?? '',
                'lieu' => sanitize($_POST['lieu'] ?? ''),
                'adresse' => sanitize($_POST['adresse'] ?? ''),
                'nb_max_participants' => intval($_POST['nb_max_participants'] ?? 0),
                'type' => sanitize($_POST['type'] ?? 'public'),
                'conditions_participation' => sanitize($_POST['conditions_participation'] ?? ''),
            ];

            if (empty($data['titre'])) $errors[] = 'Le titre est requis.';
            if (empty($data['description'])) $errors[] = 'La description est requise.';
            if ($data['categorie_id'] <= 0) $errors[] = 'La catégorie est requise.';
            if (empty($data['date_debut'])) $errors[] = 'La date de début est requise.';
            if (empty($data['date_fin'])) $errors[] = 'La date de fin est requise.';
            if (empty($data['lieu'])) $errors[] = 'Le lieu est requis.';
            if ($data['nb_max_participants'] <= 0) $errors[] = 'Le nombre de participants doit être supérieur à 0.';

            if (!empty($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $photoPath = uploadImage($_FILES['photo'], 'activities');
                if ($photoPath) {
                    $data['photo'] = $photoPath;
                }
            }

            if (empty($errors)) {
                $activityModel = new Activity();
                $activityModel->create($data);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Activité créée avec succès !'];
                redirect('mes-activites');
            }
        }

        $pageTitle = 'Créer une activité';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/activities/create.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function edit() {
        requireOrganisateur();

        $id = intval($_GET['id'] ?? 0);
        $activityModel = new Activity();
        $activite = $activityModel->find($id);

        if (!$activite || ($activite['organisateur_id'] != $_SESSION['user_id'] && !isAdmin())) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Accès non autorisé.'];
            redirect('mes-activites');
        }

        $categoryModel = new Category();
        $categories = $categoryModel->getAll();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'titre' => sanitize($_POST['titre'] ?? ''),
                'description' => sanitize($_POST['description'] ?? ''),
                'categorie_id' => intval($_POST['categorie_id'] ?? 0),
                'date_debut' => $_POST['date_debut'] ?? '',
                'date_fin' => $_POST['date_fin'] ?? '',
                'lieu' => sanitize($_POST['lieu'] ?? ''),
                'adresse' => sanitize($_POST['adresse'] ?? ''),
                'nb_max_participants' => intval($_POST['nb_max_participants'] ?? 0),
                'type' => sanitize($_POST['type'] ?? 'public'),
                'conditions_participation' => sanitize($_POST['conditions_participation'] ?? ''),
            ];

            if (!empty($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $photoPath = uploadImage($_FILES['photo'], 'activities');
                if ($photoPath) {
                    $data['photo'] = $photoPath;
                }
            }

            if (empty($errors)) {
                $activityModel->update($id, $data);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Activité modifiée avec succès !'];
                redirect('activite', ['id' => $id]);
            }
        }

        $pageTitle = 'Modifier l\'activité';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/activities/edit.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function delete() {
        requireOrganisateur();

        $id = intval($_GET['id'] ?? 0);
        $activityModel = new Activity();
        $activite = $activityModel->find($id);

        if (!$activite || ($activite['organisateur_id'] != $_SESSION['user_id'] && !isAdmin())) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Accès non autorisé.'];
            redirect('mes-activites');
        }

        $activityModel->delete($id);

        $registrationModel = new Registration();
        $inscrits = $registrationModel->getByActivity($id);
        $notificationModel = new Notification();
        foreach ($inscrits as $inscrit) {
            $notificationModel->create([
                'utilisateur_id' => $inscrit['participant_id'],
                'type' => 'annulation',
                'titre' => 'Activité annulée',
                'message' => 'L\'activité "' . $activite['titre'] . '" a été annulée par l\'organisateur.',
            ]);
        }

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Activité supprimée avec succès.'];
        redirect('mes-activites');
    }

    public function register() {
        requireLogin();

        $id = intval($_GET['id'] ?? 0);
        $activityModel = new Activity();
        $activite = $activityModel->find($id);

        if (!$activite) {
            redirect('activites');
        }

        $registrationModel = new Registration();
        $waitingListModel = new WaitingList();
        $notificationModel = new Notification();

        if ($registrationModel->isRegistered($id, $_SESSION['user_id'])) {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Vous êtes déjà inscrit à cette activité.'];
            redirect('activite', ['id' => $id]);
        }

        $nbInscrits = $registrationModel->countByActivity($id);
        if ($nbInscrits < $activite['nb_max_participants']) {
            $registrationModel->create($id, $_SESSION['user_id']);
            $notificationModel->create([
                'utilisateur_id' => $_SESSION['user_id'],
                'type' => 'confirmation_inscription',
                'titre' => 'Inscription confirmée',
                'message' => 'Vous êtes inscrit à l\'activité "' . $activite['titre'] . '".',
            ]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Inscription réussie !'];
        } else {
            if (!$waitingListModel->isOnWaitingList($id, $_SESSION['user_id'])) {
                $waitingListModel->add($id, $_SESSION['user_id']);
                $_SESSION['flash'] = ['type' => 'info', 'message' => 'L\'activité est complète. Vous avez été ajouté à la liste d\'attente.'];
            } else {
                $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Vous êtes déjà sur la liste d\'attente.'];
            }
        }

        redirect('activite', ['id' => $id]);
    }

    public function unregister() {
        requireLogin();

        $id = intval($_GET['id'] ?? 0);
        $registrationModel = new Registration();
        $waitingListModel = new WaitingList();

        if ($registrationModel->isRegistered($id, $_SESSION['user_id'])) {
            $registrationModel->cancel($id, $_SESSION['user_id']);
            $waitingListModel->promoteFirst($id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Désinscription effectuée.'];
        } elseif ($waitingListModel->isOnWaitingList($id, $_SESSION['user_id'])) {
            $waitingListModel->remove($id, $_SESSION['user_id']);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Vous avez été retiré de la liste d\'attente.'];
        }

        redirect('activite', ['id' => $id]);
    }

    public function search() {
        $this->index();
    }
}
