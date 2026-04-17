<?php
class CartController {

    public function index() {
        requireLogin();

        $cartModel = new Cart();
        $items = $cartModel->getByUser($_SESSION['user_id']);
        $total = $cartModel->total($_SESSION['user_id']);

        $pageTitle = 'Mon panier';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/cart/index.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function add() {
        requireLogin();

        $activityId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($activityId <= 0) {
            redirect('activites');
        }

        $activityModel = new Activity();
        $activite = $activityModel->find($activityId);
        if (!$activite) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Activité introuvable.'];
            redirect('activites');
        }

        if ($activite['prix'] <= 0) {
            $_SESSION['flash'] = ['type' => 'info', 'message' => 'Cette activité est gratuite. Inscrivez-vous directement.'];
            redirect('activite', ['id' => $activityId]);
        }

        $registrationModel = new Registration();
        if ($registrationModel->isRegistered($activityId, $_SESSION['user_id'])) {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Vous êtes déjà inscrit à cette activité.'];
            redirect('activite', ['id' => $activityId]);
        }

        $cartModel = new Cart();
        $cartModel->add($_SESSION['user_id'], $activityId, 1);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Activité ajoutée au panier.'];
        redirect('panier');
    }

    public function remove() {
        requireLogin();

        $activityId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($activityId > 0) {
            $cartModel = new Cart();
            $cartModel->remove($_SESSION['user_id'], $activityId);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Article retiré du panier.'];
        }

        redirect('panier');
    }

    public function update() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cartModel = new Cart();
            foreach ($_POST['quantites'] ?? [] as $activityId => $quantite) {
                $cartModel->updateQuantity($_SESSION['user_id'], intval($activityId), intval($quantite));
            }
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Panier mis à jour.'];
        }

        redirect('panier');
    }
}
