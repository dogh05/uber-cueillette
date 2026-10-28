<?php
// php/ouvriers_a_noter.php
// Retourne tous les ouvriers ACCEPTÉS par cet agriculteur dont la note est encore NULL
header('Content-Type: application/json');
require_once 'config.php';

if (!estConnecte() || getTypeUtilisateur() !== 'agriculteur') {
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

$id_agriculteur = getUserId();

try {
    $stmt = $pdo->prepare("
        SELECT
            c.id_candidate,
            o.nom,
            o.prenom,
            o.photo,
            CONCAT(tf.libelle, ' - ', g.libelle) AS offre_titre,
            off.date_debut,
            off.date_fin
        FROM candidate c
        JOIN ouvrier      o   ON c.id_ouvrier      = o.id_ouvrier
        JOIN offre        off ON c.id_offre         = off.id_offre
        JOIN type_fruit   tf  ON off.id_type_fruit  = tf.id_type_fruit
        JOIN gouvernement g   ON off.id_gouvernorat = g.id_gouvernorat
        WHERE off.id_agriculteur = ?
          AND c.decision = 'accepte'
          AND c.note IS NULL
        ORDER BY off.date_fin DESC
    ");
    $stmt->execute([$id_agriculteur]);
    $ouvriers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Encoder les photos en base64
    foreach ($ouvriers as &$ouv) {
        $ouv['photo'] = $ouv['photo'] ? base64_encode($ouv['photo']) : null;
    }
    unset($ouv);

    echo json_encode($ouvriers);

} catch (PDOException $e) {
    error_log("Erreur ouvriers_a_noter: " . $e->getMessage());
    echo json_encode(['error' => 'Erreur serveur']);
}
?>
