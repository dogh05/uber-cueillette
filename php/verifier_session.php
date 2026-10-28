<?php
// php/verifier_session.php
// CORRECTION : session_start() supprimé ici et délégué à config.php pour éviter le double appel
require_once 'config.php';
header('Content-Type: application/json');

if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])) {
    echo json_encode([
        'connecte' => true,
        'type'     => $_SESSION['user_type'],
        'pseudo'   => $_SESSION['user_pseudo'] ?? 'Utilisateur'
    ]);
} else {
    echo json_encode(['connecte' => false]);
}
?>
