<?php
class ChatController {

    public function activityChat() {
        requireLogin();

        $activityId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($activityId <= 0) {
            redirect('activites');
        }

        $activityModel = new Activity();
        $activite = $activityModel->find($activityId);
        if (!$activite) {
            redirect('activites');
        }

        $registrationModel = new Registration();
        $isOrganisateur = ($activite['organisateur_id'] == $_SESSION['user_id']);
        $isParticipant = $registrationModel->isRegistered($activityId, $_SESSION['user_id']);

        if (!$isOrganisateur && !$isParticipant && !isAdmin()) {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Le chat est réservé aux participants de cette activité.'];
            redirect('activite', ['id' => $activityId]);
        }

        $chatModel = new ActivityChat();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['contenu'])) {
            csrfVerify();
            $contenu = trim($_POST['contenu']);
            if (strlen($contenu) > 0 && strlen($contenu) <= 2000) {
                $chatModel->send($activityId, $_SESSION['user_id'], $contenu);
            }
            redirect('chat-activite', ['id' => $activityId]);
        }

        $messages = $chatModel->getByActivity($activityId);
        $inscrits = $registrationModel->getByActivity($activityId);

        $pageTitle = 'Chat - ' . $activite['titre'];
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/chat/activity.php';
        include __DIR__ . '/../views/layout/footer.php';
    }
}
