<?php
require_once "../config/db.php";

// Require login
requireLogin();

$id = $_GET['id'] ?? 0;
$token = $_GET['token'] ?? '';

// Verify CSRF token
if (!verifyCSRFToken($token)) {
    redirect('index.php', 'Invalid security token.', 'error');
}

// Get property and verify ownership
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->execute([$id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    redirect('index.php', 'Property not found.', 'error');
}

// Check if user owns this property
if ($property['user_id'] != $_SESSION['user_id']) {
    redirect('index.php', 'You do not have permission to delete this property.', 'error');
}

// Delete the image file
$imagePath = "../uploads/properties/" . $property['image'];
if (file_exists($imagePath)) {
    unlink($imagePath);
}

// Delete from database
$stmt = $pdo->prepare("DELETE FROM properties WHERE id = ? AND user_id = ?");
if ($stmt->execute([$id, $_SESSION['user_id']])) {
    redirect('index.php', 'Property deleted successfully.', 'success');
} else {
    redirect('index.php', 'Failed to delete property.', 'error');
}
?>
