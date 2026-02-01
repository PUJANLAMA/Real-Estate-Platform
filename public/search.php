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
$sql = "SELECT * FROM properties";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <?php echo htmlspecialchars($property['location']); ?>
                </div>
                
                <div class="property-price">
                    ₹<?php echo number_format($property['price'], 0); ?>
                </div>
                
                <div class="property-actions">
                    <a href="edit.php?id=<?php echo $property['id']; ?>" class="btn btn-primary btn-small">
                        Edit
                    </a>
                    <a href="delete.php?id=<?php echo $property['id']; ?>" 
                       class="btn btn-danger btn-small delete-btn">
                        Delete
                    </a>
                </div>
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
