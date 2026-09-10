<?php
require_once __DIR__ . '/../includes/auth.php';
exigerRole('administrateur');
$pdo = getConnexion();
$titrePage = "Gestion des produits";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer'])) {
    $pdo->prepare("DELETE FROM produits WHERE id = :id")->execute([':id' => (int)$_POST['supprimer']]);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Produit supprime.'];
    header('Location: produits.php');
    exit;
}

$produits = $pdo->query(
    "SELECT p.*, u.nom AS vendeur_nom,
       (SELECT chemin_image FROM produit_images WHERE produit_id = p.id AND est_principale = 1 LIMIT 1) AS image
     FROM produits p JOIN utilisateurs u ON u.id = p.vendeur_id
     ORDER BY p.date_ajout DESC"
)->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container my-5">
  <h2 class="mb-4">Gestion des produits (<?= count($produits) ?>)</h2>

  <div class="table-responsive">
    <table class="table bg-white align-middle">
      <thead><tr><th></th><th>Nom</th><th>Vendeur</th><th>Prix</th><th>Stock</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($produits as $p): ?>
        <tr>
          <td><img src="../<?= htmlspecialchars($p['image']) ?>" width="50" height="60" style="object-fit:cover"></td>
          <td><?= htmlspecialchars($p['nom']) ?></td>
          <td><?= htmlspecialchars($p['vendeur_nom']) ?></td>
          <td><?= number_format($p['prix'],0,',',' ') ?> BIF</td>
          <td><?= (int)$p['stock'] ?></td>
          <td>
            <form method="POST" onsubmit="return confirm('Supprimer ce produit ?');">
              <button name="supprimer" value="<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
