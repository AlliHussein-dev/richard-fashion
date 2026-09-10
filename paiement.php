<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/services/PaiementService.php';
require_once __DIR__ . '/services/LivraisonService.php';
exigerRole('client');
$pdo = getConnexion();
$titrePage = "Paiement";

$commandeId = (int)($_GET['commande'] ?? $_POST['commande'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = :id AND client_id = :cid");
$stmt->execute([':id' => $commandeId, ':cid' => $_SESSION['user_id']]);
$commande = $stmt->fetch();

if (!$commande) { header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['methode'])) {
    $methode = $_POST['methode'];

    // Appel au service de paiement (simule)
    $resultat = PaiementService::traiterPaiement($methode, (float)$commande['total']);

    $pdo->prepare(
        "INSERT INTO paiements (commande_id, methode, reference_transaction, statut) VALUES (:cmd, :m, :ref, :statut)"
    )->execute([
        ':cmd' => $commandeId, ':m' => $methode,
        ':ref' => $resultat['reference'], ':statut' => $resultat['statut'],
    ]);

    if ($resultat['succes']) {
        $nouveauStatutCommande = ($methode === 'especes_a_la_livraison') ? 'en_preparation' : 'payee';
        $pdo->prepare("UPDATE commandes SET statut = :s WHERE id = :id")
            ->execute([':s' => $nouveauStatutCommande, ':id' => $commandeId]);

        // Declenche le service de livraison (simule)
        LivraisonService::creerLivraison($pdo, $commandeId, $commande['adresse_livraison']);

        header('Location: confirmation.php?commande=' . $commandeId);
        exit;
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Le paiement a echoue. Veuillez reessayer ou choisir une autre methode.'];
        header('Location: paiement.php?commande=' . $commandeId);
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container my-5" style="max-width: 600px;">
  <h2 class="mb-4">Paiement de la commande #<?= $commandeId ?></h2>

  <div class="card mb-4">
    <div class="card-body d-flex justify-content-between">
      <span>Montant a payer</span>
      <span class="fw-bold text-warning fs-5"><?= number_format($commande['total'], 0, ',', ' ') ?> BIF</span>
    </div>
  </div>

  <form method="POST">
    <input type="hidden" name="commande" value="<?= $commandeId ?>">
    <h5 class="mb-3">Choisissez un moyen de paiement</h5>

    <div class="list-group mb-4">
      <label class="list-group-item d-flex gap-3">
        <input type="radio" name="methode" value="mobile_money" class="form-check-input" required>
        <span><i class="bi bi-phone"></i> <strong>Mobile Money</strong> — Lumicash, EcoCash, etc.</span>
      </label>
      <label class="list-group-item d-flex gap-3">
        <input type="radio" name="methode" value="carte_bancaire" class="form-check-input">
        <span><i class="bi bi-credit-card"></i> <strong>Carte bancaire</strong> — Visa / Mastercard</span>
      </label>
      <label class="list-group-item d-flex gap-3">
        <input type="radio" name="methode" value="especes_a_la_livraison" class="form-check-input">
        <span><i class="bi bi-cash-coin"></i> <strong>Especes a la livraison</strong> — payer au moment de la reception</span>
      </label>
    </div>

    <button type="submit" class="btn btn-dark w-100 btn-lg">Valider le paiement</button>
  </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
