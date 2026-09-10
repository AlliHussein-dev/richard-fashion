<?php
if (session_status() === PHP_SESSION_NONE) require_once __DIR__ . '/auth.php';
$user = utilisateurCourant();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($titrePage) ? htmlspecialchars($titrePage) . ' - ' : '' ?>Richard Fashion</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">RICHARD <span class="text-warning">FASHION</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php#produits">Produits</a></li>
        <?php if ($user && $user['role'] === 'client'): ?>
          <li class="nav-item"><a class="nav-link" href="mes_commandes.php">Mes commandes</a></li>
        <?php endif; ?>
        <?php if ($user && $user['role'] === 'vendeur'): ?>
          <li class="nav-item"><a class="nav-link" href="vendeur/dashboard.php">Espace vendeur</a></li>
        <?php endif; ?>
        <?php if ($user && $user['role'] === 'administrateur'): ?>
          <li class="nav-item"><a class="nav-link" href="admin/dashboard.php">Administration</a></li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav">
        <?php if ($user && $user['role'] === 'client'): ?>
          <li class="nav-item">
            <a class="nav-link" href="panier.php"><i class="bi bi-cart3"></i> Panier</a>
          </li>
        <?php endif; ?>
        <?php if ($user): ?>
          <li class="nav-item"><span class="nav-link text-warning">Bonjour, <?= htmlspecialchars($user['nom']) ?></span></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Deconnexion</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Connexion</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php">Creer un compte</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<?php if (!empty($_SESSION['flash'])): ?>
  <div class="container mt-3">
    <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show">
      <?= htmlspecialchars($_SESSION['flash']['message']) ?>
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
