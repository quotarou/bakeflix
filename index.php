<?php
$pageTitle = 'Home';
require 'header.php';

$newest = $pdo->query('SELECT * FROM products WHERE discontinued = 0 ORDER BY added_at DESC LIMIT 4')->fetchAll();
?>
<section class="hero">
    <h1>Everything you need to bake</h1>
    <p>Flours, tools, decorations, flavours and packaging - all in one place.</p>
    <a class="button" href="category.php">Shop all products</a>
</section>

<h2>Shop by category</h2>
<div class="grid">
    <?php foreach ($categories as $cat): ?>
        <a class="card category" href="category.php?cat=<?= e($cat['slug']) ?>">
            <div class="thumb"><?= e($cat['icon']) ?></div>
            <h3><?= e($cat['name']) ?></h3>
        </a>
    <?php endforeach; ?>
</div>

<h2>New arrivals</h2>
<div class="grid">
    <?php foreach ($newest as $p): ?>
        <?php include 'product_card.php'; ?>
    <?php endforeach; ?>
</div>
<?php require 'footer.php'; ?>
