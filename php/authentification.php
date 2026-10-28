<?php
// php/authentification.php
header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$pseudo = trim($_POST['pseudo'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($pseudo) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs']);
    exit;
}

try {
    // 1. Chercher d'abord dans la table agriculteur
    $stmt = $pdo->prepare("SELECT id_agriculteur as id, pseudo, password, 'agriculteur' as type FROM agriculteur WHERE pseudo = ?");
    $stmt->execute([$pseudo]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 2. Si non trouvé, chercher dans la table ouvrier
    if (!$user) {
        $stmt = $pdo->prepare("SELECT id_ouvrier as id, pseudo, password, 'ouvrier' as type FROM ouvrier WHERE pseudo = ?");
        $stmt->execute([$pseudo]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // 3. Vérification du mot de passe
    if ($user && password_verify($password, $user['password'])) {
        // Renouveler la session après une authentification réussie.
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_pseudo'] = $user['pseudo'];
        $_SESSION['user_type'] = $user['type'];
        
        // Redirection selon le type
        if ($user['type'] === 'agriculteur') {
            echo json_encode(['success' => true, 'redirect' => 'dashboard_agriculteur.html']);
        } else {
            echo json_encode(['success' => true, 'redirect' => 'dashboard_ouvrier.html']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Pseudo ou mot de passe incorrect']);
    }
    
} catch (PDOException $e) {
    error_log("Erreur authentification: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la connexion']);
}
?>