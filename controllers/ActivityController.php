<?php
class ActivityController {

    public function index() {
        $activityModel = new Activity();
        $categoryModel = new Category();

        $filters = [];

        if (!empty($_GET['recherche'])) {
            $filters['recherche'] = $_GET['recherche'];
        }
        if (!empty($_GET['categorie'])) {
            $filters['categorie'] = $_GET['categorie'];
        }
        if (!empty($_GET['type'])) {
            $filters['type'] = $_GET['type'];
        }
        if (!empty($_GET['ville'])) {
            $filters['ville'] = $_GET['ville'];
        }
        if (!empty($_GET['date_debut'])) {
            $filters['date_debut'] = $_GET['date_debut'];
        }
        if (!empty($_GET['date_fin'])) {
            $filters['date_fin'] = $_GET['date_fin'];
        }

        $page = 1;
        if (isset($_GET['p']) && intval($_GET['p']) > 0) {
            $page = intval($_GET['p']);
        }

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
        $id = 0;
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
        }

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

        // Tags
        $tagModel = new Tag();
        $tags = $tagModel->getForActivity($id);

        // Statistiques de vues : on enregistre la vue actuelle puis on lit le total
        $viewModel = new ActivityView();
        $userIdForView = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
        $viewModel->record($id, $userIdForView, clientIp());
        $nbVues = $viewModel->countForActivity($id);

        // Notation : note moyenne, avis, éligibilité de l'utilisateur courant
        $ratingModel = new Rating();
        $activityRating = $ratingModel->getActivityRating($id);
        $activityReviews = $ratingModel->getActivityReviews($id, 10);
        $organizerRating = $ratingModel->getOrganizerRating($activite['organisateur_id']);

        $canRateActivity = false;
        $userActivityRating = null;
        $canRateOrganizer = false;
        $userOrganizerRating = null;

        if (isLoggedIn()) {
            $canRateActivity = $ratingModel->canRate($_SESSION['user_id'], $id);
            $userActivityRating = $ratingModel->findActivityRating($_SESSION['user_id'], $id);
            $canRateOrganizer = $canRateActivity && intval($activite['organisateur_id']) !== intval($_SESSION['user_id']);
            $userOrganizerRating = $ratingModel->findOrganizerRating($_SESSION['user_id'], $activite['organisateur_id'], $id);
        }

        // URL absolue de partage (utilisée pour QR code et copier-coller)
        $shareUrl = $this->absoluteUrl('index.php?page=activite&id=' . $id);

        $pageTitle = $activite['titre'];
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/activities/show.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    private function absoluteUrl($path) {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
        $scheme = $https ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $dir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
        return $scheme . '://' . $host . $dir . '/' . ltrim($path, '/');
    }

