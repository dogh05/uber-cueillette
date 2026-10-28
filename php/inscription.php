<?php
// php/inscription.php
header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$type = $_POST['type'] ?? ''; // 'agriculteur' ou 'ouvrier'

if (!in_array($type, ['agriculteur', 'ouvrier'])) {
    echo json_encode(['success' => false, 'message' => 'Type d\'utilisateur invalide']);
    exit;
}

// Récupération des données communes
$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$cin = trim($_POST['cin'] ?? '');
$email = trim($_POST['email'] ?? '');
$pseudo = trim($_POST['pseudo'] ?? '');
$password = $_POST['password'] ?? '';

// ========== VALIDATIONS ==========

// Validation du CIN (8 chiffres exactement)
if (!preg_match('/^\d{8}$/', $cin)) {
    echo json_encode(['success' => false, 'message' => 'CIN invalide : doit contenir exactement 8 chiffres']);
    exit;
}

// Validation de l'email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email invalide']);
    exit;
}


// Validation du pseudo (lettres uniquement)
if (!preg_match('/^[A-Za-z]+$/', $pseudo)) {
    echo json_encode(['success' => false, 'message' => 'Pseudo invalide : lettres uniquement (sans chiffres ni caractères spéciaux)']);
    exit;
}

// Validation du mot de passe (8 caractères minimum + finit par $ ou #)
if (!preg_match('/^.{8,}[$#]$/', $password)) {
    echo json_encode(['success' => false, 'message' => 'Mot de passe invalide : 8 caractères minimum, doit se terminer par $ ou #']);
    exit;
}

// Bcrypt (PASSWORD_DEFAULT) ne doit pas tronquer un mot de passe > 72 octets.
if (strlen($password) > 72 || strpos($password, "\0") !== false) {
    echo json_encode(['success' => false, 'message' => 'Mot de passe invalide : maximum 72 octets, sans caractère nul']);
    exit;
}

try {
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    if ($type === 'agriculteur') {
        // ========== INSCRIPTION AGRICULTEUR ==========
        $adresse = trim($_POST['adresse'] ?? '');
        
        if (empty($adresse)) {
            echo json_encode(['success' => false, 'message' => 'L\'adresse est obligatoire']);
            exit;
        }
        
        // Vérifier si le pseudo, email ou CIN existe déjà
        $stmt = $pdo->prepare("SELECT id_agriculteur FROM agriculteur WHERE pseudo = ? OR email = ? OR CIN = ?");
        $stmt->execute([$pseudo, $email, $cin]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Ce pseudo, email ou CIN est déjà utilisé']);
            exit;
        }
        
        // Insertion dans la table agriculteur
        $stmt = $pdo->prepare("INSERT INTO agriculteur (nom, prenom, CIN, email, adresse, pseudo, password) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([$nom, $prenom, $cin, $email, $adresse, $pseudo, $passwordHash]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Inscription réussie !']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription']);
        }
        
    } else {
        // ========== INSCRIPTION OUVRIER ==========
        $description = trim($_POST['description'] ?? '');
        $photo = null;
        
        if (empty($description)) {
            echo json_encode(['success' => false, 'message' => 'La description est obligatoire']);
            exit;
        }
        
        // Vérifier si le pseudo, email ou CIN existe déjà
        $stmt = $pdo->prepare("SELECT id_ouvrier FROM ouvrier WHERE pseudo = ? OR email = ? OR CIN = ?");
        $stmt->execute([$pseudo, $email, $cin]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Ce pseudo, email ou CIN est déjà utilisé']);
            exit;
        }
        
        // Gestion de la photo (upload et conversion en BLOB)
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photoTmp = $_FILES['photo']['tmp_name'];
            $photoType = $_FILES['photo']['type'];
            
            // Vérifier que c'est bien une image
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!in_array($photoType, $allowedTypes)) {
                echo json_encode(['success' => false, 'message' => 'Format de photo non supporté (JPEG, PNG, GIF uniquement)']);
                exit;
            }
            
            // Lire le fichier et le convertir en BLOB
            $photo = file_get_contents($photoTmp);
        } else {
            echo json_encode(['success' => false, 'message' => 'La photo est obligatoire']);
            exit;
        }
        
        // Insertion dans la table ouvrier
        $stmt = $pdo->prepare("INSERT INTO ouvrier (nom, prenom, CIN, email, photo, description, pseudo, password) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([$nom, $prenom, $cin, $email, $photo, $description, $pseudo, $passwordHash]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Inscription réussie !']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription']);
        }
    }
    
} catch (PDOException $e) {
    // Envoi d'une erreur générique
    error_log("Erreur inscription: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription: ' . $e->getMessage()]);
}
?>