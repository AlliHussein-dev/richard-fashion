<?php
require_once __DIR__ . '/includes/auth.php';
$pdo = getConnexion();
$titrePage = "Connexion";
$erreurConnexion = '';
$erreurInscription = '';
$ongletActif = 'connexion';

// --- Connexion ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_connexion'])) {
    $email = trim($_POST['email']);
    $mdp = $_POST['mot_de_passe'];

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $utilisateur = $stmt->fetch();

    if ($utilisateur && password_verify($mdp, $utilisateur['mot_de_passe'])) {
        connecter($utilisateur);
        header('Location: index.php');
        exit;
    } else {
        $erreurConnexion = "Email ou mot de passe incorrect.";
    }
}

// --- Inscription (client uniquement) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_inscription'])) {
    $ongletActif = 'inscription';
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $adresse = trim($_POST['adresse']);
    $mdp = $_POST['mot_de_passe'];

    $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
    $check->execute([':email' => $email]);

    if ($check->fetch()) {
        $erreurInscription = "Cet email est deja utilise.";
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

<div class="container my-5" style="max-width: 460px;">

  <ul class="nav nav-pills nav-justified mb-4" id="ongletsAuth">
    <li class="nav-item">
      <button class="nav-link <?= $ongletActif === 'connexion' ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#tab-connexion" type="button">
        <i class="bi bi-box-arrow-in-right"></i> Connexion
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link <?= $ongletActif === 'inscription' ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#tab-inscription" type="button">
        <i class="bi bi-person-plus"></i> Inscription
      </button>
    </li>
  </ul>

  <div class="tab-content">

    <!-- Onglet Connexion -->
    <div class="tab-pane fade <?= $ongletActif === 'connexion' ? 'show active' : '' ?>" id="tab-connexion">
      <?php if ($erreurConnexion): ?><div class="alert alert-danger"><?= htmlspecialchars($erreurConnexion) ?></div><?php endif; ?>

      <form method="POST" class="card p-4">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Mot de passe</label>
          <input type="password" name="mot_de_passe" class="form-control" required>
        </div>
        <button type="submit" name="action_connexion" class="btn btn-dark w-100">Se connecter</button>
        <p class="text-center small mt-3 mb-0">Pas encore de compte ? Utilisez l'onglet <strong>Inscription</strong> ci-dessus.</p>
      </form>

      <div class="alert alert-secondary small mt-3">
        <strong>Comptes de demonstration</strong><br>
        Admin : richard@richardfashion.bi / 1234<br>
        Admin : espoir@richardfashion.bi / 000<br>
        Vendeur : vendeur1@richardfashion.bi / password123<br>
        Client : client1@richardfashion.bi / password123
      </div>
    </div>

    <!-- Onglet Inscription -->
    <div class="tab-pane fade <?= $ongletActif === 'inscription' ? 'show active' : '' ?>" id="tab-inscription">
      <?php if ($erreurInscription): ?><div class="alert alert-danger"><?= htmlspecialchars($erreurInscription) ?></div><?php endif; ?>

      <form method="POST" class="card p-4">
        <div class="mb-3"><label class="form-label">Nom complet</label><input type="text" name="nom" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Telephone</label><input type="text" name="telephone" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Adresse</label><input type="text" name="adresse" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Mot de passe</label><input type="password" name="mot_de_passe" class="form-control" required minlength="4"></div>
        <button type="submit" name="action_inscription" class="btn btn-dark w-100">Creer mon compte client</button>
      </form>
    </div>

  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
