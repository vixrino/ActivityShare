<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-question-circle"></i> Foire aux Questions</h1>
        <p>Retrouvez les réponses aux questions les plus fréquentes</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="faq-container">
            <?php if (empty($faqs)): ?>
                <div class="empty-state">
                    <i class="fas fa-question"></i>
                    <h3>Aucune question pour le moment</h3>
                </div>
            <?php else: ?>
                <?php foreach ($faqs as $f): ?>
                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleFaq(this)">
                            <span><?= sanitize($f['question']) ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer">
                            <p><?= nl2br(sanitize($f['reponse'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="faq-cta">
            <h3>Vous n'avez pas trouvé votre réponse ?</h3>
            <p>N'hésitez pas à nous contacter directement.</p>
            <a href="index.php?page=contact" class="btn btn-primary">
                <i class="fas fa-envelope"></i> Nous contacter
            </a>
        </div>
    </div>
</section>
