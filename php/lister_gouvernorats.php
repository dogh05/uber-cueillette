<?php
// php/lister_gouvernorats.php
header('Content-Type: application/json');
require_once 'config.php';

$stmt = $pdo->query("SELECT id_gouvernorat, libelle FROM gouvernement ORDER BY libelle");
$gouvernorats = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($gouvernorats);
?>