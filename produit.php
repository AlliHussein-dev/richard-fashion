<?php
require_once __DIR__ . '/includes/auth.php';
$pdo = getConnexion();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT p.*, u.nom AS vendeur_nom FROM produits p JOIN utilisateurs u ON u.id = p.vendeur_id WHERE p.id = :id");
$stmt->execute([':id' => $id]);
$produit = $stmt->fetch();

if (!$produit) {
    header('Location: index.php');
    exit;
}

$titrePage = $produit['nom'];

$images = $pdo->prepare("SELECT * FROM produit_images WHERE produit_id = :id ORDER BY est_principale DESC");
$images->execute([':id' => $id]);
$images = $images->fetchAll();

$categories = $pdo->prepare(
    "SELECT c.* FROM categories c
     JOIN produit_categories pc ON pc.categorie_id = c.id
     WHERE pc.produit_id = :id"
);
$categories->execute([':id' => $id]);
$categories = $categories->fetchAll();

// Ajout au panier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_panier'])) {
    exigerRole('client');
    $quantite = max(1, (int)$_POST['quantite']);

    $panierStmt = $pdo->prepare("SELECT id FROM paniers WHERE client_id = :cid");
    $panierStmt->execute([':cid' => $_SESSION['user_id']]);
    $panier = $panierStmt->fetch();

    if (!$panier) {
        $pdo->prepare("INSERT INTO paniers (client_id) VALUES (:cid)")->execute([':cid' => $_SESSION['user_id']]);
        $panierId = $pdo->lastInsertId();
    } else {
        $panierId = $panier['id'];
    }

    $ligneStmt = $pdo->prepare("SELECT id, quantite FROM panier_details WHERE panier_id = :pid AND produit_id = :prodid");
    $ligneStmt->execute([':pid' => $panierId, ':prodid' => $id]);
    $ligne = $ligneStmt->fetch();

    if ($ligne) {
        $pdo->prepare("UPDATE panier_details SET quantite = quantite + :q WHERE id = :lid")
            ->execute([':q' => $quantite, ':lid' => $ligne['id']]);
    } else {
        $pdo->prepare("INSERT INTO panier_details (panier_id, produit_id, quantite) VALUES (:pid, :prodid, :q)")
            ->execute([':pid' => $panierId, ':prodid' => $id, ':q' => $quantite]);
    }

    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Produit ajoute au panier.'];
    header('Location: panier.php');
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container my-5">
  <div class="row g-5">
    <div class="col-md-6">
      <div id="carousel" class="carousel slide">
        <div class="carousel-inner rounded shadow-sm">
          <?php foreach ($images as $i => $img): ?>
            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
              <img src="<?= htmlspecialchars($img['chemin_image']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($produit['nom']) ?>">
            </div>
          <?php endforeach; ?>
        </div>
        <?php if (count($images) > 1): ?>
          <button class="carousel-control-prev" type="button" data-bs-target="#carousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
        <?php endif; ?>
      </div>
    </div>

    <div class="col-md-6">
      <h2><?= htmlspecialchars($produit['nom']) ?></h2>
      <p class="text-muted">Vendu par <?= htmlspecialchars($produit['vendeur_nom']) ?></p>

      <div class="mb-3">
        <?php foreach ($categories as $cat): ?>
          <span class="badge bg-secondary badge-categorie"><?= htmlspecialchars($cat['nom']) ?></span>
        <?php endforeach; ?>
      </div>

      <h3 class="text-warning fw-bold"><?= number_format($produit['prix'], 0, ',', ' ') ?> BIF</h3>
      <p><?= nl2br(htmlspecialchars($produit['description'])) ?></p>

      <ul class="list-unstyled small text-muted">
        <li>Taille disponible : <?= htmlspecialchars($produit['taille']) ?></li>
        <li>Couleur : <?= htmlspecialchars($produit['couleur']) ?></li>
        <li>Stock disponible : <?= (int)$produit['stock'] ?> unite(s)</li>
      </ul>

      <?php if (estConnecte() && utilisateurCourant()['role'] === 'client'): ?>
        <form method="POST" class="d-flex gap-2 align-items-center mt-4">
          <input type="number" name="quantite" value="1" min="1" max="<?= (int)$produit['stock'] ?>" class="form-control" style="width: 100px;">
          <button type="submit" name="ajouter_panier" class="btn btn-dark px-4">
            <i class="bi bi-cart-plus"></i> Ajouter au panier
          </button>
        </form>
      <?php elseif (!estConnecte()): ?>
        <a href="login.php" class="btn btn-dark mt-4">Connectez-vous pour acheter</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
