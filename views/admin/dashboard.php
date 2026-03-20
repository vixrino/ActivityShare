<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-tachometer-alt"></i> Tableau de bord</h1>
        <p>Administration ActivityShare</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="admin-nav">
            <a href="index.php?page=admin" class="admin-nav-link active">
                <i class="fas fa-tachometer-alt"></i> Tableau de bord
            </a>
            <a href="index.php?page=admin-utilisateurs" class="admin-nav-link">
                <i class="fas fa-users"></i> Utilisateurs
            </a>
            <a href="index.php?page=admin-activites" class="admin-nav-link">
                <i class="fas fa-calendar"></i> Activités
            </a>
            <a href="index.php?page=admin-faq" class="admin-nav-link">
                <i class="fas fa-question-circle"></i> FAQ
            </a>
            <a href="index.php?page=admin-messages" class="admin-nav-link">
                <i class="fas fa-envelope"></i> Messages
                <?php if ($stats['messages_non_lus'] > 0): ?>
                    <span class="notif-badge"><?= $stats['messages_non_lus'] ?></span>
                <?php endif; ?>
            </a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon bg-green"><i class="fas fa-users"></i></div>
                <div class="stat-content">
                    <h3><?= $stats['total_utilisateurs'] ?></h3>
                    <p>Utilisateurs</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-blue"><i class="fas fa-user"></i></div>
                <div class="stat-content">
                    <h3><?= $stats['total_participants'] ?></h3>
                    <p>Participants</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-orange"><i class="fas fa-bullhorn"></i></div>
                <div class="stat-content">
                    <h3><?= $stats['total_organisateurs'] ?></h3>
                    <p>Organisateurs</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-green"><i class="fas fa-calendar-alt"></i></div>
                <div class="stat-content">
                    <h3><?= $stats['total_activites'] ?></h3>
                    <p>Activités totales</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-blue"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-content">
                    <h3><?= $stats['activites_actives'] ?></h3>
                    <p>Activités actives</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-orange"><i class="fas fa-clipboard-list"></i></div>
                <div class="stat-content">
                    <h3><?= $stats['total_inscriptions'] ?></h3>
                    <p>Inscriptions</p>
                </div>
            </div>
        </div>
    </div>
</section>
