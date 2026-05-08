<?php
class ForumController {

    public function index() {
        $forumModel = new Forum();
        $categories = $forumModel->getCategories();

        $pageTitle = 'Forum de la communauté';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/forum/index.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function category() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $forumModel = new Forum();
        $categorie = $forumModel->findCategory($id);

        if (!$categorie) {
            redirect('forum');
        }

        $topics = $forumModel->getTopics($id);

        $pageTitle = $categorie['nom'];
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/forum/category.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function topic() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $forumModel = new Forum();
        $topic = $forumModel->findTopic($id);

        if (!$topic) {
            redirect('forum');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
            csrfVerify();
            if ($topic['ferme']) {
                $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Ce sujet est fermé.'];
            } else {
                $contenu = trim($_POST['contenu'] ?? '');
                if (strlen($contenu) > 0 && strlen($contenu) <= 5000) {
                    $forumModel->postMessage($id, $_SESSION['user_id'], $contenu);
                }
            }
            redirect('forum-topic', ['id' => $id]);
        }

        $forumModel->incrementViews($id);
        $messages = $forumModel->getMessages($id);

        $pageTitle = $topic['titre'];
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/forum/topic.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function createTopic() {
        requireLogin();

        $forumModel = new Forum();
        $categories = $forumModel->getCategories();
        $errors = [];
        $categorieId = isset($_GET['categorie']) ? intval($_GET['categorie']) : 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfVerify();
            $categorieId = intval($_POST['forum_categorie_id']);
            $titre = sanitize($_POST['titre']);
            $contenu = trim($_POST['contenu']);

            if (empty($titre)) {
                $errors[] = 'Le titre est requis.';
            }
            if (empty($contenu)) {
                $errors[] = 'Le message est requis.';
            }
            if ($categorieId <= 0) {
                $errors[] = 'Choisissez une catégorie.';
            }

            if (empty($errors)) {
                $topicId = $forumModel->createTopic([
                    'forum_categorie_id' => $categorieId,
                    'utilisateur_id' => $_SESSION['user_id'],
                    'titre' => $titre,
                    'contenu' => $contenu,
                ]);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Sujet publié !'];
                redirect('forum-topic', ['id' => $topicId]);
            }
        }

        $pageTitle = 'Nouveau sujet';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/forum/create.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function deleteTopic() {
        requireAdmin();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            $forumModel = new Forum();
            $forumModel->deleteTopic($id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Sujet supprimé.'];
        }
        redirect('forum');
    }

    public function togglePin() {
        requireAdmin();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            $forumModel = new Forum();
            $forumModel->togglePin($id);
        }
        redirect('forum-topic', ['id' => $id]);
    }
}
