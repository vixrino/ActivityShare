<?php
class ContactController {
    public function contact() {
        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => sanitize($_POST['nom'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'sujet' => sanitize($_POST['sujet'] ?? ''),
                'message' => sanitize($_POST['message'] ?? ''),
            ];

            if (empty($data['nom'])) $errors[] = 'Le nom est requis.';
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'E-mail invalide.';
            if (empty($data['sujet'])) $errors[] = 'Le sujet est requis.';
            if (empty($data['message'])) $errors[] = 'Le message est requis.';

            if (empty($errors)) {
                $contactModel = new ContactMessage();
                $contactModel->create($data);
                $success = true;
            }
        }

        $pageTitle = 'Contact';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/pages/contact.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function faq() {
        $faqModel = new Faq();
        $faqs = $faqModel->getAll();

        $pageTitle = 'Foire aux questions';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/pages/faq.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function cgu() {
        $pageTitle = 'Conditions Générales d\'Utilisation';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/pages/cgu.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function mentionsLegales() {
        $pageTitle = 'Mentions Légales';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/pages/mentions-legales.php';
        include __DIR__ . '/../views/layout/footer.php';
    }
}
