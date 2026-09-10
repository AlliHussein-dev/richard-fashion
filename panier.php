<?php
require_once __DIR__ . '/includes/auth.php';
exigerRole('client');
$pdo = getConnexion();
$titrePage = "Mon panier";

$panierStmt = $pdo->prepare("SELECT id FROM paniers WHERE client_id = :cid");
$panierStmt->execute([':cid' => $_SESSION['user_id']]);
$panier = $panierStmt->fetch();
$panierId = $panier['id'] ?? null;

// Mise a jour de quantite / suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $panierId) {
    if (isset($_POST['supprimer_ligne'])) {
        $pdo->prepare("DELETE FROM panier_details WHERE id = :id AND panier_id = :pid")
            ->execute([':id' => (int)$_POST['supprimer_ligne'], ':pid' => $panierId]);
    } elseif (isset($_POST['maj_quantite'])) {
        $ligneId = (int)$_POST['ligne_id'];
        $qte = max(1, (int)$_POST['maj_quantite']);
        $pdo->prepare("UPDATE panier_details SET quantite = :q WHERE id = :id AND panier_id = :pid")
            ->execute([':q' => $qte, ':id' => $ligneId, ':pid' => $panierId]);
    }
    header('Location: panier.php');
    exit;
}

$lignes = [];
$total = 0;
if ($panierId) {
    $stmt = $pdo->prepare(
        "SELECT pd.id AS ligne_id, pd.quantite, p.id AS produit_id, p.nom, p.prix, p.stock,
           (SELECT chemin_image FROM produit_images WHERE produit_id = p.id AND est_principale = 1 LIMIT 1) AS image
         FROM panier_details pd
         JOIN produits p ON p.id = pd.produit_id
         WHERE pd.panier_id = :pid"
    );
    $stmt->execute([':pid' => $panierId]);
    $lignes = $stmt->fetchAll();
    foreach ($lignes as $l) {
        $total += $l['prix'] * $l['quantite'];
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container my-5">
  <h2 class="mb-4"><i class="bi bi-cart3"></i> Mon panier</h2>

  <?php if (empty($lignes)): ?>
    <div class="alert alert-info">Votre panier est vide. <a href="index.php">Continuer mes achats</a></div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table align-middle bg-white">
        <thead>
          <tr><th>Produit</th><th>Prix unitaire</th><th>Quantite</th><th>Sous-total</th><th></th></tr>
        </thead>
        <tbody>
        <?php foreach ($lignes as $l): ?>
          <tr>
            <td class="d-flex align-items-center gap-2">
              <img src="<?= htmlspecialchars($l['image']) ?>" width="60" height="70" style="object-fit:cover" class="rounded">
              <?= htmlspecialchars($l['nom']) ?>
            </td>
            <td><?= number_format($l['prix'], 0, ',', ' ') ?> BIF</td>
            <td style="width: 140px;">
              <form method="POST" class="d-flex gap-1">
                <input type="hidden" name="ligne_id" value="<?= $l['ligne_id'] ?>">
                <input type="number" name="maj_quantite" value="<?= $l['quantite'] ?>" min="1" max="<?= $l['stock'] ?>" class="form-control form-control-sm">
                <button class="btn btn-sm btn-outline-secondary">OK</button>
              </form>
            </td>
            <td class="fw-bold"><?= number_format($l['prix'] * $l['quantite'], 0, ',', ' ') ?> BIF</td>
            <td>
              <form method="POST">
                <button name="supprimer_ligne" value="<?= $l['ligne_id'] ?>" class="btn btn-sm btn-outline-danger">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4">
      <h4>Total : <span class="text-warning"><?= number_format($total, 0, ',', ' ') ?> BIF</span></h4>
      <a href="commande.php" class="btn btn-dark btn-lg">Passer la commande <i class="bi bi-arrow-right"></i></a>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
