<?php
// php/modifier_profil_ouvrier.php
header('Content-Type: application/json');
require_once 'config.php';

if (!estConnecte() || getTypeUtilisateur() !== 'ouvrier') {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$id          = getUserId();
$nom         = trim($_POST['nom']         ?? '');
$prenom      = trim($_POST['prenom']      ?? '');
$email       = trim($_POST['email']       ?? '');
$description = trim($_POST['description'] ?? '');

// Validations
if (empty($nom) || empty($prenom) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Nom, prénom et email sont obligatoires']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email invalide']);
    exit;
}

try {
    // Vérifier que l'email n'est pas déjà pris par un autre utilisateur
    $stmt = $pdo->prepare("SELECT id_ouvrier FROM ouvrier WHERE email = ? AND id_ouvrier != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé par un autre compte']);
        exit;
    }

    // Gestion de la photo (optionnelle)
    $photoSql   = '';
    $photoParam = null;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!in_array($_FILES['photo']['type'], $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Format de photo non supporté (JPEG/PNG/GIF)']);
            exit;
        }
        if ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'La photo ne doit pas dépasser 2MB']);
            exit;
        }
        $photoParam = file_get_contents($_FILES['photo']['tmp_name']);
        $photoSql   = ', photo = ?';
    }

    if ($photoParam !== null) {
        $stmt = $pdo->prepare("UPDATE ouvrier SET nom=?, prenom=?, email=?, description=?{$photoSql} WHERE id_ouvrier=?");
        $stmt->execute([$nom, $prenom, $email, $description, $photoParam, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE ouvrier SET nom=?, prenom=?, email=?, description=? WHERE id_ouvrier=?");
        $stmt->execute([$nom, $prenom, $email, $description, $id]);
    }

    // Mettre à jour le pseudo en session si le nom change (optionnel, le pseudo reste)
    echo json_encode(['success' => true, 'message' => 'Profil mis à jour avec succès !']);

} catch (PDOException $e) {
    error_log("Erreur modifier_profil_ouvrier: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur serveur lors de la mise à jour']);
}
?>
