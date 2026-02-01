<?php
require_once "../config/db.php";

// Require login
requireLogin();

include "../includes/header.php";

$id = $_GET['id'] ?? 0;

// Get property and verify ownership
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->execute([$id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    echo '<div class="error-message">Property not found.</div>';
    echo '<a href="index.php" class="btn btn-primary">Back to Listings</a>';
    include "../includes/footer.php";
    exit;
}

// Check if user owns this property
if ($property['user_id'] != $_SESSION['user_id']) {
    echo '<div class="error-message">You do not have permission to edit this property.</div>';
    echo '<a href="index.php" class="btn btn-primary">Back to Listings</a>';
    include "../includes/footer.php";
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $message = '<div class="error-message">Invalid security token. Please try again.</div>';
    } else {
        $title = sanitizeInput($_POST['title']);
        $location = sanitizeInput($_POST['location']);
        $price = $_POST['price'];
        $house_type = $_POST['house_type'];
        $description = sanitizeInput($_POST['description']);

        $stmt = $pdo->prepare("
            UPDATE properties 
            SET title=?, location=?, price=?, house_type=?, description=?, updated_at=NOW()
            WHERE id=? AND user_id=?
        ");

        if ($stmt->execute([
            $title,
            $location,
            $price,
            $house_type,
            $description,
            $id,
            $_SESSION['user_id']
        ])) {
            redirect('index.php', 'Property updated successfully!', 'success');
        } else {
            $message = '<div class="error-message">Failed to update property. Please try again.</div>';
        }
        
        // Refresh property data
        $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
        $stmt->execute([$id]);
        $property = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<div class="page-header">
    <h2>Edit Property</h2>
    <a href="index.php" class="btn btn-primary">← Back to Listings</a>
</div>

<?php echo $message; ?>

<div class="form-container">
    <form method="POST">
        <?php echo getCSRFField(); ?>
        
        <div class="form-group">
            <label for="title">Property Title *</label>
            <input type="text" id="title" name="title" 
                   value="<?php echo htmlspecialchars($property['title']); ?>" required>
        </div>

        <div class="form-group">
            <label for="location">Location *</label>
            <input type="text" id="location" name="location" 
                   value="<?php echo htmlspecialchars($property['location']); ?>" required>
        </div>

        <div class="form-group">
            <label for="price">Price (₹) *</label>
            <input type="number" id="price" name="price" step="0.01"
                   value="<?php echo htmlspecialchars($property['price']); ?>" required>
        </div>

        <div class="form-group">
            <label for="house_type">Property Type *</label>
            <select id="house_type" name="house_type" required>
                <option value="Apartment" <?php if($property['house_type']=="Apartment") echo "selected"; ?>>Apartment</option>
                <option value="House" <?php if($property['house_type']=="House") echo "selected"; ?>>House</option>
                <option value="Villa" <?php if($property['house_type']=="Villa") echo "selected"; ?>>Villa</option>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description"><?php echo htmlspecialchars($property['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Current Image</label>
            <div style="margin-top: 0.5rem;">
                <img src="../uploads/properties/<?php echo htmlspecialchars($property['image']); ?>" 
                     alt="Property Image"
                     style="max-width: 100%; max-height: 200px; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            </div>
            <small style="color: #6b7280; margin-top: 0.5rem; display: block;">
                Note: Image update functionality coming soon
            </small>
        </div>

        <button type="submit" name="update" class="btn btn-primary">
            Update Property
        </button>
    </form>
</div>

<script src="../assets/js/script.js"></script>

<?php include "../includes/footer.php"; ?>
