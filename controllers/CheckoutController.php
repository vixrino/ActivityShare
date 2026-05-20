<?php
class CheckoutController {

    public function index() {
        requireLogin();

        $cartModel = new Cart();
        $items = $cartModel->getByUser($_SESSION['user_id']);
        $total = $cartModel->total($_SESSION['user_id']);
        $errors = [];

        if (empty($items)) {
            $_SESSION['flash'] = ['type' => 'info', 'message' => 'Votre panier est vide.'];
            redirect('panier');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfVerify();
            $titulaire = sanitize($_POST['titulaire'] ?? '');
            $numeroCarte = preg_replace('/\s+/', '', $_POST['numero_carte'] ?? '');
            $expiration = sanitize($_POST['expiration'] ?? '');
            $cvv = preg_replace('/\D/', '', $_POST['cvv'] ?? '');
            $adresse = sanitize($_POST['adresse'] ?? '');
            $ville = sanitize($_POST['ville_facturation'] ?? '');
            $cp = sanitize($_POST['code_postal'] ?? '');
            $pays = sanitize($_POST['pays'] ?? 'France');

            if (strlen($titulaire) < 3) {
                $errors[] = 'Le titulaire de la carte est requis.';
            }
            if (!preg_match('/^\d{16}$/', $numeroCarte)) {
                $errors[] = 'Le numéro de carte doit contenir 16 chiffres.';
            }
            if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiration)) {
                $errors[] = 'La date d\'expiration doit être au format MM/AA.';
            } else {
                $parts = explode('/', $expiration);
                $mois = intval($parts[0]);
                $annee = 2000 + intval($parts[1]);
                $finDuMois = mktime(23, 59, 59, $mois + 1, 0, $annee);
                if ($finDuMois < time()) {
                    $errors[] = 'Votre carte est expirée.';
                }
            }
            if (!preg_match('/^\d{3,4}$/', $cvv)) {
                $errors[] = 'Le cryptogramme (CVV) doit contenir 3 ou 4 chiffres.';
            }
            if (empty($adresse) || empty($ville) || empty($cp)) {
                $errors[] = 'L\'adresse de facturation est incomplète.';
            }

            if (empty($errors)) {
                $paymentModel = new Payment();
                $registrationModel = new Registration();
                $waitingListModel = new WaitingList();
                $notificationModel = new Notification();
                $activityModel = new Activity();

                $lignes = [];
                foreach ($items as $item) {
                    $lignes[] = [
                        'activite_id' => $item['activite_id'],
                        'titre' => $item['titre'],
                        'prix_unitaire' => floatval($item['prix']),
                        'quantite' => 1,
                    ];
                }

                $reference = $paymentModel->generateReference();
                $paiementId = $paymentModel->create([
                    'utilisateur_id' => $_SESSION['user_id'],
                    'reference' => $reference,
                    'montant_total' => $total,
                    'titulaire_carte' => $titulaire,
                    'derniers_chiffres' => substr($numeroCarte, -4),
                    'methode' => 'carte',
                    'statut' => 'confirme',
                    'adresse_facturation' => $adresse,
                    'ville_facturation' => $ville,
                    'code_postal' => $cp,
                    'pays' => $pays,
                ], $lignes);

                foreach ($items as $item) {
                    $activite = $activityModel->find($item['activite_id']);
                    if (!$activite) {
                        continue;
                    }

                    if ($registrationModel->isRegistered($item['activite_id'], $_SESSION['user_id'])) {
                        continue;
                    }

                    $nbInscrits = $registrationModel->countByActivity($item['activite_id']);
                    if ($nbInscrits < $activite['nb_max_participants']) {
                        $registrationModel->createWithPayment($item['activite_id'], $_SESSION['user_id'], $paiementId);
                        $notificationModel->create([
                            'utilisateur_id' => $_SESSION['user_id'],
                            'type' => 'confirmation_inscription',
                            'titre' => 'Inscription confirmée',
                            'message' => 'Vous êtes inscrit à l\'activité "' . $activite['titre'] . '". Paiement ' . $reference . '.',
                        ]);
                    } else {
                        if (!$waitingListModel->isOnWaitingList($item['activite_id'], $_SESSION['user_id'])) {
                            $waitingListModel->add($item['activite_id'], $_SESSION['user_id']);
                        }
                    }
                }

                $notificationModel->create([
                    'utilisateur_id' => $_SESSION['user_id'],
                    'type' => 'paiement',
                    'titre' => 'Paiement confirmé',
                    'message' => 'Votre paiement ' . $reference . ' d\'un montant de ' . number_format($total, 2, ',', ' ') . ' € a été accepté.',
                ]);

                $cartModel->clear($_SESSION['user_id']);

                redirect('confirmation-paiement', ['ref' => $reference]);
            }
        }

        $pageTitle = 'Paiement sécurisé';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/checkout/payment.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function confirmation() {
        requireLogin();

        $reference = $_GET['ref'] ?? '';
        if (empty($reference)) {
            redirect('home');
        }

        $paymentModel = new Payment();
        $paiement = $paymentModel->findByReference($reference);

        if (!$paiement || $paiement['utilisateur_id'] != $_SESSION['user_id']) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Paiement introuvable.'];
            redirect('home');
        }

        $lignes = $paymentModel->getLines($paiement['id']);

        $pageTitle = 'Confirmation de paiement';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/checkout/confirmation.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function history() {
        requireLogin();

        $paymentModel = new Payment();
        $paiements = $paymentModel->getByUser($_SESSION['user_id']);

        $details = [];
        foreach ($paiements as $p) {
            $details[$p['id']] = $paymentModel->getLines($p['id']);
        }

        $pageTitle = 'Mes paiements';
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/checkout/history.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function receipt() {
        requireLogin();

        $reference = $_GET['ref'] ?? '';
        $paymentModel = new Payment();
        $paiement = $paymentModel->findByReference($reference);

        if (!$paiement || ($paiement['utilisateur_id'] != $_SESSION['user_id'] && !isAdmin())) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Paiement introuvable.'];
            redirect('home');
        }

        $lignes = $paymentModel->getLines($paiement['id']);
        $userModel = new User();
        $client = $userModel->find($paiement['utilisateur_id']);

        $pageTitle = 'Reçu ' . $paiement['reference'];
        include __DIR__ . '/../views/checkout/receipt.php';
    }
}
