<?php
// php/supprimer_offre.php
header('Content-Type: application/json');
require_once 'config.php';

if (!estConnecte() || getTypeUtilisateur() !== 'agriculteur') {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$id_offre = $_POST['id_offre'];
$id_agriculteur = getUserId();

// Vérifier qu'il n'y a aucun postulant
$stmt = $pdo->prepare("SELECT COUNT(*) as nb FROM candidate WHERE id_offre = ?");
$stmt->execute([$id_offre]);
$nbPostulants = $stmt->fetch()['nb'];

if ($nbPostulants > 0) {
    echo json_encode(['success' => false, 'message' => 'Impossible de supprimer : des ouvriers ont déjà postulé']);
    exit;
}

// Supprimer l'offre
$stmt = $pdo->prepare("DELETE FROM offre WHERE id_offre = ? AND id_agriculteur = ?");
$result = $stmt->execute([$id_offre, $id_agriculteur]);

if ($result && $stmt->rowCount() > 0) {
    echo json_encode(['success' => true, 'message' => 'Offre supprimée avec succès']);
} else {
    echo json_encode(['success' => false, 'message' => 'Offre non trouvée']);
}
?>