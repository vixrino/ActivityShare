<section class="container stats-page">
    <header class="stats-header">
        <h1><i class="fas fa-chart-pie"></i> Tableau de bord organisateur</h1>
        <p class="muted">Performance de toutes vos activités, en un coup d'œil.</p>
    </header>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
            <div>
                <div class="stat-value"><?= count($activites) ?></div>
                <div class="stat-label">Activités publiées</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-eye"></i></div>
            <div>
                <div class="stat-value"><?= $totalVues ?></div>
                <div class="stat-label">Vues cumulées</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div>
                <div class="stat-value"><?= $totalInscrits ?> / <?= $totalPlaces ?></div>
                <div class="stat-label">Inscrits / places offertes</div>
                <div class="stat-sub"><strong><?= $tauxMoyen ?>%</strong> taux moyen de remplissage</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-star"></i></div>
            <div>
                <div class="stat-value"><?= $noteOrga['total'] > 0 ? number_format($noteOrga['moyenne'], 1, ',', '') . ' / 5' : '—' ?></div>
                <div class="stat-label">Note moyenne (<?= $noteOrga['total'] ?> avis)</div>
            </div>
        </div>
    </div>

    <h2 class="section-title mt-2">Détail par activité</h2>
    <?php if (empty($activites)): ?>
        <p class="muted">Vous n'avez pas encore d'activité.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Activité</th>
                        <th>Date</th>
                        <th>Inscrits</th>
                        <th>Remplissage</th>
                        <th>Vues</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($statsParActivite as $row): ?>
                        <?php $a = $row['activite']; ?>
                        <tr>
                            <td>
                                <a href="index.php?page=activite&id=<?= intval($a['id']) ?>"><?= sanitize($a['titre']) ?></a>
                                <br><small class="muted"><?= sanitize($a['categorie_nom']) ?></small>
                            </td>
                            <td><?= formatDateShort($a['date_debut']) ?></td>
                            <td><?= intval($a['nb_inscrits']) ?> / <?= intval($a['nb_max_participants']) ?></td>
                            <td>
                                <div class="progress-bar progress-sm">
                                    <span style="width: <?= min(100, $row['taux']) ?>%"></span>
                                </div>
                                <small><?= $row['taux'] ?>%</small>
                            </td>
                            <td><?= $row['vues'] ?></td>
                            <td>
                                <a href="index.php?page=activite-stats&id=<?= intval($a['id']) ?>" class="btn btn-sm btn-outline">
                                    <i class="fas fa-chart-line"></i> Détails
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
