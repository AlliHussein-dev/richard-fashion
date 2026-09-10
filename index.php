<?php
require_once __DIR__ . '/includes/auth.php';
$pdo = getConnexion();
$titrePage = "Accueil";

// Filtre par categorie
$categorieId = isset($_GET['categorie']) ? (int)$_GET['categorie'] : 0;

$categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll();

if ($categorieId > 0) {
    $stmt = $pdo->prepare(
        "SELECT DISTINCT p.*,
           (SELECT chemin_image FROM produit_images WHERE produit_id = p.id AND est_principale = 1 LIMIT 1) AS image
         FROM produits p
         JOIN produit_categories pc ON pc.produit_id = p.id
         WHERE pc.categorie_id = :cid
         ORDER BY p.date_ajout DESC"
    );
    $stmt->execute([':cid' => $categorieId]);
} else {
    $stmt = $pdo->query(
        "SELECT p.*,
           (SELECT chemin_image FROM produit_images WHERE produit_id = p.id AND est_principale = 1 LIMIT 1) AS image
         FROM produits p ORDER BY p.date_ajout DESC"
    );
}
$produits = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="hero text-center">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 text-lg-start text-center">
        <h1 class="display-4 fw-bold">RICHARD <span class="text-warning">FASHION</span></h1>
        <p class="lead">Habits &amp; accessoires tendance, livres chez vous a Bujumbura</p>
        <a href="#produits" class="btn btn-warning btn-lg mt-2">Decouvrir la collection</a>
      </div>
      <div class="col-lg-6 mt-4 mt-lg-0 text-center">
        <img src="assets/images/hero.png" alt="Style Richard Fashion" class="img-fluid rounded-4 hero-photo">
      </div>
    </div>
  </div>
</div>

<div class="container my-5" id="produits">
  <div class="row">
    <!-- Filtre categories -->
    <div class="col-lg-3 mb-4">
      <div class="card">
        <div class="card-header fw-bold">Categories</div>
        <div class="list-group list-group-flush">
          <a href="index.php" class="list-group-item list-group-item-action <?= $categorieId === 0 ? 'active' : '' ?>">
            Toutes les categories
          </a>
          <?php foreach ($categories as $cat): ?>
            <a href="index.php?categorie=<?= $cat['id'] ?>#produits"
               class="list-group-item list-group-item-action <?= $categorieId === (int)$cat['id'] ? 'active' : '' ?>">
              <?= htmlspecialchars($cat['nom']) ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Grille produits -->
    <div class="col-lg-9">
      <h4 class="mb-3">Nos produits (<?= count($produits) ?>)</h4>
      <div class="row g-4">
        <?php foreach ($produits as $p): ?>
          <div class="col-md-4">
            <div class="card card-produit h-100">
              <img src="<?= htmlspecialchars($p['image'] ?? 'assets/images/produits/produit1_1.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($p['nom']) ?>">
              <div class="card-body d-flex flex-column">
                <h6 class="card-title"><?= htmlspecialchars($p['nom']) ?></h6>
                <p class="text-warning fw-bold mb-2"><?= number_format($p['prix'], 0, ',', ' ') ?> BIF</p>
                <a href="produit.php?id=<?= $p['id'] ?>" class="btn btn-outline-dark btn-sm mt-auto">Voir le produit</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($produits)): ?>
          <p class="text-muted">Aucun produit dans cette categorie.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
