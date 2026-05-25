<section class="container stats-page">
    <div class="back-link-wrapper">
        <a href="javascript:history.back()" class="forum-back-btn"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>

    <header class="stats-header">
        <h1><i class="fas fa-chart-line"></i> Statistiques</h1>
        <h2 class="stats-subtitle"><?= sanitize($activite['titre']) ?></h2>
        <p class="muted">
            <i class="fas fa-calendar"></i> <?= formatDateShort($activite['date_debut']) ?>
            • <i class="fas fa-map-marker-alt"></i> <?= sanitize($activite['lieu']) ?>
        </p>
    </header>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-eye"></i></div>
            <div>
                <div class="stat-value"><?= $nbVues ?></div>
                <div class="stat-label">Vues au total</div>
                <div class="stat-sub"><?= $nbVues30j ?> sur 30 jours</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div>
                <div class="stat-value"><?= $nbInscrits ?> / <?= intval($activite['nb_max_participants']) ?></div>
                <div class="stat-label">Inscrits</div>
                <div class="progress-bar" aria-label="Taux de remplissage : <?= $tauxRemplissage ?>%">
                    <span style="width: <?= min(100, $tauxRemplissage) ?>%"></span>
                </div>
                <div class="stat-sub"><strong><?= $tauxRemplissage ?>%</strong> de remplissage</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div>
                <div class="stat-value"><?= count($listeAttente) ?></div>
                <div class="stat-label">Liste d'attente</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-percentage"></i></div>
            <div>
                <div class="stat-value"><?= $tauxConversion ?>%</div>
                <div class="stat-label">Taux de conversion (vue → inscription)</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-star"></i></div>
            <div>
                <div class="stat-value"><?= $notation['total'] > 0 ? number_format($notation['moyenne'], 1, ',', '') . ' / 5' : '—' ?></div>
                <div class="stat-label">Note moyenne (<?= $notation['total'] ?> avis)</div>
            </div>
        </div>
    </div>

    <?php if (!empty($serieVues)): ?>
        <section class="card stats-chart-block">
            <h3><i class="fas fa-chart-bar"></i> Vues sur les 14 derniers jours</h3>
            <?php
            $max = 1;
            foreach ($serieVues as $row) { if ($row['total'] > $max) $max = intval($row['total']); }
            ?>
            <div class="bar-chart" role="img" aria-label="Histogramme des vues quotidiennes">
                <?php foreach ($serieVues as $row): ?>
                    <?php $hauteur = max(4, intval(($row['total'] / $max) * 100)); ?>
                    <div class="bar-chart-item">
                        <div class="bar" style="height: <?= $hauteur ?>%" title="<?= $row['total'] ?> vues le <?= formatDateShort($row['jour']) ?>"></div>
                        <span class="bar-label"><?= date('d/m', strtotime($row['jour'])) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <div class="text-center mt-2">
        <a href="index.php?page=activite&id=<?= intval($activite['id']) ?>" class="btn btn-outline">
            <i class="fas fa-eye"></i> Voir la page publique
        </a>
        <a href="index.php?page=modifier-activite&id=<?= intval($activite['id']) ?>" class="btn btn-primary">
            <i class="fas fa-pen"></i> Modifier l'activité
        </a>
    </div>
</section>
