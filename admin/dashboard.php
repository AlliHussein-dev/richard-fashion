<?php
require_once __DIR__ . '/../includes/auth.php';
exigerRole('administrateur');
$pdo = getConnexion();
$titrePage = "Administration";

$nbProduits = $pdo->query("SELECT COUNT(*) c FROM produits")->fetch()['c'];
$nbCommandes = $pdo->query("SELECT COUNT(*) c FROM commandes")->fetch()['c'];
$nbClients = $pdo->query("SELECT COUNT(*) c FROM utilisateurs WHERE role='client'")->fetch()['c'];
$nbVendeurs = $pdo->query("SELECT COUNT(*) c FROM utilisateurs WHERE role='vendeur'")->fetch()['c'];
$chiffreAffaires = $pdo->query("SELECT COALESCE(SUM(total),0) t FROM commandes WHERE statut != 'annulee'")->fetch()['t'];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container my-5">
  <h2 class="mb-4">Tableau de bord Administrateur</h2>

  <div class="row g-3 mb-5">
    <div class="col-md-3"><div class="card text-center p-3"><h3><?= $nbProduits ?></h3><p class="text-muted mb-0">Produits</p></div></div>
    <div class="col-md-3"><div class="card text-center p-3"><h3><?= $nbCommandes ?></h3><p class="text-muted mb-0">Commandes</p></div></div>
    <div class="col-md-3"><div class="card text-center p-3"><h3><?= $nbClients ?></h3><p class="text-muted mb-0">Clients</p></div></div>
    <div class="col-md-3"><div class="card text-center p-3"><h3><?= number_format($chiffreAffaires,0,',',' ') ?></h3><p class="text-muted mb-0">BIF de ventes</p></div></div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-md-4">
      <a href="produits.php" class="text-decoration-none">
        <div class="card p-4 text-center"><i class="bi bi-bag fs-1 text-dark"></i><h5 class="mt-2">Gerer les produits</h5></div>
      </a>
    </div>
    <div class="col-md-4">
      <a href="commandes.php" class="text-decoration-none">
        <div class="card p-4 text-center"><i class="bi bi-receipt fs-1 text-dark"></i><h5 class="mt-2">Gerer les commandes</h5></div>
      </a>
    </div>
    <div class="col-md-4">
      <a href="utilisateurs.php" class="text-decoration-none">
        <div class="card p-4 text-center"><i class="bi bi-people fs-1 text-dark"></i><h5 class="mt-2">Gerer les utilisateurs</h5></div>
      </a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
