<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' - ActivityShare' : 'ActivityShare' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/img/logo.png" type="image/png">
</head>
<body>

<nav class="navbar">
    <div class="container navbar-content">
        <a href="index.php?page=home" class="navbar-brand">
            <img src="assets/img/logo.png" alt="ActivityShare" class="navbar-logo">
        </a>

        <button class="navbar-toggle" id="navbarToggle" aria-label="Menu">
            <i class="fas fa-bars"></i>
        </button>

        <div class="navbar-menu" id="navbarMenu">
            <a href="index.php?page=home" class="navbar-link <?= ($page ?? '') === 'home' ? 'active' : '' ?>">Accueil</a>
            <a href="index.php?page=activites" class="navbar-link <?= ($page ?? '') === 'activites' ? 'active' : '' ?>">Activités</a>
            <a href="index.php?page=faq" class="navbar-link <?= ($page ?? '') === 'faq' ? 'active' : '' ?>">FAQ</a>
            <a href="index.php?page=contact" class="navbar-link <?= ($page ?? '') === 'contact' ? 'active' : '' ?>">Contact</a>

            <?php if (isLoggedIn()): ?>
                <?php if (isOrganisateur()): ?>
                    <a href="index.php?page=creer-activite" class="navbar-link">
                        <i class="fas fa-plus"></i> Créer
                    </a>
                <?php endif; ?>

                <div class="navbar-dropdown">
                    <button class="navbar-link dropdown-toggle">
                        <i class="fas fa-user-circle"></i>
                        <?= sanitize($_SESSION['user_prenom']) ?>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="index.php?page=profil"><i class="fas fa-user"></i> Mon Profil</a>
                        <a href="index.php?page=mes-inscriptions"><i class="fas fa-calendar-check"></i> Mes Inscriptions</a>
                        <?php if (isOrganisateur()): ?>
                            <a href="index.php?page=mes-activites"><i class="fas fa-list"></i> Mes Activités</a>
                        <?php endif; ?>
                        <?php if (isAdmin()): ?>
                            <a href="index.php?page=admin"><i class="fas fa-cog"></i> Administration</a>
                        <?php endif; ?>
                        <hr>
                        <a href="index.php?page=deconnexion" class="text-danger"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="index.php?page=connexion" class="btn btn-outline btn-sm">Connexion</a>
                <a href="index.php?page=inscription" class="btn btn-primary btn-sm">Inscription</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main>
    <?php $flashMessage = flash(); ?>
    <?php if ($flashMessage): ?>
        <div class="container">
            <div class="alert alert-<?= $flashMessage['type'] ?>">
                <?= sanitize($flashMessage['message']) ?>
                <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
        </div>
    <?php endif; ?>
