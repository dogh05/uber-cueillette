<?php
// php/gerer_candidatures.php
require_once 'config.php';

if (!estConnecte() || getTypeUtilisateur() !== 'agriculteur') {
    header('Location: ../index.html');
    exit;
}

$id_offre = $_GET['id_offre'] ?? 0;
$id_agriculteur = getUserId();

// Vérifier que l'offre appartient bien à l'agriculteur
$stmt = $pdo->prepare("SELECT id_offre, nombre_ouvriers FROM offre WHERE id_offre = ? AND id_agriculteur = ?");
$stmt->execute([$id_offre, $id_agriculteur]);
$offre = $stmt->fetch();

if (!$offre) {
    die("Offre non trouvée");
}

// Traitement de l'acceptation/refus
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_candidate = $_POST['id_candidate'];
    $decision = $_POST['decision'];
    
    // Vérifier le nombre d'ouvriers déjà acceptés
    $stmt = $pdo->prepare("SELECT COUNT(*) as nb FROM candidate WHERE id_offre = ? AND decision = 'accepte'");
    $stmt->execute([$id_offre]);
    $nbAcceptes = $stmt->fetch()['nb'];
    
    if ($decision === 'accepte' && $nbAcceptes >= $offre['nombre_ouvriers']) {
        $error = "Nombre d'ouvriers maximum atteint pour cette offre";
    } else {
        $stmt = $pdo->prepare("UPDATE candidate SET decision = ? WHERE id_candidate = ?");
        $stmt->execute([$decision, $id_candidate]);
        $success = "Décision enregistrée";
    }
    
    // Redirection pour éviter la resoumission
    header("Location: gerer_candidatures.php?id_offre=$id_offre" . (isset($success) ? "&success=" . urlencode($success) : "&error=" . urlencode($error)));
    exit;
}

// Récupérer les candidatures avec les infos des ouvriers
$sql = "SELECT c.*, o.nom, o.prenom, o.description, o.photo,
        (SELECT AVG(note) FROM candidate WHERE id_ouvrier = o.id_ouvrier AND note IS NOT NULL) as moyenne_notes,
        (SELECT COUNT(*) FROM candidate WHERE id_ouvrier = o.id_ouvrier AND note IS NOT NULL) as nb_evaluations
        FROM candidate c
        JOIN ouvrier o ON c.id_ouvrier = o.id_ouvrier
        WHERE c.id_offre = ?
        ORDER BY c.date_candidature DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_offre]);
$candidatures = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les candidatures - Uber-Cueillette</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; }
        .container { padding: 30px; }
        .candidat-card { background: white; border-radius: 15px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .photo-profil { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; background: #e0e0e0; display: flex; align-items: center; justify-content: center; }
        .note-moyenne { color: #ff9800; font-weight: bold; }
        .btn-accepter { background: #4caf50; color: white; border: none; padding: 8px 20px; border-radius: 8px; }
        .btn-refuser { background: #dc3545; color: white; border: none; padding: 8px 20px; border-radius: 8px; }
        .btn-retour { background: #6c757d; color: white; padding: 10px 25px; border-radius: 10px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-users"></i> Gestion des candidatures</h2>
            <a href="../dashboard_agriculteur.html" class="btn-retour"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Offre : <?= $offre['nombre_ouvriers'] ?> ouvriers demandés
        </div>
        
        <?php if (count($candidatures) === 0): ?>
            <div class="alert alert-warning">Aucune candidature pour cette offre.</div>
        <?php else: ?>
            <?php foreach ($candidatures as $c): ?>
                <div class="candidat-card">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <?php if ($c['photo']): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($c['photo']) ?>" class="photo-profil" alt="Photo">
                            <?php else: ?>
                                <div class="photo-profil"><i class="fas fa-user fa-3x text-muted"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <h5><?= htmlspecialchars($c['prenom']) ?> <?= htmlspecialchars($c['nom']) ?></h5>
                            <p class="mb-1"><small><?= htmlspecialchars($c['description']) ?></small></p>
                            <div class="note-moyenne">
                                <i class="fas fa-star"></i> Moyenne: <?= $c['moyenne_notes'] ? number_format($c['moyenne_notes'], 1) : 'Non évalué' ?>
                                (<?= $c['nb_evaluations'] ?> avis)
                            </div>
                        </div>
                        <div class="col-md-3">
                            <span class="badge bg-<?= $c['decision'] === 'accepte' ? 'success' : ($c['decision'] === 'refuse' ? 'danger' : 'warning') ?>">
                                <?= $c['decision'] === 'accepte' ? 'Accepté' : ($c['decision'] === 'refuse' ? 'Refusé' : 'En attente') ?>
                            </span>
                        </div>
                        <div class="col-md-3 text-end">
                            <?php if ($c['decision'] === 'en_attente'): ?>
                                <form method="POST" style="display: inline-block;">
                                    <input type="hidden" name="id_candidate" value="<?= $c['id_candidate'] ?>">
                                    <button type="submit" name="decision" value="accepte" class="btn-accepter m-1">
                                        <i class="fas fa-check"></i> Accepter
                                    </button>
                                    <button type="submit" name="decision" value="refuse" class="btn-refuser m-1">
                                        <i class="fas fa-times"></i> Refuser
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>