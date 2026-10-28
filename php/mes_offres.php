<?php
// php/mes_offres.php
header('Content-Type: application/json');
require_once 'config.php';

if (!estConnecte() || getTypeUtilisateur() !== 'agriculteur') {
    echo json_encode([]);
    exit;
}

$id_agriculteur = getUserId();

// CORRECTION : created_at n'existe pas dans la table offre → ORDER BY date_limitte DESC
$sql = "SELECT o.*, tf.libelle as fruit, g.libelle as gouvernorat,
        (SELECT COUNT(*) FROM candidate c WHERE c.id_offre = o.id_offre) as nb_postulants
        FROM offre o
        JOIN type_fruit tf ON o.id_type_fruit = tf.id_type_fruit
        JOIN gouvernement g ON o.id_gouvernorat = g.id_gouvernorat
        WHERE o.id_agriculteur = ?
        ORDER BY o.date_limitte DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_agriculteur]);
    $offres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($offres);
} catch (PDOException $e) {
    error_log("Erreur mes_offres: " . $e->getMessage());
    echo json_encode([]);
}
?>
