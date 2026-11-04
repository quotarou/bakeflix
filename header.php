<?php
require_once 'db.php';
$categories = $pdo->query('SELECT * FROM categories ORDER BY id')->fetchAll();
$cartCount  = array_sum($_SESSION['cart']);
$pageTitle  = $pageTitle ?? 'Bakeflix';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | Bakeflix</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <a class="logo" href="index.php">🧁 Bakeflix</a>
    <form action="category.php" method="get" role="search">
        <input type="search" name="q" placeholder="Search products..." aria-label="Search products">
        <button type="submit">Search</button>
    </form>
    <a class="cart-link" href="cart.php">🛒 Cart (<?= $cartCount ?>)</a>
</header>
<nav aria-label="Categories">
    <a href="category.php">All</a>
    <?php foreach ($categories as $cat): ?>
        <a href="category.php?cat=<?= e($cat['slug']) ?>"><?= e($cat['name']) ?></a>
    <?php endforeach; ?>
</nav>
<main>
