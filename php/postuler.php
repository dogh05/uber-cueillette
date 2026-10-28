<?php
// php/postuler.php
header('Content-Type: application/json');
require_once 'config.php';

if (!estConnecte() || getTypeUtilisateur() !== 'ouvrier') {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$id_offre = $_POST['id_offre'];
$id_ouvrier = getUserId();

try {
    // Vérifier si a déjà postulé
    $stmt = $pdo->prepare("SELECT id_candidate FROM candidate WHERE id_offre = ? AND id_ouvrier = ?");
    $stmt->execute([$id_offre, $id_ouvrier]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Vous avez déjà postulé à cette offre']);
        exit;
    }
    
    $stmt = $pdo->prepare("INSERT INTO candidate (id_offre, id_ouvrier, decision) VALUES (?, ?, 'en_attente')");
    $stmt->execute([$id_offre, $id_ouvrier]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la candidature']);
}
?>