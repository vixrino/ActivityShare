</main>

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <div class="footer-logo-wrapper">
                    <img src="assets/img/logo.png" alt="ActivityShare" class="footer-logo">
                </div>
                <p class="footer-brand-name">ActivityShare</p>
                <p class="footer-desc">La plateforme qui connecte les passionnés d'activités locales. Proposez, découvrez et partagez des expériences uniques près de chez vous.</p>
                <p class="footer-team">Équipe <strong>Webkit</strong> — Groupe G8A</p>
            </div>
            <div class="footer-col">
                <h4>Navigation</h4>
                <ul>
                    <li><a href="index.php?page=home">Accueil</a></li>
                    <li><a href="index.php?page=activites">Activités</a></li>
                    <li><a href="index.php?page=forum">Forum</a></li>
                    <li><a href="index.php?page=faq">FAQ</a></li>
                    <li><a href="index.php?page=contact">Contact</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Mon compte</h4>
                <ul>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="index.php?page=profil">Mon profil</a></li>
                        <li><a href="index.php?page=panier">Panier</a></li>
                        <li><a href="index.php?page=mes-inscriptions">Mes inscriptions</a></li>
                        <li><a href="index.php?page=mes-paiements">Mes paiements</a></li>
                        <li><a href="index.php?page=messagerie">Messagerie</a></li>
                    <?php else: ?>
                        <li><a href="index.php?page=connexion">Connexion</a></li>
                        <li><a href="index.php?page=inscription">Inscription</a></li>
                        <li><a href="index.php?page=mot-de-passe-oublie">Mot de passe oublié</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Informations</h4>
                <ul>
                    <li><a href="index.php?page=cgu">Conditions Générales</a></li>
                    <li><a href="index.php?page=mentions-legales">Mentions Légales</a></li>
                    <li><a href="index.php?page=faq">Aide</a></li>
                </ul>
                <p class="footer-contact-line"><i class="fas fa-envelope" aria-hidden="true"></i> contact@activityshare.com</p>
                <p class="footer-contact-line"><i class="fas fa-map-marker-alt" aria-hidden="true"></i> Bordeaux, France</p>
                <div class="footer-social" aria-label="Réseaux sociaux">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook" aria-hidden="true"></i></a>
                    <a href="#" aria-label="Twitter / X"><i class="fab fa-twitter" aria-hidden="true"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> ActivityShare — Équipe Webkit / Groupe G8A — Tous droits réservés.</p>
        </div>
    </div>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
