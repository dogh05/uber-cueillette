<?php
// php/lister_fruits.php
header('Content-Type: application/json');
require_once 'config.php';

$stmt = $pdo->query("SELECT id_type_fruit, libelle FROM type_fruit ORDER BY libelle");
$fruits = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($fruits);
?>