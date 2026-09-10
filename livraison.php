<?php
require_once __DIR__ . '/includes/auth.php';
exigerRole('client');
$pdo = getConnexion();
$titrePage = "Suivi de livraison";

$commandeId = (int)($_GET['commande'] ?? 0);
$stmt = $pdo->prepare(
    "SELECT l.*, c.total, c.statut AS statut_commande
     FROM livraisons l JOIN commandes c ON c.id = l.commande_id
     WHERE l.commande_id = :id AND c.client_id = :cid"
);
$stmt->execute([':id' => $commandeId, ':cid' => $_SESSION['user_id']]);
$livraison = $stmt->fetch();

if (!$livraison) { header('Location: index.php'); exit; }

$etapes = ['en_preparation' => 1, 'en_cours' => 2, 'livree' => 3];
$etapeActuelle = $etapes[$livraison['statut']] ?? 1;

require_once __DIR__ . '/includes/header.php';
?>

<div class="container my-5" style="max-width: 650px;">
  <h2 class="mb-4">Suivi de la commande #<?= $commandeId ?></h2>

  <div class="card">
    <div class="card-body">
      <div class="etape-suivi mb-4">
        <div class="cercle bg-<?= $etapeActuelle >= 1 ? 'dark' : 'secondary' ?>">1</div>
        <h6>Commande en preparation</h6>
        <p class="text-muted small mb-0">Votre commande est preparee par le vendeur.</p>
      </div>
      <div class="etape-suivi mb-4">
        <div class="cercle bg-<?= $etapeActuelle >= 2 ? 'dark' : 'secondary' ?>">2</div>
        <h6>En cours de livraison</h6>
        <p class="text-muted small mb-0">
          Transporteur : <?= htmlspecialchars($livraison['transporteur']) ?><br>
          <?= $livraison['date_expedition'] ? 'Expediee le ' . date('d/m/Y H:i', strtotime($livraison['date_expedition'])) : 'Pas encore expediee' ?>
        </p>
      </div>
      <div class="etape-suivi">
        <div class="cercle bg-<?= $etapeActuelle >= 3 ? 'success' : 'secondary' ?>">3</div>
        <h6>Livree</h6>
        <p class="text-muted small mb-0">
          Date prevue : <?= date('d/m/Y', strtotime($livraison['date_livraison_prevue'])) ?><br>
          Adresse : <?= htmlspecialchars($livraison['adresse']) ?>
        </p>
      </div>
    </div>
  </div>

  <a href="mes_commandes.php" class="btn btn-outline-dark mt-4">Retour a mes commandes</a>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
