<?php
// php/profil.php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.html');
    exit;
}

$type = $_SESSION['user_type'];
$id = $_SESSION['user_id'];

if ($type === 'agriculteur') {
    $stmt = $pdo->prepare("SELECT * FROM agriculteur WHERE id_agriculteur = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
} else {
    $stmt = $pdo->prepare("SELECT * FROM ouvrier WHERE id_ouvrier = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon profil - Uber-Cueillette</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; }
        .profile-card { background: white; border-radius: 20px; padding: 30px; max-width: 600px; margin: 50px auto; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .profile-photo { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin: 0 auto 20px; display: block; }
        .btn-modifier { background: #2e7d32; color: white; border: none; padding: 10px 30px; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-card">
            <h2 class="text-center mb-4"><i class="fas fa-user-circle"></i> Mon profil</h2>
            
            <?php if ($type === 'ouvrier' && $user['photo']): ?>
                <img src="data:image/jpeg;base64,<?= base64_encode($user['photo']) ?>" class="profile-photo" alt="Photo">
            <?php else: ?>
                <div class="text-center mb-3"><i class="fas fa-user fa-5x text-muted"></i></div>
            <?php endif; ?>
            
            <table class="table table-borderless">
                <tr><td><strong>Nom :</strong></td><td><?= htmlspecialchars($user['nom']) ?></td></tr>
                <tr><td><strong>Prénom :</strong></td><td><?= htmlspecialchars($user['prenom']) ?></td></tr>
                <tr><td><strong>CIN :</strong></td><td><?= htmlspecialchars($user['CIN']) ?></td></tr>
                <tr><td><strong>Email :</strong></td><td><?= htmlspecialchars($user['email']) ?></td></tr>
                <tr><td><strong>Pseudo :</strong></td><td><?= htmlspecialchars($user['pseudo']) ?></td></tr>
                <?php if ($type === 'agriculteur'): ?>
                    <tr><td><strong>Adresse :</strong></td><td><?= htmlspecialchars($user['adresse']) ?></td></tr>
                <?php else: ?>
                    <tr><td><strong>Description :</strong></td><td><?= htmlspecialchars($user['description']) ?></td></tr>
                <?php endif; ?>
            </table>
            
            <div class="text-center">
                <a href="modifier_profil.php" class="btn-modifier"><i class="fas fa-edit"></i> Modifier mon profil</a>
                <a href="../dashboard_<?= $type ?>.html" class="btn btn-secondary">Retour</a>
            </div>
        </div>
    </div>
</body>
</html>