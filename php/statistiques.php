<?php
// php/statistiques.php
// Retourne les vraies statistiques pour la page d'accueil
header('Content-Type: application/json');
require_once 'config.php';

try {
    $stats = [];

    // Nombre d'ouvriers inscrits
    $stmt = $pdo->query("SELECT COUNT(*) FROM ouvrier");
    $stats['nb_ouvriers'] = (int)$stmt->fetchColumn();

    // Nombre d'agriculteurs inscrits
    $stmt = $pdo->query("SELECT COUNT(*) FROM agriculteur");
    $stats['nb_agriculteurs'] = (int)$stmt->fetchColumn();

    // Nombre d'offres publiées (toutes, pas seulement actives)
    $stmt = $pdo->query("SELECT COUNT(*) FROM offre");
    $stats['nb_offres'] = (int)$stmt->fetchColumn();

    // Nombre de missions réalisées = candidatures acceptées ET notées (chantier terminé)
    $stmt = $pdo->query("SELECT COUNT(*) FROM candidate WHERE decision = 'accepte' AND note IS NOT NULL");
    $stats['nb_missions'] = (int)$stmt->fetchColumn();

    echo json_encode($stats);

} catch (PDOException $e) {
    error_log("Erreur statistiques: " . $e->getMessage());
    // En cas d'erreur, retourner des valeurs à 0 plutôt qu'une erreur visible
    echo json_encode([
        'nb_ouvriers'     => 0,
        'nb_agriculteurs' => 0,
        'nb_offres'       => 0,
        'nb_missions'     => 0
    ]);
}
?>
