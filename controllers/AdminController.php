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
            $action = $_POST['faq_action'] ?? '';

            if ($action === 'create') {
                $faqModel->create([
                    'question' => sanitize($_POST['question'] ?? ''),
                    'reponse' => sanitize($_POST['reponse'] ?? ''),
                    'ordre' => intval($_POST['ordre'] ?? 0),
                ]);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Question ajoutée.'];
            } elseif ($action === 'update') {
                $faqModel->update(intval($_POST['faq_id']), [
                    'question' => sanitize($_POST['question'] ?? ''),
                    'reponse' => sanitize($_POST['reponse'] ?? ''),
                ]);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Question modifiée.'];
            } elseif ($action === 'delete') {
                $faqModel->delete(intval($_POST['faq_id']));
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
            $contactModel->markAsRead(intval($_GET['mark_read']));
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

        $id = intval($_GET['id'] ?? 0);
        if ($id && $id != $_SESSION['user_id']) {
            $userModel = new User();
            $userModel->toggleActive($id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Statut de l\'utilisateur modifié.'];
        }

        redirect('admin-utilisateurs');
    }

    public function deleteActivity() {
        requireAdmin();

        $id = intval($_GET['id'] ?? 0);
        if ($id) {
            $activityModel = new Activity();
            $activityModel->delete($id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Activité supprimée.'];
        }

        redirect('admin-activites');
    }
}
