<?php
require_once __DIR__ . '/includes/auth.php';
exigerRole('client');
$pdo = getConnexion();
$titrePage = "Confirmation";

$commandeId = (int)($_GET['commande'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = :id AND client_id = :cid");
$stmt->execute([':id' => $commandeId, ':cid' => $_SESSION['user_id']]);
$commande = $stmt->fetch();

if (!$commande) { header('Location: index.php'); exit; }

$paiement = $pdo->prepare("SELECT * FROM paiements WHERE commande_id = :id ORDER BY id DESC LIMIT 1");
$paiement->execute([':id' => $commandeId]);
$paiement = $paiement->fetch();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container my-5 text-center" style="max-width: 600px;">
  <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
  <h2 class="mt-3">Commande confirmee !</h2>
  <p class="text-muted">Votre commande #<?= $commandeId ?> a bien ete enregistree.</p>

  <div class="card text-start mt-4">
    <div class="card-body">
      <p><strong>Statut de la commande :</strong> <?= htmlspecialchars(ucfirst(str_replace('_',' ', $commande['statut']))) ?></p>
      <p><strong>Methode de paiement :</strong> <?= htmlspecialchars(ucfirst(str_replace('_',' ', $paiement['methode'] ?? ''))) ?></p>
      <p><strong>Reference de paiement :</strong> <?= htmlspecialchars($paiement['reference_transaction'] ?? '-') ?></p>
      <p><strong>Adresse de livraison :</strong> <?= htmlspecialchars($commande['adresse_livraison']) ?></p>
      <p class="mb-0"><strong>Total :</strong> <?= number_format($commande['total'], 0, ',', ' ') ?> BIF</p>
    </div>
  </div>

  <div class="d-flex gap-2 justify-content-center mt-4">
    <a href="livraison.php?commande=<?= $commandeId ?>" class="btn btn-dark">Suivre ma livraison</a>
    <a href="index.php" class="btn btn-outline-dark">Retour a l'accueil</a>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
