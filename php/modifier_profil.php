<?php
// php/modifier_profil.php
require_once 'config.php'; // config.php appelle déjà session_start()

if (!estConnecte()) {
    header('Location: ../index.html');
    exit;
}

// CORRECTION : $_SESSION['type'] → $_SESSION['user_type']
$type = $_SESSION['user_type'];
$id   = $_SESSION['user_id'];
$message = '';
$erreur  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Email invalide.";
    } else {
        if ($type === 'agriculteur') {
            $adresse = trim($_POST['adresse'] ?? '');
            $stmt = $pdo->prepare("UPDATE agriculteur SET email = ?, adresse = ? WHERE id_agriculteur = ?");
            $stmt->execute([$email, $adresse, $id]);
        } else {
            $description = trim($_POST['description'] ?? '');
            $stmt = $pdo->prepare("UPDATE ouvrier SET email = ?, description = ? WHERE id_ouvrier = ?");
            $stmt->execute([$email, $description, $id]);
        }
        $message = "Profil mis à jour avec succès !";
    }
}

// Récupérer les données actuelles
if ($type === 'agriculteur') {
    $stmt = $pdo->prepare("SELECT * FROM agriculteur WHERE id_agriculteur = ?");
} else {
    $stmt = $pdo->prepare("SELECT * FROM ouvrier WHERE id_ouvrier = ?");
}
$stmt->execute([$id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier mon profil - Uber-Cueillette</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f5f5f5; }
        .card { border-radius: 15px; }
        .card-header { border-radius: 15px 15px 0 0 !important; }
    </style>
</head>
<body>
    <div class="container" style="max-width:600px; margin-top:50px;">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h4><i class="fas fa-edit"></i> Modifier mon profil</h4>
            </div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <?php if ($erreur): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>

                    <?php if ($type === 'agriculteur'): ?>
                        <div class="mb-3">
                            <label class="form-label">Adresse</label>
                            <textarea name="adresse" class="form-control" rows="2"><?= htmlspecialchars($user['adresse'] ?? '') ?></textarea>
                        </div>
                    <?php else: ?>
                        <div class="mb-3">
                            <label class="form-label">Description (niveau éducatif, expérience)</label>
                            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($user['description'] ?? '') ?></textarea>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                    <a href="../dashboard_<?= $type ?>.html" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
