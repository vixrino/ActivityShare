<?php
class MessagingController {

    public function index() {
        requireLogin();

        $messageModel = new PrivateMessage();
        $conversations = $messageModel->getConversations($_SESSION['user_id']);

        $pageTitle = 'Messagerie';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/messaging/index.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function conversation() {
        requireLogin();

        $otherId = isset($_GET['user']) ? intval($_GET['user']) : 0;
        if ($otherId <= 0 || $otherId == $_SESSION['user_id']) {
            redirect('messagerie');
        }

        $userModel = new User();
        $autre = $userModel->find($otherId);
        if (!$autre) {
            redirect('messagerie');
        }

        $messageModel = new PrivateMessage();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['contenu'])) {
            $contenu = trim($_POST['contenu']);
            if (strlen($contenu) > 0 && strlen($contenu) <= 2000) {
                $messageModel->send($_SESSION['user_id'], $otherId, $contenu);

                $notificationModel = new Notification();
                $notificationModel->create([
                    'utilisateur_id' => $otherId,
                    'type' => 'message',
                    'titre' => 'Nouveau message',
                    'message' => 'Vous avez reçu un message de ' . $_SESSION['user_prenom'] . '.',
                ]);
            }
            redirect('conversation', ['user' => $otherId]);
        }

        $messageModel->markRead($otherId, $_SESSION['user_id']);
        $messages = $messageModel->getConversation($_SESSION['user_id'], $otherId);
        $conversations = $messageModel->getConversations($_SESSION['user_id']);

        $pageTitle = 'Conversation avec ' . $autre['prenom'];
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/messaging/conversation.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function newConversation() {
        requireLogin();

        $userModel = new User();
        $resultats = [];
        $recherche = '';

        if (isset($_GET['q'])) {
            $recherche = trim($_GET['q']);
            if (strlen($recherche) >= 2) {
                $resultats = $userModel->search($recherche);
            }
        }

        $pageTitle = 'Nouveau message';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/messaging/new.php';
        include __DIR__ . '/../views/layout/footer.php';
    }
}
