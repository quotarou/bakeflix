<?php $id = $p['id']; ?>
<div class="card">
    <div class="thumb"><?= productImage($p) ?></div>
    <h3><a href="product.php?id=<?= $id ?>"><?= e($p['name']) ?></a></h3>
    <p class="muted"><?= e($p['brand']) ?> &middot; <?= e($p['unit']) ?></p>
    <p class="price"><?= money($p['price']) ?></p>
    <?php if ($p['stock'] > 0): ?>
        <p class="in-stock">In Stock</p>
        <form action="cart.php" method="post">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="back" value="<?= e($_SERVER['REQUEST_URI']) ?>">
            <label class="sr-only" for="qty<?= $id ?>">Quantity</label>
            <input type="number" id="qty<?= $id ?>" name="qty" value="1" min="1" max="<?= $p['stock'] ?>">
            <button type="submit">Add to cart</button>
        </form>
    <?php else: ?>
        <p class="out-stock">Out of Stock</p>
    <?php endif; ?>
</div>