    public function byTag() {
        $slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
        $tagModel = new Tag();
        $tag = $tagModel->findBySlug($slug);

        if (!$tag) {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Tag introuvable.'];
            redirect('activites');
        }

        $activityModel = new Activity();
        $activites = $activityModel->getByTag($tag['id']);
        $categories = (new Category())->getAll();

        $pageTitle = 'Tag : ' . $tag['nom'];
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/activities/by-tag.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function create() {
        requireOrganisateur();

        $categoryModel = new Category();
        $categories = $categoryModel->getAll();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfVerify();
            $titre = sanitize($_POST['titre']);
            $description = sanitize($_POST['description']);
            $categorie_id = intval($_POST['categorie_id']);
            $date_debut = str_replace('T', ' ', $_POST['date_debut']);
            $date_fin = str_replace('T', ' ', $_POST['date_fin']);
            $lieu = sanitize($_POST['lieu']);
            $adresse = sanitize($_POST['adresse']);
            $nb_max = intval($_POST['nb_max_participants']);
            $type = sanitize($_POST['type']);
            $conditions = sanitize($_POST['conditions_participation']);
            $prix = isset($_POST['prix']) ? floatval(str_replace(',', '.', $_POST['prix'])) : 0;
            if ($prix < 0) { $prix = 0; }
            $tagsInput = inputString($_POST, 'tags', 200);
            $photo = null;

            if (empty($titre)) {
                $errors[] = 'Le titre est requis.';
            }
            if (empty($description)) {
                $errors[] = 'La description est requise.';
            }
            if ($categorie_id <= 0) {
                $errors[] = 'La catégorie est requise.';
            }
            if (empty($date_debut)) {
                $errors[] = 'La date de début est requise.';
            }
            if (empty($date_fin)) {
                $errors[] = 'La date de fin est requise.';
            }
            if (empty($lieu)) {
                $errors[] = 'Le lieu est requis.';
            }
            if ($nb_max <= 0) {
                $errors[] = 'Le nombre de participants doit être supérieur à 0.';
            }

            if (!empty($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $photoPath = uploadImage($_FILES['photo'], 'activities');
                if ($photoPath) {
                    $photo = $photoPath;
                }
            }

            if (empty($errors)) {
                $activityModel = new Activity();
                $newId = $activityModel->create([
                    'organisateur_id' => $_SESSION['user_id'],
                    'titre' => $titre,
                    'description' => $description,
                    'categorie_id' => $categorie_id,
                    'date_debut' => $date_debut,
                    'date_fin' => $date_fin,
                    'lieu' => $lieu,
                    'adresse' => $adresse,
                    'nb_max_participants' => $nb_max,
                    'type' => $type,
                    'conditions_participation' => $conditions,
                    'prix' => $prix,
                    'photo' => $photo,
                ]);

                if ($newId && $tagsInput !== '') {
                    (new Tag())->syncForActivity($newId, $tagsInput);
                }

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

        $id = 0;
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
        }

        $activityModel = new Activity();
        $activite = $activityModel->find($id);

        if (!$activite) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Activité introuvable.'];
            redirect('mes-activites');
        }

        if ($activite['organisateur_id'] != $_SESSION['user_id'] && !isAdmin()) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Accès non autorisé.'];
            redirect('mes-activites');
        }

        $categoryModel = new Category();
        $categories = $categoryModel->getAll();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfVerify();
            $data = [
                'titre' => sanitize($_POST['titre']),
                'description' => sanitize($_POST['description']),
                'categorie_id' => intval($_POST['categorie_id']),
                'date_debut' => str_replace('T', ' ', $_POST['date_debut']),
                'date_fin' => str_replace('T', ' ', $_POST['date_fin']),
                'lieu' => sanitize($_POST['lieu']),
                'adresse' => sanitize($_POST['adresse']),
                'nb_max_participants' => intval($_POST['nb_max_participants']),
                'type' => sanitize($_POST['type']),
                'conditions_participation' => sanitize($_POST['conditions_participation']),
                'prix' => isset($_POST['prix']) ? max(0, floatval(str_replace(',', '.', $_POST['prix']))) : 0,
            ];

            if (!empty($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $photoPath = uploadImage($_FILES['photo'], 'activities');
                if ($photoPath) {
                    $data['photo'] = $photoPath;
                }
            }

            if (empty($errors)) {
                $activityModel->update($id, $data);
                $tagsInput = inputString($_POST, 'tags', 200);
                (new Tag())->syncForActivity($id, $tagsInput);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Activité modifiée avec succès !'];
                redirect('activite', ['id' => $id]);
            }
        }

        $tagModel = new Tag();
        $tagsExistants = $tagModel->getForActivity($id);
        $tagsCSV = implode(', ', array_map(function ($t) { return $t['nom']; }, $tagsExistants));

        $pageTitle = 'Modifier l\'activité';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/activities/edit.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function delete() {
        requireOrganisateur();

        $id = 0;
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
        }

        $activityModel = new Activity();
        $activite = $activityModel->find($id);

        if (!$activite) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Activité introuvable.'];
            redirect('mes-activites');
        }

        if ($activite['organisateur_id'] != $_SESSION['user_id'] && !isAdmin()) {
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

        $id = 0;
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
        }

        $activityModel = new Activity();
        $activite = $activityModel->find($id);

        if (!$activite) {
            redirect('activites');
        }

        $registrationModel = new Registration();
        $waitingListModel = new WaitingList();
        $notificationModel = new Notification();

        $dejaInscrit = $registrationModel->isRegistered($id, $_SESSION['user_id']);
        if ($dejaInscrit) {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Vous êtes déjà inscrit à cette activité.'];
            redirect('activite', ['id' => $id]);
        }

        if (floatval($activite['prix']) > 0) {
            $cartModel = new Cart();
            if (!$cartModel->exists($_SESSION['user_id'], $id)) {
                $cartModel->add($_SESSION['user_id'], $id, 1);
            }
            $_SESSION['flash'] = ['type' => 'info', 'message' => 'Activité payante : finalisez votre paiement pour valider l\'inscription.'];
            redirect('panier');
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
            $dejaSurListe = $waitingListModel->isOnWaitingList($id, $_SESSION['user_id']);

            if (!$dejaSurListe) {
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

        $id = 0;
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
        }

        $registrationModel = new Registration();
        $waitingListModel = new WaitingList();

        $estInscrit = $registrationModel->isRegistered($id, $_SESSION['user_id']);

        if ($estInscrit) {
            $registrationModel->cancel($id, $_SESSION['user_id']);
            $waitingListModel->promoteFirst($id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Désinscription effectuée.'];
        } else {
            $surListeAttente = $waitingListModel->isOnWaitingList($id, $_SESSION['user_id']);

            if ($surListeAttente) {
                $waitingListModel->remove($id, $_SESSION['user_id']);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Vous avez été retiré de la liste d\'attente.'];
            }
        }

        redirect('activite', ['id' => $id]);
    }

    public function search() {
        $this->index();
    }
}
