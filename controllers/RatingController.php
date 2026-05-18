<?php
class RatingController {

    public function rateActivity() {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('home');
        }
        csrfVerify();

        $activiteId = intval($_POST['activite_id'] ?? 0);
        $note = intval($_POST['note'] ?? 0);
        $commentaire = trim($_POST['commentaire'] ?? '');

        if ($activiteId <= 0 || $note < 1 || $note > 5) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Note invalide.'];
            redirect('activite', ['id' => $activiteId]);
        }

        $ratingModel = new Rating();
        if (!$ratingModel->canRate($_SESSION['user_id'], $activiteId)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Vous ne pouvez pas noter cette activité.'];
            redirect('activite', ['id' => $activiteId]);
        }

        $ratingModel->rateActivity($_SESSION['user_id'], $activiteId, $note, $commentaire);

        $activityModel = new Activity();
        $activite = $activityModel->find($activiteId);
        if ($activite) {
            $notif = new Notification();
            $notif->create([
                'utilisateur_id' => $activite['organisateur_id'],
                'type' => 'notation',
                'titre' => 'Nouvel avis sur votre activité',
                'message' => $_SESSION['user_prenom'] . ' a évalué « ' . $activite['titre'] . ' » : ' . $note . '/5.',
            ]);
        }

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Merci ! Votre avis sur l\'activité a été enregistré.'];
        redirect('activite', ['id' => $activiteId]);
    }

    public function rateOrganizer() {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('home');
        }
        csrfVerify();

        $activiteId = intval($_POST['activite_id'] ?? 0);
        $organisateurId = intval($_POST['organisateur_id'] ?? 0);
        $note = intval($_POST['note'] ?? 0);
        $commentaire = trim($_POST['commentaire'] ?? '');

        if ($activiteId <= 0 || $organisateurId <= 0 || $note < 1 || $note > 5) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Note invalide.'];
            redirect('activite', ['id' => $activiteId]);
        }

        $ratingModel = new Rating();
        if (!$ratingModel->canRate($_SESSION['user_id'], $activiteId)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Vous ne pouvez pas noter cet organisateur.'];
            redirect('activite', ['id' => $activiteId]);
        }

        $ok = $ratingModel->rateOrganizer($_SESSION['user_id'], $organisateurId, $activiteId, $note, $commentaire);

        if ($ok) {
            $notif = new Notification();
            $notif->create([
                'utilisateur_id' => $organisateurId,
                'type' => 'notation',
                'titre' => 'Nouvel avis sur votre profil organisateur',
                'message' => $_SESSION['user_prenom'] . ' vous a noté ' . $note . '/5.',
            ]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Merci ! Votre avis sur l\'organisateur a été enregistré.'];
        }

        redirect('activite', ['id' => $activiteId]);
    }
}
