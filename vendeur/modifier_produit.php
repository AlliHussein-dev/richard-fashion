<?php
require_once __DIR__ . '/../includes/auth.php';
exigerRole('vendeur');
$pdo = getConnexion();
$titrePage = "Modifier le produit";

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM produits WHERE id = :id AND vendeur_id = :vid");
$stmt->execute([':id' => $id, ':vid' => $_SESSION['user_id']]);
$produit = $stmt->fetch();

if (!$produit) { header('Location: dashboard.php'); exit; }

$categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll();
$catActuelles = $pdo->prepare("SELECT categorie_id FROM produit_categories WHERE produit_id = :id");
$catActuelles->execute([':id' => $id]);
$catActuelles = array_column($catActuelles->fetchAll(), 'categorie_id');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->prepare(
        "UPDATE produits SET nom = :nom, description = :desc, prix = :prix, stock = :stock, taille = :taille, couleur = :couleur WHERE id = :id"
    )->execute([
        ':nom' => trim($_POST['nom']), ':desc' => trim($_POST['description']),
        ':prix' => (float)$_POST['prix'], ':stock' => (int)$_POST['stock'],
        ':taille' => trim($_POST['taille']), ':couleur' => trim($_POST['couleur']), ':id' => $id,
    ]);

    $pdo->prepare("DELETE FROM produit_categories WHERE produit_id = :id")->execute([':id' => $id]);
    foreach (($_POST['categories'] ?? []) as $catId) {
        $pdo->prepare("INSERT INTO produit_categories (produit_id, categorie_id) VALUES (:p, :c)")
            ->execute([':p' => $id, ':c' => (int)$catId]);
    }

    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Produit mis a jour.'];
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container my-5" style="max-width: 650px;">
  <h2 class="mb-4">Modifier : <?= htmlspecialchars($produit['nom']) ?></h2>

  <form method="POST" class="card p-4">
    <div class="mb-3"><label class="form-label">Nom</label><input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($produit['nom']) ?>" required></div>
    <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($produit['description']) ?></textarea></div>
    <div class="row">
      <div class="col-6 mb-3"><label class="form-label">Prix (BIF)</label><input type="number" name="prix" class="form-control" value="<?= $produit['prix'] ?>" required></div>
      <div class="col-6 mb-3"><label class="form-label">Stock</label><input type="number" name="stock" class="form-control" value="<?= $produit['stock'] ?>" required></div>
    </div>
    <div class="row">
      <div class="col-6 mb-3"><label class="form-label">Taille</label><input type="text" name="taille" class="form-control" value="<?= htmlspecialchars($produit['taille']) ?>"></div>
      <div class="col-6 mb-3"><label class="form-label">Couleur</label><input type="text" name="couleur" class="form-control" value="<?= htmlspecialchars($produit['couleur']) ?>"></div>
    </div>

    <div class="mb-3">
      <label class="form-label">Categories</label>
      <?php foreach ($categories as $cat): ?>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="categories[]" value="<?= $cat['id'] ?>"
                 id="cat<?= $cat['id'] ?>" <?= in_array($cat['id'], $catActuelles) ? 'checked' : '' ?>>
          <label class="form-check-label" for="cat<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></label>
        </div>
      <?php endforeach; ?>
    </div>

    <button type="submit" class="btn btn-dark w-100">Enregistrer les modifications</button>
  </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
