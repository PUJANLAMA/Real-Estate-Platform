<?php
require_once "../config/db.php";

// Require login to add properties
requireLogin();

include "../includes/header.php";

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $message = '<div class="error-message">Invalid security token. Please try again.</div>';
    } else {
        $title = sanitizeInput($_POST['title']);
        $location = sanitizeInput($_POST['location']);
        $price = $_POST['price'];
        $house_type = $_POST['house_type'];
        $description = sanitizeInput($_POST['description']);
        $user_id = $_SESSION['user_id'];

        // Image handling
        $image = $_FILES['image'];
        $imageName = $image['name'];
        $imageTmp = $image['tmp_name'];

        $ext = pathinfo($imageName, PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png'];

        if (!in_array(strtolower($ext), $allowed)) {
            $message = '<div class="error-message">Invalid image format. Please upload JPG, JPEG, or PNG.</div>';
        } else {
            $newImageName = uniqid("property_") . "." . $ext;
            $uploadPath = "../uploads/properties/" . $newImageName;

            if (move_uploaded_file($imageTmp, $uploadPath)) {
                // Insert into database
                $stmt = $pdo->prepare("
                    INSERT INTO properties 
                    (title, location, price, house_type, description, image, user_id, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                ");

                if ($stmt->execute([
                    $title,
                    $location,
                    $price,
                    $house_type,
                    $description,
                    $newImageName,
                    $user_id
                ])) {
                    redirect('index.php', 'Property added successfully!', 'success');
                } else {
                    $message = '<div class="error-message">Failed to add property. Please try again.</div>';
                }
            } else {
                $message = '<div class="error-message">Failed to upload image. Please try again.</div>';
            }
        }
    }
}
?>

<div class="page-header">
    <h2>Add New Property</h2>
    <a href="index.php" class="btn btn-primary">← Back to Listings</a>
</div>

<?php echo $message; ?>

<div class="form-container">
    <form method="POST" enctype="multipart/form-data">
        <?php echo getCSRFField(); ?>
        
        <div class="form-group">
            <label for="title">Property Title *</label>
            <input type="text" id="title" name="title" required 
                   placeholder="e.g., Luxury 3BHK Apartment">
        </div>

        <div class="form-group">
            <label for="location">Location *</label>
            <input type="text" id="location" name="location" required 
                   placeholder="e.g., Delhi, Mumbai">
        </div>

        <div class="form-group">
            <label for="price">Price (₹) *</label>
            <input type="number" id="price" name="price" step="0.01" required 
                   placeholder="e.g., 5000000">
        </div>

        <div class="form-group">
            <label for="house_type">Property Type *</label>
            <select id="house_type" name="house_type" required>
                <option value="">Select Type</option>
                <option value="Apartment">Apartment</option>
                <option value="House">House</option>
                <option value="Villa">Villa</option>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" 
                      placeholder="Enter property description..."></textarea>
        </div>

        <div class="form-group">
            <label for="image">Property Image *</label>
            <input type="file" id="image" name="image" required accept="image/jpeg,image/jpg,image/png">
            <small style="color: #6b7280; margin-top: 0.5rem; display: block;">
                Accepted formats: JPG, JPEG, PNG (Max 5MB)
            </small>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">
            Add Property
        </button>
    </form>
</div>

<script src="../assets/js/script.js"></script>

<?php include "../includes/footer.php"; ?>
