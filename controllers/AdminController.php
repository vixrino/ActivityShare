<?php
class AdminController {

    public function dashboard() {
        requireAdmin();

        $userModel = new User();
        $activityModel = new Activity();
        $registrationModel = new Registration();
        $contactModel = new ContactMessage();

        $stats = [
            'total_utilisateurs' => $userModel->countAll(),
            'total_organisateurs' => $userModel->countByRole('organisateur'),
            'total_participants' => $userModel->countByRole('participant'),
            'total_activites' => $activityModel->countAllAdmin(),
            'activites_actives' => $activityModel->countActive(),
            'total_inscriptions' => $registrationModel->countAll(),
            'messages_non_lus' => $contactModel->countUnread(),
        ];

        $pageTitle = 'Tableau de bord - Administration';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/admin/dashboard.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function users() {
        requireAdmin();

        $userModel = new User();
        $utilisateurs = $userModel->getAll();

        $pageTitle = 'Gestion des utilisateurs';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/admin/users.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function activities() {
        requireAdmin();

        $activityModel = new Activity();
        $activites = $activityModel->getAllAdmin();

        $pageTitle = 'Gestion des activités';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/admin/activities.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function faq() {
        requireAdmin();

        $faqModel = new Faq();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfVerify();
            $action = $_POST['faq_action'];

            if ($action === 'create') {
                $faqModel->create([
                    'question' => sanitize($_POST['question']),
                    'reponse' => sanitize($_POST['reponse']),
                    'ordre' => intval($_POST['ordre']),
                ]);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Question ajoutée.'];
            }

            if ($action === 'update') {
                $faqId = intval($_POST['faq_id']);
                $faqModel->update($faqId, [
                    'question' => sanitize($_POST['question']),
                    'reponse' => sanitize($_POST['reponse']),
                ]);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Question modifiée.'];
            }

            if ($action === 'delete') {
                $faqId = intval($_POST['faq_id']);
                $faqModel->delete($faqId);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Question supprimée.'];
            }

            redirect('admin-faq');
        }

        $faqs = $faqModel->getAll();

        $pageTitle = 'Gestion de la FAQ';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/admin/faq.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function messages() {
        requireAdmin();

        $contactModel = new ContactMessage();

        if (isset($_GET['mark_read'])) {
            $messageId = intval($_GET['mark_read']);
            $contactModel->markAsRead($messageId);
            redirect('admin-messages');
        }

        $messages = $contactModel->getAll();

        $pageTitle = 'Messages de contact';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/admin/messages.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function toggleUser() {
        requireAdmin();

        $id = 0;
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
        }

        if ($id > 0 && $id != $_SESSION['user_id']) {
            $userModel = new User();
            $userModel->toggleActive($id);
            logSecurity('admin_toggle_user', 'target_id=' . $id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Statut de l\'utilisateur modifié.'];
        }

        redirect('admin-utilisateurs');
    }

    public function deleteUser() {
        requireAdmin();

        $id = 0;
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
        }

        if ($id > 0 && $id != $_SESSION['user_id']) {
            $userModel = new User();
            $userModel->delete($id);
            logSecurity('admin_delete_user', 'target_id=' . $id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Utilisateur supprimé définitivement.'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Action impossible.'];
        }

        redirect('admin-utilisateurs');
    }

    public function editorial() {
        requireAdmin();

        $editorialModel = new EditorialContent();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfVerify();
            $cle = sanitize($_POST['cle']);
            $titre = sanitize($_POST['titre']);
            $contenu = richSanitize($_POST['contenu']);
            $editorialModel->update($cle, $titre, $contenu);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Contenu mis à jour.'];
            redirect('admin-editorial');
        }

        $contenus = $editorialModel->getAll();

        $pageTitle = 'Gestion des pages légales';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/admin/editorial.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function mailbox() {
        requireAdmin();

        $resetModel = new PasswordReset();
        $resets = $resetModel->getRecent(30);

        $pageTitle = 'Boîte mail (démo)';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/admin/mailbox.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function payments() {
        requireAdmin();

        $sql = "SELECT p.*, u.nom, u.prenom, u.email
                FROM paiement p
                JOIN utilisateur u ON p.utilisateur_id = u.id
                ORDER BY p.date_paiement DESC";
        $db = Database::getInstance()->getConnection();
        $paiements = $db->query($sql)->fetchAll();

        $pageTitle = 'Paiements';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/admin/payments.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function deleteActivity() {
        requireAdmin();

        $id = 0;
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
        }

        if ($id > 0) {
            $activityModel = new Activity();
            $activityModel->delete($id);
            logSecurity('admin_delete_activity', 'activite_id=' . $id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Activité supprimée.'];
        }

        redirect('admin-activites');
    }

    // ============================================
    // Journal de sécurité : tentatives de login, actions admin, CSRF...
    // ============================================
    public function security() {
        requireAdmin();

        $logModel = new SecurityLog();
        $action = isset($_GET['action_filter']) ? trim($_GET['action_filter']) : '';
        $logs = $logModel->recent(150, $action ?: null);

        $resume = [
            'login_failed_24h' => $logModel->countByAction('login_failed', 24),
            'login_blocked_24h' => $logModel->countByAction('login_blocked', 24),
            'csrf_failed_24h' => $logModel->countByAction('csrf_failed', 24),
            'admin_actions_24h' => $logModel->countByAction('admin_delete_user', 24)
                + $logModel->countByAction('admin_delete_activity', 24)
                + $logModel->countByAction('admin_toggle_user', 24),
        ];

        $pageTitle = 'Journal de sécurité';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/admin/security.php';
        include __DIR__ . '/../views/layout/footer.php';
    }
}
