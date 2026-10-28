<?php
// php/lister_candidatures.php
header('Content-Type: application/json'); // CORRECTION : header JSON manquant
require_once 'config.php';

if (!estConnecte() || getTypeUtilisateur() !== 'agriculteur') {
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

$id_agriculteur = getUserId();

try {
    // CORRECTION : o.titre n'existe pas dans la table offre.
    // On construit un titre lisible à partir de fruit + gouvernorat.
    $stmt = $pdo->prepare("
        SELECT o.id_offre,
               CONCAT(tf.libelle, ' - ', g.libelle) AS titre,
               o.date_limitte,
               o.nombre_ouvriers,
               tf.libelle AS fruit,
               g.libelle  AS gouvernorat,
               COUNT(c.id_candidate)                                         AS nb_candidatures,
               COUNT(CASE WHEN c.decision = 'accepte'   THEN 1 END)         AS nb_acceptes,
               COUNT(CASE WHEN c.decision = 'refuse'    THEN 1 END)         AS nb_refuses,
               COUNT(CASE WHEN c.decision = 'en_attente' OR c.decision IS NULL THEN 1 END) AS nb_en_attente
        FROM offre o
        JOIN type_fruit  tf ON o.id_type_fruit   = tf.id_type_fruit
        JOIN gouvernement g ON o.id_gouvernorat   = g.id_gouvernorat
        LEFT JOIN candidate c ON o.id_offre = c.id_offre
        WHERE o.id_agriculteur = ?
        GROUP BY o.id_offre, tf.libelle, g.libelle, o.date_limitte, o.nombre_ouvriers
        ORDER BY o.date_limitte DESC
    ");
    $stmt->execute([$id_agriculteur]);
    $offres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($offres);
} catch (PDOException $e) {
    error_log("Erreur lister_candidatures: " . $e->getMessage());
    echo json_encode(['error' => 'Erreur serveur : ' . $e->getMessage()]);
}
?>
