<?php
// php/anciens_chantiers.php
header('Content-Type: application/json');
require_once 'config.php';

if (!estConnecte() || getTypeUtilisateur() !== 'ouvrier') {
    echo json_encode([]);
    exit;
}

$id_ouvrier = getUserId();

$sql = "SELECT c.*, o.date_debut, o.date_fin, o.prix_journee, o.adresse,
        tf.libelle as fruit, g.libelle as gouvernorat
        FROM candidate c
        JOIN offre o ON c.id_offre = o.id_offre
        JOIN type_fruit tf ON o.id_type_fruit = tf.id_type_fruit
        JOIN gouvernement g ON o.id_gouvernorat = g.id_gouvernorat
        WHERE c.id_ouvrier = ? AND c.decision = 'accepte' AND c.note IS NOT NULL
        ORDER BY o.date_fin DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_ouvrier]);
$chantiers = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($chantiers);
?>