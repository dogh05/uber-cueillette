<?php
// php/noter_ouvrier.php
header('Content-Type: application/json');
require_once 'config.php';

if (!estConnecte() || getTypeUtilisateur() !== 'agriculteur') {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$id_candidate = $_POST['id_candidate'];
$note = $_POST['note'];
$commentaire = $_POST['commentaire'];
$remuneration = $_POST['remuneration'];

// Vérifier que l'agriculteur est bien propriétaire de l'offre
$stmt = $pdo->prepare("UPDATE candidate c
                       JOIN offre o ON c.id_offre = o.id_offre
                       SET c.note = ?, c.commentaire = ?, c.remuneration = ?
                       WHERE c.id_candidate = ? AND o.id_agriculteur = ?");
$result = $stmt->execute([$note, $commentaire, $remuneration, $id_candidate, getUserId()]);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Évaluation enregistrée']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'évaluation']);
}
?>