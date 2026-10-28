<?php
// php/profil_ouvrier.php  (NOUVEAU FICHIER — nécessaire pour le dashboard)
header('Content-Type: application/json');
require_once 'config.php';

if (!estConnecte() || getTypeUtilisateur() !== 'ouvrier') {
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

$id = getUserId();

try {
    $stmt = $pdo->prepare("SELECT nom, prenom, CIN, email, pseudo, description, photo FROM ouvrier WHERE id_ouvrier = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Encoder la photo en base64 pour l'affichage
        $user['photo'] = $user['photo'] ? base64_encode($user['photo']) : null;
        echo json_encode($user);
    } else {
        echo json_encode(['error' => 'Utilisateur introuvable']);
    }
} catch (PDOException $e) {
    error_log("Erreur profil_ouvrier: " . $e->getMessage());
    echo json_encode(['error' => 'Erreur serveur']);
}
?>
