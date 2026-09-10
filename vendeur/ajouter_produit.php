<?php
require_once __DIR__ . '/../includes/auth.php';
exigerRole('vendeur');
$pdo = getConnexion();
$titrePage = "Ajouter un produit";

$categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll();
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $description = trim($_POST['description']);
    $prix = (float)$_POST['prix'];
    $stock = (int)$_POST['stock'];
    $taille = trim($_POST['taille']);
    $couleur = trim($_POST['couleur']);
    $categoriesChoisies = $_POST['categories'] ?? [];

    if ($nom === '' || $prix <= 0 || empty($categoriesChoisies)) {
        $erreur = "Veuillez remplir le nom, un prix valide et selectionner au moins une categorie.";
    } else {
        $pdo->prepare(
            "INSERT INTO produits (vendeur_id, nom, description, prix, stock, taille, couleur)
             VALUES (:vid, :nom, :desc, :prix, :stock, :taille, :couleur)"
        )->execute([
            ':vid' => $_SESSION['user_id'], ':nom' => $nom, ':desc' => $description,
            ':prix' => $prix, ':stock' => $stock, ':taille' => $taille, ':couleur' => $couleur,
        ]);
        $produitId = $pdo->lastInsertId();

        foreach ($categoriesChoisies as $catId) {
            $pdo->prepare("INSERT INTO produit_categories (produit_id, categorie_id) VALUES (:p, :c)")
                ->execute([':p' => $produitId, ':c' => (int)$catId]);
        }

        // Gestion des images uploadees (plusieurs photos)
        if (!empty($_FILES['images']['name'][0])) {
            $dossier = __DIR__ . '/../assets/images/produits/';
            foreach ($_FILES['images']['tmp_name'] as $i => $tmpName) {
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
                    $nomFichier = 'produit_' . $produitId . '_' . ($i + 1) . '.' . $ext;
                    move_uploaded_file($tmpName, $dossier . $nomFichier);
                    $pdo->prepare(
                        "INSERT INTO produit_images (produit_id, chemin_image, est_principale) VALUES (:p, :img, :principale)"
                    )->execute([
                        ':p' => $produitId,
                        ':img' => 'assets/images/produits/' . $nomFichier,
                        ':principale' => $i === 0 ? 1 : 0,
                    ]);
                }
            }
        } else {
            // Image par defaut si aucune image n'est envoyee
            $pdo->prepare(
                "INSERT INTO produit_images (produit_id, chemin_image, est_principale) VALUES (:p, 'assets/images/produits/produit1_1.jpg', 1)"
            )->execute([':p' => $produitId]);
        }

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Produit ajoute avec succes.'];
        header('Location: dashboard.php');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container my-5" style="max-width: 650px;">
  <h2 class="mb-4">Ajouter un produit</h2>

  <?php if ($erreur): ?><div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="card p-4">
    <div class="mb-3"><label class="form-label">Nom du produit</label><input type="text" name="nom" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
    <div class="row">
      <div class="col-6 mb-3"><label class="form-label">Prix (BIF)</label><input type="number" name="prix" class="form-control" required></div>
      <div class="col-6 mb-3"><label class="form-label">Stock</label><input type="number" name="stock" class="form-control" required></div>
    </div>
    <div class="row">
      <div class="col-6 mb-3"><label class="form-label">Taille</label><input type="text" name="taille" class="form-control" placeholder="ex: S-XL"></div>
      <div class="col-6 mb-3"><label class="form-label">Couleur</label><input type="text" name="couleur" class="form-control"></div>
    </div>

    <div class="mb-3">
      <label class="form-label">Categories (au moins une)</label>
      <?php foreach ($categories as $cat): ?>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" id="cat<?= $cat['id'] ?>">
          <label class="form-check-label" for="cat<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></label>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="mb-3">
      <label class="form-label">Photos du produit (plusieurs possibles)</label>
      <input type="file" name="images[]" class="form-control" multiple accept="image/*">
      <div class="form-text">Si aucune photo n'est envoyee, une image par defaut sera utilisee.</div>
    </div>

    <button type="submit" class="btn btn-dark w-100">Enregistrer le produit</button>
  </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
