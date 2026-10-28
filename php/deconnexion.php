<?php
// php/deconnexion.php
// CORRECTION : session_start() supprimé car config.php le fait déjà.
// Mais comme ce fichier est appelé directement (sans config.php),
// on garde session_start() ici et on NE fait PAS require config.php.
session_start();
session_unset();   // CORRECTION : vide proprement les variables de session
session_destroy();

// Répondre en JSON si appelé en AJAX (fetch depuis le dashboard)
// ou rediriger si appelé directement (lien href)
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') ||
    str_contains($_SERVER['HTTP_ACCEPT'] ?? '', '*/*')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} else {
    header('Location: ../index.html');
}
exit;
?>
