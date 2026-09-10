<?php
require_once __DIR__ . '/../includes/auth.php';
exigerRole('vendeur');
$pdo = getConnexion();
$titrePage = "Espace vendeur";

$produits = $pdo->prepare(
    "SELECT p.*, (SELECT chemin_image FROM produit_images WHERE produit_id = p.id AND est_principale = 1 LIMIT 1) AS image
     FROM produits p WHERE p.vendeur_id = :vid ORDER BY p.date_ajout DESC"
);
$produits->execute([':vid' => $_SESSION['user_id']]);
$produits = $produits->fetchAll();

// Commandes contenant au moins un produit de ce vendeur
$commandes = $pdo->prepare(
    "SELECT DISTINCT c.id, c.date_commande, c.statut, c.total
     FROM commandes c
     JOIN commande_details cd ON cd.commande_id = c.id
     JOIN produits p ON p.id = cd.produit_id
     WHERE p.vendeur_id = :vid
     ORDER BY c.date_commande DESC LIMIT 10"
);
$commandes->execute([':vid' => $_SESSION['user_id']]);
$commandes = $commandes->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Espace vendeur</h2>
    <a href="ajouter_produit.php" class="btn btn-dark"><i class="bi bi-plus-lg"></i> Ajouter un produit</a>
  </div>

  <h5>Mes produits (<?= count($produits) ?>)</h5>
  <div class="table-responsive mb-5">
    <table class="table bg-white align-middle">
      <thead><tr><th></th><th>Nom</th><th>Prix</th><th>Stock</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($produits as $p): ?>
        <tr>
          <td><img src="../<?= htmlspecialchars($p['image']) ?>" width="50" height="60" style="object-fit:cover"></td>
          <td><?= htmlspecialchars($p['nom']) ?></td>
          <td><?= number_format($p['prix'], 0, ',', ' ') ?> BIF</td>
          <td><?= (int)$p['stock'] ?></td>
          <td><a href="modifier_produit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-dark">Modifier</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <h5>Dernieres commandes contenant mes produits</h5>
  <div class="table-responsive">
    <table class="table bg-white align-middle">
      <thead><tr><th>#</th><th>Date</th><th>Total</th><th>Statut</th></tr></thead>
      <tbody>
      <?php foreach ($commandes as $c): ?>
        <tr>
          <td>#<?= $c['id'] ?></td>
          <td><?= date('d/m/Y', strtotime($c['date_commande'])) ?></td>
          <td><?= number_format($c['total'], 0, ',', ' ') ?> BIF</td>
          <td><span class="badge bg-dark"><?= htmlspecialchars(str_replace('_',' ',$c['statut'])) ?></span></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
