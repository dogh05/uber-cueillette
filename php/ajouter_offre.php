<?php
// php/ajouter_offre.php
header('Content-Type: application/json'); // CORRECTION : doit retourner du JSON, pas une redirection
require_once 'config.php';

if (!estConnecte() || getTypeUtilisateur() !== 'agriculteur') {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$id_type_fruit   = trim($_POST['id_type_fruit']   ?? '');
$id_gouvernorat  = trim($_POST['id_gouvernorat']  ?? '');
$adresse         = trim($_POST['adresse']         ?? '');
$date_debut      = trim($_POST['date_debut']      ?? '');
$date_fin        = trim($_POST['date_fin']        ?? '');
$nombre_ouvriers = trim($_POST['nombre_ouvriers'] ?? '');
$prix_journee    = trim($_POST['prix_journee']    ?? '');
$date_limitte    = trim($_POST['date_limitte']    ?? '');
$id_agriculteur  = getUserId();

// Validations
if (empty($id_type_fruit) || empty($id_gouvernorat) || empty($adresse) ||
    empty($date_debut) || empty($date_fin) || empty($nombre_ouvriers) ||
    empty($prix_journee) || empty($date_limitte)) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires']);
    exit;
}

if ($date_fin < $date_debut) {
    echo json_encode(['success' => false, 'message' => 'La date de fin doit être après la date de début']);
    exit;
}

if ($date_limitte > $date_debut) {
    // avertissement seulement, pas bloquant
}

try {
    $stmt = $pdo->prepare("INSERT INTO offre (id_type_fruit, id_gouvernorat, adresse, date_debut, date_fin,
                           nombre_ouvriers, prix_journee, date_limitte, id_agriculteur)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $id_type_fruit, $id_gouvernorat, $adresse,
        $date_debut, $date_fin,
        $nombre_ouvriers, $prix_journee, $date_limitte,
        $id_agriculteur
    ]);

    echo json_encode(['success' => true, 'message' => 'Offre publiée avec succès !']);
} catch (PDOException $e) {
    error_log("Erreur ajouter_offre: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement : ' . $e->getMessage()]);
}
?>
