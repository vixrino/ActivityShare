<?php
session_start();

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

require_once __DIR__ . '/controllers/HomeController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/ActivityController.php';
require_once __DIR__ . '/controllers/ProfileController.php';
require_once __DIR__ . '/controllers/AdminController.php';
require_once __DIR__ . '/controllers/ContactController.php';

$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

$routes = [
    'home'              => ['HomeController', 'index'],
    'activites'         => ['ActivityController', 'index'],
    'activite'          => ['ActivityController', 'show'],
    'creer-activite'    => ['ActivityController', 'create'],
    'modifier-activite' => ['ActivityController', 'edit'],
    'supprimer-activite'=> ['ActivityController', 'delete'],
    'inscription-activite' => ['ActivityController', 'register'],
    'desinscription-activite' => ['ActivityController', 'unregister'],
    'connexion'         => ['AuthController', 'login'],
    'inscription'       => ['AuthController', 'register'],
    'deconnexion'       => ['AuthController', 'logout'],
    'mot-de-passe-oublie' => ['AuthController', 'forgotPassword'],
    'profil'            => ['ProfileController', 'show'],
    'modifier-profil'   => ['ProfileController', 'edit'],
    'mes-activites'     => ['ProfileController', 'myActivities'],
    'mes-inscriptions'  => ['ProfileController', 'myRegistrations'],
    'admin'             => ['AdminController', 'dashboard'],
    'admin-utilisateurs'=> ['AdminController', 'users'],
    'admin-activites'   => ['AdminController', 'activities'],
    'admin-faq'         => ['AdminController', 'faq'],
    'admin-messages'    => ['AdminController', 'messages'],
    'admin-toggle-user' => ['AdminController', 'toggleUser'],
    'admin-delete-activity' => ['AdminController', 'deleteActivity'],
    'faq'               => ['ContactController', 'faq'],
    'cgu'               => ['ContactController', 'cgu'],
    'mentions-legales'  => ['ContactController', 'mentionsLegales'],
    'contact'           => ['ContactController', 'contact'],
    'recherche'         => ['ActivityController', 'search'],
];

if (isset($routes[$page])) {
    [$controllerName, $method] = $routes[$page];
    $controller = new $controllerName();
    $controller->$method();
} else {
    http_response_code(404);
    include __DIR__ . '/views/pages/404.php';
}
