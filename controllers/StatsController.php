<?php
class StatsController {

    /**
     * Statistiques détaillées pour une activité (vue par son organisateur).
     */
    public function activity() {
        requireOrganisateur();

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
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

        $registrationModel = new Registration();
        $waitingListModel = new WaitingList();
        $viewModel = new ActivityView();
        $ratingModel = new Rating();

        $nbInscrits = $registrationModel->countByActivity($id);
        $listeAttente = $waitingListModel->getByActivity($id);
        $tauxRemplissage = $activite['nb_max_participants'] > 0
            ? round(($nbInscrits / $activite['nb_max_participants']) * 100)
            : 0;

        $nbVues = $viewModel->countForActivity($id);
        $nbVues30j = $viewModel->countLast30Days($id);
        $tauxConversion = $nbVues > 0 ? round(($nbInscrits / $nbVues) * 100, 1) : 0;
        $serieVues = $viewModel->dailySeries($id, 14);

        $notation = $ratingModel->getActivityRating($id);

        $pageTitle = 'Statistiques – ' . $activite['titre'];
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/stats/activity.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * Tableau de bord global pour un organisateur : toutes ses activités.
     */
    public function organizer() {
        requireOrganisateur();

        $activityModel = new Activity();
        $viewModel = new ActivityView();
        $ratingModel = new Rating();

        $activites = $activityModel->getByOrganisateur($_SESSION['user_id']);

        $statsParActivite = [];
        $totalInscrits = 0;
        $totalPlaces = 0;
        foreach ($activites as $a) {
            $nbInscrits = intval($a['nb_inscrits']);
            $places = intval($a['nb_max_participants']);
            $totalInscrits += $nbInscrits;
            $totalPlaces += $places;
            $statsParActivite[$a['id']] = [
                'activite' => $a,
                'vues' => $viewModel->countForActivity($a['id']),
                'taux' => $places > 0 ? round(($nbInscrits / $places) * 100) : 0,
            ];
        }

        $totalVues = $viewModel->totalForOrganizer($_SESSION['user_id']);
        $tauxMoyen = $totalPlaces > 0 ? round(($totalInscrits / $totalPlaces) * 100) : 0;
        $noteOrga = $ratingModel->getOrganizerRating($_SESSION['user_id']);

        $pageTitle = 'Mes statistiques';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/stats/organizer.php';
        include __DIR__ . '/../views/layout/footer.php';
    }
}
