<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real Estate Platform - Property Listings</title>
    <meta name="description" content="Browse and manage real estate properties">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header>
    <div class="header-content">
        <h1>Real Estate Platform</h1>
        <nav>
            <a href="index.php">🏠 Home</a>
            <?php if (isLoggedIn()): ?>
                <a href="add.php">➕ Add Property</a>
                <span class="user-info">
                    👤 <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
                <a href="logout.php?token=<?php echo urlencode(generateCSRFToken()); ?>" 
                   class="logout-link">🚪 Logout</a>
            <?php else: ?>
                <a href="login.php">🔑 Login</a>
                <a href="register.php">📝 Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main>
