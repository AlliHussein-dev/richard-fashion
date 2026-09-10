<?php
require_once __DIR__ . '/includes/auth.php';
exigerRole('client');
$pdo = getConnexion();
$titrePage = "Passer la commande";

$panierStmt = $pdo->prepare("SELECT id FROM paniers WHERE client_id = :cid");
$panierStmt->execute([':cid' => $_SESSION['user_id']]);
$panier = $panierStmt->fetch();

if (!$panier) { header('Location: panier.php'); exit; }

$stmt = $pdo->prepare(
    "SELECT pd.quantite, p.id AS produit_id, p.nom, p.prix, p.stock
     FROM panier_details pd JOIN produits p ON p.id = pd.produit_id
     WHERE pd.panier_id = :pid"
);
$stmt->execute([':pid' => $panier['id']]);
$lignes = $stmt->fetchAll();

if (empty($lignes)) { header('Location: panier.php'); exit; }

$total = 0;
foreach ($lignes as $l) $total += $l['prix'] * $l['quantite'];

$userStmt = $pdo->prepare("SELECT adresse, telephone FROM utilisateurs WHERE id = :id");
$userStmt->execute([':id' => $_SESSION['user_id']]);
$userInfo = $userStmt->fetch();

// Validation : creer la commande puis rediriger vers le paiement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adresse = trim($_POST['adresse']);
    if ($adresse === '') {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Veuillez indiquer une adresse de livraison.'];
        header('Location: commande.php');
        exit;
    }

    $pdo->beginTransaction();
    try {
        $pdo->prepare(
            "INSERT INTO commandes (client_id, statut, adresse_livraison, total) VALUES (:cid, 'en_attente', :adresse, :total)"
        )->execute([':cid' => $_SESSION['user_id'], ':adresse' => $adresse, ':total' => $total]);

        $commandeId = $pdo->lastInsertId();

        $detailStmt = $pdo->prepare(
            "INSERT INTO commande_details (commande_id, produit_id, quantite, prix_unitaire) VALUES (:cmd, :prod, :q, :prix)"
        );
        foreach ($lignes as $l) {
            $detailStmt->execute([
                ':cmd' => $commandeId, ':prod' => $l['produit_id'],
                ':q' => $l['quantite'], ':prix' => $l['prix'],
            ]);
            // decrement du stock
            $pdo->prepare("UPDATE produits SET stock = GREATEST(0, stock - :q) WHERE id = :id")
                ->execute([':q' => $l['quantite'], ':id' => $l['produit_id']]);
        }

        // Vider le panier
        $pdo->prepare("DELETE FROM panier_details WHERE panier_id = :pid")->execute([':pid' => $panier['id']]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur lors de la creation de la commande.'];
        header('Location: commande.php');
        exit;
    }

    header('Location: paiement.php?commande=' . $commandeId);
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container my-5">
  <h2 class="mb-4">Recapitulatif de la commande</h2>

  <div class="row g-4">
    <div class="col-md-7">
      <div class="card">
        <div class="card-body">
          <?php foreach ($lignes as $l): ?>
            <div class="d-flex justify-content-between border-bottom py-2">
              <span><?= htmlspecialchars($l['nom']) ?> x <?= $l['quantite'] ?></span>
              <span><?= number_format($l['prix'] * $l['quantite'], 0, ',', ' ') ?> BIF</span>
            </div>
          <?php endforeach; ?>
          <div class="d-flex justify-content-between pt-3 fw-bold fs-5">
            <span>Total</span>
            <span class="text-warning"><?= number_format($total, 0, ',', ' ') ?> BIF</span>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-5">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Adresse de livraison</h5>
          <form method="POST">
            <textarea name="adresse" class="form-control mb-3" rows="3" required placeholder="Quartier, avenue, ville..."><?= htmlspecialchars($userInfo['adresse'] ?? '') ?></textarea>
            <p class="small text-muted">Telephone de contact : <?= htmlspecialchars($userInfo['telephone'] ?? 'non renseigne') ?></p>
            <button type="submit" class="btn btn-dark w-100">Continuer vers le paiement <i class="bi bi-arrow-right"></i></button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
