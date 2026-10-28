<?php
// php/lister_offres.php
header('Content-Type: application/json');
require_once 'config.php';

$sql = "SELECT o.*, tf.libelle as fruit, g.libelle as gouvernorat, 
        (SELECT COUNT(*) FROM candidate c WHERE c.id_offre = o.id_offre) as nb_postulants
        FROM offre o
        JOIN type_fruit tf ON o.id_type_fruit = tf.id_type_fruit
        JOIN gouvernement g ON o.id_gouvernorat = g.id_gouvernorat
        WHERE o.date_limitte >= CURDATE()";

$params = [];

// Filtrage
if (isset($_GET['fruit']) && !empty($_GET['fruit'])) {
    $sql .= " AND tf.libelle = ?";
    $params[] = $_GET['fruit'];
}
if (isset($_GET['gouvernorat']) && !empty($_GET['gouvernorat'])) {
    $sql .= " AND g.libelle = ?";
    $params[] = $_GET['gouvernorat'];
}

// Tri
if (isset($_GET['tri']) && $_GET['tri'] === 'prix_asc') {
    $sql .= " ORDER BY o.prix_journee ASC";
} elseif (isset($_GET['tri']) && $_GET['tri'] === 'prix_desc') {
    $sql .= " ORDER BY o.prix_journee DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$offres = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($offres);
?>