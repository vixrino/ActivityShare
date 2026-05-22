<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ActivityShare : la plateforme pour proposer et rejoindre des activités locales entre particuliers.">
    <meta name="theme-color" content="#1a1a2e">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' - ActivityShare' : 'ActivityShare' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/img/logo.png" type="image/png">
</head>
<body>

<a href="#main-content" class="skip-link">Aller au contenu principal</a>

<nav class="navbar" aria-label="Navigation principale">
    <div class="container navbar-content">
        <a href="index.php?page=home" class="navbar-brand" aria-label="ActivityShare - retour à l'accueil">
            <img src="assets/img/logo.png" alt="ActivityShare" class="navbar-logo">
        </a>

        <button class="navbar-toggle" id="navbarToggle" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="navbarMenu">
            <i class="fas fa-bars" aria-hidden="true"></i>
        </button>

        <div class="navbar-menu" id="navbarMenu">
            <a href="index.php?page=home" class="navbar-link <?= ($page ?? '') === 'home' ? 'active' : '' ?>">Accueil</a>
            <a href="index.php?page=activites" class="navbar-link <?= in_array(($page ?? ''), ['activites', 'activite', 'recherche']) ? 'active' : '' ?>">Activités</a>
            <a href="index.php?page=forum" class="navbar-link <?= in_array(($page ?? ''), ['forum', 'forum-categorie', 'forum-topic', 'forum-nouveau-sujet']) ? 'active' : '' ?>">Forum</a>
            <a href="index.php?page=faq" class="navbar-link <?= ($page ?? '') === 'faq' ? 'active' : '' ?>">FAQ</a>
            <a href="index.php?page=contact" class="navbar-link <?= ($page ?? '') === 'contact' ? 'active' : '' ?>">Contact</a>

            <?php if (isLoggedIn()): ?>
                <?php
                $cartCount = cartCount();
                $unreadMsg = unreadMessages();
                ?>
                <a href="index.php?page=panier" class="navbar-icon-link <?= in_array(($page ?? ''), ['panier','paiement','confirmation-paiement']) ? 'active' : '' ?>" aria-label="Panier (<?= $cartCount ?> articles)">
                    <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                    <?php if ($cartCount > 0): ?><span class="navbar-icon-badge"><?= $cartCount ?></span><?php endif; ?>
                </a>
                <a href="index.php?page=messagerie" class="navbar-icon-link <?= in_array(($page ?? ''), ['messagerie','conversation','nouveau-message']) ? 'active' : '' ?>" aria-label="Messagerie (<?= $unreadMsg ?> non lus)">
                    <i class="fas fa-envelope" aria-hidden="true"></i>
                    <?php if ($unreadMsg > 0): ?><span class="navbar-icon-badge"><?= $unreadMsg ?></span><?php endif; ?>
                </a>

                <?php if (isOrganisateur()): ?>
                    <a href="index.php?page=creer-activite" class="btn btn-outline btn-sm">
                        <i class="fas fa-plus" aria-hidden="true"></i> Créer
                    </a>
                <?php endif; ?>

                <div class="navbar-dropdown">
                    <button class="navbar-link dropdown-toggle" aria-haspopup="true" aria-expanded="false">
                        <?php if (!empty($_SESSION['user_photo'])): ?>
                            <img src="<?= sanitize($_SESSION['user_photo']) ?>" alt="" class="navbar-avatar">
                        <?php else: ?>
                            <i class="fas fa-user-circle" aria-hidden="true"></i>
                        <?php endif; ?>
                        <?= sanitize($_SESSION['user_prenom']) ?>
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div class="dropdown-menu" role="menu">
                        <a href="index.php?page=profil" role="menuitem"><i class="fas fa-user" aria-hidden="true"></i> Mon Profil</a>
                        <a href="index.php?page=mes-inscriptions" role="menuitem"><i class="fas fa-calendar-check" aria-hidden="true"></i> Mes Inscriptions</a>
                        <a href="index.php?page=mes-paiements" role="menuitem"><i class="fas fa-receipt" aria-hidden="true"></i> Mes Paiements</a>
                        <?php if (isOrganisateur()): ?>
                            <a href="index.php?page=mes-activites" role="menuitem"><i class="fas fa-list" aria-hidden="true"></i> Mes Activités</a>
                        <?php endif; ?>
                        <?php if (isAdmin()): ?>
                            <a href="index.php?page=admin" role="menuitem"><i class="fas fa-cog" aria-hidden="true"></i> Administration</a>
                        <?php endif; ?>
                        <hr>
                        <a href="index.php?page=deconnexion" class="text-danger" role="menuitem"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> Déconnexion</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="index.php?page=connexion" class="btn btn-outline btn-sm">Connexion</a>
                <a href="index.php?page=inscription" class="btn btn-primary btn-sm">Inscription</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<button id="accessibility-toggle" class="accessibility-toggle" aria-label="Ouvrir le menu accessibilité" aria-expanded="false" aria-controls="accessibility-panel">
    <i class="fas fa-universal-access" aria-hidden="true"></i>
</button>
<aside id="accessibility-panel" class="accessibility-panel" aria-label="Préférences d'accessibilité" hidden>
    <h2><i class="fas fa-universal-access" aria-hidden="true"></i> Accessibilité</h2>
    <div class="a11y-group">
        <label>Taille du texte</label>
        <div class="a11y-buttons" role="group" aria-label="Taille du texte">
            <button type="button" data-a11y="font" data-value="small">A-</button>
            <button type="button" data-a11y="font" data-value="normal" class="active">A</button>
            <button type="button" data-a11y="font" data-value="large">A+</button>
            <button type="button" data-a11y="font" data-value="xlarge">A++</button>
        </div>
    </div>
    <div class="a11y-group">
        <label>Contraste</label>
        <div class="a11y-buttons" role="group" aria-label="Contraste">
            <button type="button" data-a11y="contrast" data-value="normal" class="active">Normal</button>
            <button type="button" data-a11y="contrast" data-value="high">Élevé</button>
            <button type="button" data-a11y="contrast" data-value="dark">Sombre</button>
        </div>
    </div>
    <div class="a11y-group">
        <label>Confort de lecture</label>
        <div class="a11y-buttons" role="group" aria-label="Confort de lecture">
            <button type="button" data-a11y="dyslexia" data-value="off" class="active">Standard</button>
            <button type="button" data-a11y="dyslexia" data-value="on">Espacement renforcé</button>
        </div>
    </div>
    <div class="a11y-group">
        <label>Animations</label>
        <div class="a11y-buttons" role="group" aria-label="Animations">
            <button type="button" data-a11y="motion" data-value="on" class="active">Activées</button>
            <button type="button" data-a11y="motion" data-value="off">Réduites</button>
        </div>
    </div>
    <button type="button" id="a11y-reset" class="btn btn-outline btn-sm btn-block">
        <i class="fas fa-undo" aria-hidden="true"></i> Réinitialiser
    </button>
</aside>

<main id="main-content" tabindex="-1">
    <?php $flashMessage = flash(); ?>
    <?php if ($flashMessage): ?>
        <div class="container">
            <div class="alert alert-<?= $flashMessage['type'] ?>" role="alert">
                <?= sanitize($flashMessage['message']) ?>
                <button class="alert-close" onclick="this.parentElement.remove()" aria-label="Fermer">&times;</button>
            </div>
        </div>
    <?php endif; ?>
