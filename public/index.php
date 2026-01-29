<?php
require_once "../config/db.php";
include "../includes/header.php";
?>

<div class="page-header">
    <h2>All Properties</h2>
    <a href="add.php" class="btn btn-primary">+ Add New Property</a>
</div>

<div class="search-container">
    <form id="searchForm">
        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" id="location" placeholder="Enter location...">
        </div>
        
        <div class="form-group">
            <label for="min_price">Min Price (₹)</label>
            <input type="number" id="min_price" placeholder="Min price">
        </div>
        
        <div class="form-group">
            <label for="max_price">Max Price (₹)</label>
            <input type="number" id="max_price" placeholder="Max price">
        </div>
        
        <div class="form-group">
            <label for="house_type">Property Type</label>
            <select id="house_type">
                <option value="">All Types</option>
                <option value="Apartment">Apartment</option>
                <option value="House">House</option>
                <option value="Villa">Villa</option>
            </select>
        </div>
    </form>
</div>

<div id="results">
    <!-- Properties will be loaded here via AJAX -->
</div>

<script src="../assets/js/script.js"></script>

<?php include "../includes/footer.php"; ?>
