<?php
require_once __DIR__ . '/includes/auth.php';
$pdo = getConnexion();
$titrePage = "Creer un compte";
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $adresse = trim($_POST['adresse']);
    $mdp = $_POST['mot_de_passe'];

    $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
    $check->execute([':email' => $email]);

    if ($check->fetch()) {
        $erreur = "Cet email est deja utilise.";
    } else {
        $hash = password_hash($mdp, PASSWORD_DEFAULT);
        $pdo->prepare(
            "INSERT INTO utilisateurs (nom, email, mot_de_passe, telephone, adresse, role)
             VALUES (:nom, :email, :mdp, :tel, :adr, 'client')"
        )->execute([':nom' => $nom, ':email' => $email, ':mdp' => $hash, ':tel' => $telephone, ':adr' => $adresse]);

        $utilisateur = ['id' => $pdo->lastInsertId(), 'nom' => $nom, 'role' => 'client'];
        connecter($utilisateur);
        header('Location: index.php');
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container my-5" style="max-width: 480px;">
  <h2 class="mb-4 text-center">Creer un compte client</h2>

  <?php if ($erreur): ?><div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>

  <form method="POST" class="card p-4">
    <div class="mb-3"><label class="form-label">Nom complet</label><input type="text" name="nom" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Telephone</label><input type="text" name="telephone" class="form-control"></div>
    <div class="mb-3"><label class="form-label">Adresse</label><input type="text" name="adresse" class="form-control"></div>
    <div class="mb-3"><label class="form-label">Mot de passe</label><input type="password" name="mot_de_passe" class="form-control" required minlength="6"></div>
    <button type="submit" class="btn btn-dark w-100">Creer mon compte</button>
    <p class="text-center small mt-3 mb-0">Deja inscrit ? <a href="login.php">Connectez-vous</a></p>
  </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
