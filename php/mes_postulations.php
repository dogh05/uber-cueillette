<?php
// php/mes_postulations.php
header('Content-Type: application/json');
require_once 'config.php';

if (!estConnecte() || getTypeUtilisateur() !== 'ouvrier') {
    echo json_encode([]);
    exit;
}

$id_ouvrier = getUserId();

// CORRECTION : suppression des colonnes dupliquées o.fruit / o.gouvernorat
// (ces colonnes n'existent pas dans la table offre, seuls les alias tf/g existent)
$sql = "SELECT c.id_candidate,
               c.id_offre,
               c.id_ouvrier,
               c.decision,
               c.date_candidature,
               c.note,
               c.commentaire,
               c.remuneration,
               o.prix_journee,
               o.date_debut,
               o.date_fin,
               o.adresse,
               tf.libelle AS fruit,
               g.libelle  AS gouvernorat
        FROM candidate c
        JOIN offre       o  ON c.id_offre        = o.id_offre
        JOIN type_fruit  tf ON o.id_type_fruit    = tf.id_type_fruit
        JOIN gouvernement g ON o.id_gouvernorat   = g.id_gouvernorat
        WHERE c.id_ouvrier = ?
        ORDER BY c.date_candidature DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_ouvrier]);
    $postulations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($postulations);
} catch (PDOException $e) {
    error_log("Erreur mes_postulations: " . $e->getMessage());
    echo json_encode([]);
}
?>
