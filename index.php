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

$page = 'home';
if (isset($_GET['page'])) {
    $page = $_GET['page'];
}

if ($page === 'home') {
    $controller = new HomeController();
    $controller->index();

} else if ($page === 'activites') {
    $controller = new ActivityController();
    $controller->index();

} else if ($page === 'activite') {
    $controller = new ActivityController();
    $controller->show();

} else if ($page === 'creer-activite') {
    $controller = new ActivityController();
    $controller->create();

} else if ($page === 'modifier-activite') {
    $controller = new ActivityController();
    $controller->edit();

} else if ($page === 'supprimer-activite') {
    $controller = new ActivityController();
    $controller->delete();

} else if ($page === 'inscription-activite') {
    $controller = new ActivityController();
    $controller->register();

} else if ($page === 'desinscription-activite') {
    $controller = new ActivityController();
    $controller->unregister();

} else if ($page === 'recherche') {
    $controller = new ActivityController();
    $controller->search();

} else if ($page === 'connexion') {
    $controller = new AuthController();
    $controller->login();

} else if ($page === 'inscription') {
    $controller = new AuthController();
    $controller->register();

} else if ($page === 'deconnexion') {
    $controller = new AuthController();
    $controller->logout();

} else if ($page === 'mot-de-passe-oublie') {
    $controller = new AuthController();
    $controller->forgotPassword();

} else if ($page === 'profil') {
    $controller = new ProfileController();
    $controller->show();

} else if ($page === 'modifier-profil') {
    $controller = new ProfileController();
    $controller->edit();

} else if ($page === 'mes-activites') {
    $controller = new ProfileController();
    $controller->myActivities();

} else if ($page === 'mes-inscriptions') {
    $controller = new ProfileController();
    $controller->myRegistrations();

} else if ($page === 'admin') {
    $controller = new AdminController();
    $controller->dashboard();

} else if ($page === 'admin-utilisateurs') {
    $controller = new AdminController();
    $controller->users();

} else if ($page === 'admin-activites') {
    $controller = new AdminController();
    $controller->activities();

} else if ($page === 'admin-faq') {
    $controller = new AdminController();
    $controller->faq();

} else if ($page === 'admin-messages') {
    $controller = new AdminController();
    $controller->messages();

} else if ($page === 'admin-toggle-user') {
    $controller = new AdminController();
    $controller->toggleUser();

} else if ($page === 'admin-delete-activity') {
    $controller = new AdminController();
    $controller->deleteActivity();

} else if ($page === 'faq') {
    $controller = new ContactController();
    $controller->faq();

} else if ($page === 'cgu') {
    $controller = new ContactController();
    $controller->cgu();

} else if ($page === 'mentions-legales') {
    $controller = new ContactController();
    $controller->mentionsLegales();

} else if ($page === 'contact') {
    $controller = new ContactController();
    $controller->contact();

} else {
    http_response_code(404);
    include __DIR__ . '/views/pages/404.php';
}
