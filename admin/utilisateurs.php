<?php
require_once __DIR__ . '/../includes/auth.php';
exigerRole('administrateur');
$pdo = getConnexion();
$titrePage = "Gestion des utilisateurs";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer'])) {
    if ((int)$_POST['supprimer'] !== $_SESSION['user_id']) {
        $pdo->prepare("DELETE FROM utilisateurs WHERE id = :id")->execute([':id' => (int)$_POST['supprimer']]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Utilisateur supprime.'];
    }
    header('Location: utilisateurs.php');
    exit;
}

$utilisateurs = $pdo->query("SELECT * FROM utilisateurs ORDER BY role, nom")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container my-5">
  <h2 class="mb-4">Gestion des utilisateurs (<?= count($utilisateurs) ?>)</h2>

  <div class="table-responsive">
    <table class="table bg-white align-middle">
      <thead><tr><th>Nom</th><th>Email</th><th>Role</th><th>Inscrit le</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($utilisateurs as $u): ?>
        <tr>
          <td><?= htmlspecialchars($u['nom']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><span class="badge bg-<?= $u['role']==='administrateur'?'danger':($u['role']==='vendeur'?'warning text-dark':'secondary') ?>"><?= htmlspecialchars($u['role']) ?></span></td>
          <td><?= date('d/m/Y', strtotime($u['date_inscription'])) ?></td>
          <td>
            <?php if ((int)$u['id'] !== $_SESSION['user_id']): ?>
              <form method="POST" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                <button name="supprimer" value="<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
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
