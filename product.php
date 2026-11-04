<?php
require_once 'db.php';

$id   = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT p.*, c.name AS category_name, c.slug AS category_slug
                       FROM products p JOIN categories c ON p.category_id = c.id
                       WHERE p.id = ? AND p.discontinued = 0');
$stmt->execute([$id]);
$p = $stmt->fetch();

$pageTitle = $p ? $p['name'] : 'Product not found';
require 'header.php';

if (!$p) {
    echo '<p>Sorry, that product was not found. <a href="category.php">Browse all products</a></p>';
    require 'footer.php';
    exit;
}
?>
<p><a href="category.php?cat=<?= e($p['category_slug']) ?>">&laquo; Back to <?= e($p['category_name']) ?></a></p>

<div class="detail">
    <div class="big-thumb"><?= productImage($p, 'detail-img') ?></div>
    <div>
        <h1><?= e($p['name']) ?></h1>
        <p class="muted"><?= e($p['brand']) ?> &middot; <?= e($p['unit']) ?> &middot; SKU: <?= e($p['sku']) ?></p>
        <p class="price big"><?= money($p['price']) ?></p>

        <?php if ($p['stock'] > 0): ?>
            <p class="in-stock">In Stock (<?= (int)$p['stock'] ?> available)</p>
            <form action="cart.php" method="post">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <input type="hidden" name="back" value="<?= e($_SERVER['REQUEST_URI']) ?>">
                <label for="qty">Quantity:</label>
                <input type="number" id="qty" name="qty" value="1" min="1" max="<?= $p['stock'] ?>">
                <button type="submit">Add to cart</button>
            </form>
        <?php else: ?>
            <p class="out-stock">Out of Stock</p>
        <?php endif; ?>

        <h2>About this product</h2>
        <p><?= e($p['description']) ?></p>

        <h2>Ingredients / materials</h2>
        <p><?= e($p['ingredients']) ?></p>

        <h2>Allergen notice</h2>
        <p><?= e($p['allergens']) ?></p>

        <h2>Shelf life &amp; storage</h2>
        <p><?= e($p['storage']) ?></p>
    </div>
</div>
<?php require 'footer.php'; ?>
