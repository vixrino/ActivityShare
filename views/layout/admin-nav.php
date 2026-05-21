<?php
$adminPage = $page ?? '';
?>
<div class="admin-nav" role="navigation" aria-label="Navigation administration">
    <a href="index.php?page=admin" class="admin-nav-link <?= $adminPage === 'admin' ? 'active' : '' ?>">
        <i class="fas fa-tachometer-alt" aria-hidden="true"></i> Tableau de bord
    </a>
    <a href="index.php?page=admin-utilisateurs" class="admin-nav-link <?= $adminPage === 'admin-utilisateurs' ? 'active' : '' ?>">
        <i class="fas fa-users" aria-hidden="true"></i> Utilisateurs
    </a>
    <a href="index.php?page=admin-activites" class="admin-nav-link <?= $adminPage === 'admin-activites' ? 'active' : '' ?>">
        <i class="fas fa-calendar" aria-hidden="true"></i> Activités
    </a>
    <a href="index.php?page=admin-paiements" class="admin-nav-link <?= $adminPage === 'admin-paiements' ? 'active' : '' ?>">
        <i class="fas fa-receipt" aria-hidden="true"></i> Paiements
    </a>
    <a href="index.php?page=admin-faq" class="admin-nav-link <?= $adminPage === 'admin-faq' ? 'active' : '' ?>">
        <i class="fas fa-question-circle" aria-hidden="true"></i> FAQ
    </a>
    <a href="index.php?page=admin-editorial" class="admin-nav-link <?= $adminPage === 'admin-editorial' ? 'active' : '' ?>">
        <i class="fas fa-file-contract" aria-hidden="true"></i> CGU & Mentions
    </a>
    <a href="index.php?page=admin-messages" class="admin-nav-link <?= $adminPage === 'admin-messages' ? 'active' : '' ?>">
        <i class="fas fa-envelope" aria-hidden="true"></i> Messages
    </a>
    <a href="index.php?page=admin-mailbox" class="admin-nav-link <?= $adminPage === 'admin-mailbox' ? 'active' : '' ?>">
        <i class="fas fa-inbox" aria-hidden="true"></i> Boîte mail
    </a>
    <a href="index.php?page=admin-securite" class="admin-nav-link <?= $adminPage === 'admin-securite' ? 'active' : '' ?>">
        <i class="fas fa-shield-alt" aria-hidden="true"></i> Sécurité
    </a>
</div>
