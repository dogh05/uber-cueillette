<?php
// php/profil_agriculteur.php
header('Content-Type: application/json');
require_once 'config.php';

if (!estConnecte() || getTypeUtilisateur() !== 'agriculteur') {
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

$id = getUserId();

try {
    // CORRECTION : CIN est en majuscule dans la table → alias "cin" pour cohérence JS
    $stmt = $pdo->prepare("SELECT nom, prenom, CIN as cin, email, pseudo, adresse FROM agriculteur WHERE id_agriculteur = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode($user);
    } else {
        echo json_encode(['error' => 'Utilisateur introuvable']);
    }
} catch (PDOException $e) {
    error_log("Erreur profil_agriculteur: " . $e->getMessage());
    echo json_encode(['error' => 'Erreur serveur']);
}
?>
