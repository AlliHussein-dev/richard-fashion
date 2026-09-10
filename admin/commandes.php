<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../services/LivraisonService.php';
exigerRole('administrateur');
$pdo = getConnexion();
$titrePage = "Gestion des commandes";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['livraison_id'])) {
    LivraisonService::mettreAJourStatut($pdo, (int)$_POST['livraison_id'], $_POST['nouveau_statut']);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Statut de livraison mis a jour.'];
    header('Location: commandes.php');
    exit;
}

$commandes = $pdo->query(
    "SELECT c.*, u.nom AS client_nom, l.id AS livraison_id, l.statut AS statut_livraison
     FROM commandes c
     JOIN utilisateurs u ON u.id = c.client_id
     LEFT JOIN livraisons l ON l.commande_id = c.id
     ORDER BY c.date_commande DESC"
)->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container my-5">
  <h2 class="mb-4">Gestion des commandes (<?= count($commandes) ?>)</h2>

  <div class="table-responsive">
    <table class="table bg-white align-middle">
      <thead><tr><th>#</th><th>Client</th><th>Date</th><th>Total</th><th>Statut commande</th><th>Livraison</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($commandes as $c): ?>
        <tr>
          <td>#<?= $c['id'] ?></td>
          <td><?= htmlspecialchars($c['client_nom']) ?></td>
          <td><?= date('d/m/Y', strtotime($c['date_commande'])) ?></td>
          <td><?= number_format($c['total'],0,',',' ') ?> BIF</td>
          <td><span class="badge bg-dark"><?= htmlspecialchars(str_replace('_',' ',$c['statut'])) ?></span></td>
          <td>
            <?php if ($c['livraison_id']): ?>
              <span class="badge bg-secondary"><?= htmlspecialchars(str_replace('_',' ',$c['statut_livraison'])) ?></span>
            <?php else: ?>
              <span class="text-muted">-</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($c['livraison_id'] && $c['statut_livraison'] !== 'livree'): ?>
              <form method="POST" class="d-flex gap-1">
                <input type="hidden" name="livraison_id" value="<?= $c['livraison_id'] ?>">
                <select name="nouveau_statut" class="form-select form-select-sm">
                  <option value="en_preparation" <?= $c['statut_livraison']==='en_preparation'?'selected':'' ?>>En preparation</option>
                  <option value="en_cours" <?= $c['statut_livraison']==='en_cours'?'selected':'' ?>>En cours</option>
                  <option value="livree">Livree</option>
                </select>
                <button class="btn btn-sm btn-dark">OK</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
