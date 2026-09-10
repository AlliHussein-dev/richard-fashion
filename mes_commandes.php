<?php
require_once __DIR__ . '/includes/auth.php';
exigerRole('client');
$pdo = getConnexion();
$titrePage = "Mes commandes";

$stmt = $pdo->prepare(
    "SELECT c.*, l.statut AS statut_livraison
     FROM commandes c
     LEFT JOIN livraisons l ON l.commande_id = c.id
     WHERE c.client_id = :cid ORDER BY c.date_commande DESC"
);
$stmt->execute([':cid' => $_SESSION['user_id']]);
$commandes = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container my-5">
  <h2 class="mb-4">Mes commandes</h2>

  <?php if (empty($commandes)): ?>
    <div class="alert alert-info">Vous n'avez pas encore passe de commande.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table bg-white align-middle">
        <thead><tr><th>#</th><th>Date</th><th>Total</th><th>Statut</th><th>Livraison</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($commandes as $c): ?>
          <tr>
            <td>#<?= $c['id'] ?></td>
            <td><?= date('d/m/Y', strtotime($c['date_commande'])) ?></td>
            <td><?= number_format($c['total'], 0, ',', ' ') ?> BIF</td>
            <td><span class="badge bg-dark"><?= htmlspecialchars(str_replace('_',' ', $c['statut'])) ?></span></td>
            <td><?= $c['statut_livraison'] ? htmlspecialchars(str_replace('_',' ', $c['statut_livraison'])) : '-' ?></td>
            <td>
              <?php if ($c['statut_livraison']): ?>
                <a href="livraison.php?commande=<?= $c['id'] ?>" class="btn btn-sm btn-outline-dark">Suivre</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
