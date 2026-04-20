<?php
class ContactController {

    public function contact() {
        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = sanitize($_POST['nom']);
            $email = sanitize($_POST['email']);
            $sujet = sanitize($_POST['sujet']);
            $message = sanitize($_POST['message']);

            if (empty($nom)) {
                $errors[] = 'Le nom est requis.';
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'E-mail invalide.';
            }
            if (empty($sujet)) {
                $errors[] = 'Le sujet est requis.';
            }
            if (empty($message)) {
                $errors[] = 'Le message est requis.';
            }

            if (empty($errors)) {
                $contactModel = new ContactMessage();
                $contactModel->create([
                    'nom' => $nom,
                    'email' => $email,
                    'sujet' => $sujet,
                    'message' => $message,
                ]);
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
        $editorialModel = new EditorialContent();
        $contenu = $editorialModel->getByKey('cgu');

        $pageTitle = $contenu ? $contenu['titre'] : 'Conditions Générales d\'Utilisation';
        $iconClass = 'fa-file-contract';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/pages/editorial.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function mentionsLegales() {
        $editorialModel = new EditorialContent();
        $contenu = $editorialModel->getByKey('mentions-legales');

        $pageTitle = $contenu ? $contenu['titre'] : 'Mentions Légales';
        $iconClass = 'fa-gavel';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/pages/editorial.php';
        include __DIR__ . '/../views/layout/footer.php';
    }
}
