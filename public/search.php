<?php
require_once "../config/db.php";

// Initialize arrays for query building
$where = [];
$params = [];

// Build WHERE clauses based on search parameters
if (!empty($_GET['location'])) {
    $where[] = "location LIKE ?";
    $params[] = "%" . $_GET['location'] . "%";
}

if (!empty($_GET['min_price'])) {
    $where[] = "price >= ?";
    $params[] = $_GET['min_price'];
}

if (!empty($_GET['max_price'])) {
    $where[] = "price <= ?";
    $params[] = $_GET['max_price'];
}

if (!empty($_GET['house_type'])) {
    $where[] = "house_type = ?";
    $params[] = $_GET['house_type'];
}

// Build and execute query
$sql = "SELECT p.*, u.username FROM properties p LEFT JOIN users u ON p.user_id = u.id";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

$currentUserId = $_SESSION['user_id'] ?? null;
?>

<?php if ($properties): ?>
    <?php foreach ($properties as $property): ?>
        <div class="property-card">
            <div class="property-image">
                <img src="../uploads/properties/<?php echo htmlspecialchars($property['image']); ?>" 
                     alt="<?php echo htmlspecialchars($property['title']); ?>"
                     loading="lazy">
                <span class="property-type-badge"><?php echo htmlspecialchars($property['house_type']); ?></span>
            </div>
            
            <div class="property-content">
                <h3 class="property-title"><?php echo htmlspecialchars($property['title']); ?></h3>
                
                <div class="property-location">
                    📍 <?php echo htmlspecialchars($property['location']); ?>
                </div>
                
                <div class="property-price">
                    ₹<?php echo number_format($property['price'], 0); ?>
                </div>
                
                <?php if (!empty($property['description'])): ?>
                    <div class="property-description">
                        <?php echo htmlspecialchars(substr($property['description'], 0, 100)); ?>
                        <?php if (strlen($property['description']) > 100) echo '...'; ?>
                    </div>
                <?php endif; ?>
                
                <div class="property-meta">
                    <small>Posted by: <?php echo htmlspecialchars($property['username'] ?? 'Unknown'); ?></small>
                </div>
                
                <?php if (isLoggedIn() && $property['user_id'] == $currentUserId): ?>
                    <div class="property-actions">
                        <a href="edit.php?id=<?php echo $property['id']; ?>" class="btn btn-primary btn-small">
                            ✏️ Edit
                        </a>
                        <a href="delete.php?id=<?php echo $property['id']; ?>&token=<?php echo urlencode(generateCSRFToken()); ?>" 
                           class="btn btn-danger btn-small delete-btn"
                           onclick="return confirm('Are you sure you want to delete this property?');">
                            🗑️ Delete
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="no-results">
        <div class="no-results-icon">🏠</div>
        <h3>No Properties Found</h3>
        <p>Try adjusting your search filters or add a new property.</p>
    </div>
<?php endif; ?>
