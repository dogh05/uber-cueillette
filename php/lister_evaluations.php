<?php
// php/lister_evaluations.php
header('Content-Type: application/json'); // CORRECTION : header JSON manquant
require_once 'config.php';

if (!estConnecte() || getTypeUtilisateur() !== 'agriculteur') {
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

$id_agriculteur = getUserId();

try {
    // CORRECTION : off.titre remplacé par CONCAT(tf.libelle, ' - ', g.libelle)
    $stmt = $pdo->prepare("
        SELECT c.note,
               c.commentaire,
               c.date_candidature,
               o.nom,
               o.prenom,
               o.photo,
               CONCAT(tf.libelle, ' - ', g.libelle) AS offre_titre
        FROM candidate c
        JOIN ouvrier      o   ON c.id_ouvrier      = o.id_ouvrier
        JOIN offre        off ON c.id_offre         = off.id_offre
        JOIN type_fruit   tf  ON off.id_type_fruit  = tf.id_type_fruit
        JOIN gouvernement g   ON off.id_gouvernorat = g.id_gouvernorat
        WHERE off.id_agriculteur = ? AND c.note IS NOT NULL
        ORDER BY c.date_candidature DESC
    ");
    $stmt->execute([$id_agriculteur]);
    $evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Encoder les photos en base64
    foreach ($evaluations as &$eval) {
        $eval['photo'] = $eval['photo'] ? base64_encode($eval['photo']) : null;
    }
    unset($eval);

    echo json_encode($evaluations);
} catch (PDOException $e) {
    error_log("Erreur lister_evaluations: " . $e->getMessage());
    echo json_encode(['error' => 'Erreur serveur : ' . $e->getMessage()]);
}
?>
