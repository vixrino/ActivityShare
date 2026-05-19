<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas <?= isset($iconClass) ? $iconClass : 'fa-file-alt' ?>" aria-hidden="true"></i>
            <?= sanitize($contenu['titre'] ?? $pageTitle) ?></h1>
        <?php if (!empty($contenu['date_maj'])): ?>
            <p>Dernière mise à jour : <?= formatDateShort($contenu['date_maj']) ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="legal-content">
            <?php if (!empty($contenu['contenu'])): ?>
                <?= $contenu['contenu'] ?>
            <?php else: ?>
                <p class="text-muted">Aucun contenu n'a été publié pour cette page.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
