<?php
// php/config.php
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'uber_cueillette');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8");
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données");
}

// Fonctions utilitaires
function estConnecte() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

function getTypeUtilisateur() {
    return $_SESSION['user_type'] ?? null;
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}
?>