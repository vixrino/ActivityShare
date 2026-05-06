<?php

// ============================================
// Sécurité : sessions, cookies, headers HTTP
// ============================================
require_once __DIR__ . '/helpers/security.php';
secureSessionStart();
sendSecurityHeaders();

date_default_timezone_set('Europe/Paris');

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/functions.php';

require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Activity.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Registration.php';
require_once __DIR__ . '/models/WaitingList.php';
require_once __DIR__ . '/models/Notification.php';
require_once __DIR__ . '/models/Faq.php';
require_once __DIR__ . '/models/ContactMessage.php';
require_once __DIR__ . '/models/Cart.php';
require_once __DIR__ . '/models/Payment.php';
require_once __DIR__ . '/models/PasswordReset.php';
require_once __DIR__ . '/models/EditorialContent.php';
require_once __DIR__ . '/models/PrivateMessage.php';
require_once __DIR__ . '/models/ActivityChat.php';
require_once __DIR__ . '/models/Forum.php';
require_once __DIR__ . '/models/Follow.php';
require_once __DIR__ . '/models/Rating.php';
require_once __DIR__ . '/models/Tag.php';
require_once __DIR__ . '/models/ActivityView.php';
require_once __DIR__ . '/models/LoginAttempt.php';
require_once __DIR__ . '/models/SecurityLog.php';

require_once __DIR__ . '/controllers/HomeController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/ActivityController.php';
require_once __DIR__ . '/controllers/ProfileController.php';
require_once __DIR__ . '/controllers/AdminController.php';
require_once __DIR__ . '/controllers/ContactController.php';
require_once __DIR__ . '/controllers/CartController.php';
require_once __DIR__ . '/controllers/CheckoutController.php';
require_once __DIR__ . '/controllers/MessagingController.php';
require_once __DIR__ . '/controllers/ChatController.php';
require_once __DIR__ . '/controllers/ForumController.php';
require_once __DIR__ . '/controllers/RatingController.php';
require_once __DIR__ . '/controllers/IcsController.php';
require_once __DIR__ . '/controllers/StatsController.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

$routes = [
    'home' => ['HomeController', 'index'],

    'activites' => ['ActivityController', 'index'],
    'activite' => ['ActivityController', 'show'],
    'creer-activite' => ['ActivityController', 'create'],
    'modifier-activite' => ['ActivityController', 'edit'],
    'supprimer-activite' => ['ActivityController', 'delete'],
    'inscription-activite' => ['ActivityController', 'register'],
    'desinscription-activite' => ['ActivityController', 'unregister'],
    'recherche' => ['ActivityController', 'search'],
    'tag' => ['ActivityController', 'byTag'],

    'connexion' => ['AuthController', 'login'],
    'inscription' => ['AuthController', 'register'],
    'deconnexion' => ['AuthController', 'logout'],
    'mot-de-passe-oublie' => ['AuthController', 'forgotPassword'],
    'reinitialiser-mot-de-passe' => ['AuthController', 'resetPassword'],

    'profil' => ['ProfileController', 'show'],
    'modifier-profil' => ['ProfileController', 'edit'],
    'mes-activites' => ['ProfileController', 'myActivities'],
    'mes-inscriptions' => ['ProfileController', 'myRegistrations'],

    'membres' => ['ProfileController', 'directory'],
    'utilisateur' => ['ProfileController', 'publicProfile'],
    'suivre' => ['ProfileController', 'follow'],
    'ne-plus-suivre' => ['ProfileController', 'unfollow'],
    'abonnes' => ['ProfileController', 'followers'],
    'abonnements' => ['ProfileController', 'following'],

    'noter-activite' => ['RatingController', 'rateActivity'],
    'noter-organisateur' => ['RatingController', 'rateOrganizer'],

    'activite-ics' => ['IcsController', 'export'],
    'activite-stats' => ['StatsController', 'activity'],
    'organisateur-stats' => ['StatsController', 'organizer'],

    'panier' => ['CartController', 'index'],
    'panier-ajouter' => ['CartController', 'add'],
    'panier-retirer' => ['CartController', 'remove'],
    'panier-modifier' => ['CartController', 'update'],
    'paiement' => ['CheckoutController', 'index'],
    'confirmation-paiement' => ['CheckoutController', 'confirmation'],
    'mes-paiements' => ['CheckoutController', 'history'],
    'recu-paiement' => ['CheckoutController', 'receipt'],

    'messagerie' => ['MessagingController', 'index'],
    'conversation' => ['MessagingController', 'conversation'],
    'nouveau-message' => ['MessagingController', 'newConversation'],

    'chat-activite' => ['ChatController', 'activityChat'],

    'forum' => ['ForumController', 'index'],
    'forum-categorie' => ['ForumController', 'category'],
    'forum-topic' => ['ForumController', 'topic'],
    'forum-nouveau-sujet' => ['ForumController', 'createTopic'],
    'forum-supprimer-sujet' => ['ForumController', 'deleteTopic'],
    'forum-epingler-sujet' => ['ForumController', 'togglePin'],

    'admin' => ['AdminController', 'dashboard'],
    'admin-utilisateurs' => ['AdminController', 'users'],
    'admin-activites' => ['AdminController', 'activities'],
    'admin-faq' => ['AdminController', 'faq'],
    'admin-messages' => ['AdminController', 'messages'],
    'admin-toggle-user' => ['AdminController', 'toggleUser'],
    'admin-supprimer-user' => ['AdminController', 'deleteUser'],
    'admin-delete-activity' => ['AdminController', 'deleteActivity'],
    'admin-editorial' => ['AdminController', 'editorial'],
    'admin-mailbox' => ['AdminController', 'mailbox'],
    'admin-paiements' => ['AdminController', 'payments'],
    'admin-securite' => ['AdminController', 'security'],

    'faq' => ['ContactController', 'faq'],
    'cgu' => ['ContactController', 'cgu'],
    'mentions-legales' => ['ContactController', 'mentionsLegales'],
    'contact' => ['ContactController', 'contact'],
];

if (isset($routes[$page])) {
    $controllerName = $routes[$page][0];
    $actionName = $routes[$page][1];
    $controller = new $controllerName();
    $controller->$actionName();
} else {
    http_response_code(404);
    include __DIR__ . '/views/pages/404.php';
}
