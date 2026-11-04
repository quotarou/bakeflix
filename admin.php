<?php
require_once 'db.php';
if (empty($_SESSION['admin'])) { header('Location: admin_login.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    if ($action === 'stock') {
        $stmt = $pdo->prepare('UPDATE products SET stock = ? WHERE id = ?');
        $stmt->execute([max(0, (int)($_POST['stock'] ?? 0)), $id]);
        $_SESSION['msg'] = 'Stock updated.';
    }
    if ($action === 'toggle') {
        $pdo->prepare('UPDATE products SET discontinued = 1 - discontinued WHERE id = ?')->execute([$id]);
        $_SESSION['msg'] = 'Product status changed.';
    }
    if ($action === 'delete') {
        $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
        $_SESSION['msg'] = 'Product deleted.';
    }
    header('Location: admin.php');
    exit;
}

$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);

$products = $pdo->query('SELECT p.*, c.name AS category_name FROM products p
                         JOIN categories c ON p.category_id = c.id ORDER BY p.id')->fetchAll();
$orders   = $pdo->query('SELECT * FROM orders ORDER BY id DESC LIMIT 10')->fetchAll();

$pageTitle = 'Admin dashboard';
require 'header.php';
?>
<h1>Admin dashboard</h1>
<p><a class="button" href="admin_edit.php">+ Add product</a> <a href="admin_logout.php">Log out</a></p>
<?php if ($msg): ?><p class="notice" role="status"><?= e($msg) ?></p><?php endif; ?>

<h2>Products</h2>
<div class="scroll">
<table>
    <thead><tr><th>SKU</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($products as $p): ?>
        <tr>
            <td><?= e($p['sku']) ?></td>
            <td><?= productImage($p, 'mini-img') ?> <?= e($p['name']) ?></td>
            <td><?= e($p['category_name']) ?></td>
            <td><?= money($p['price']) ?></td>
            <td>
                <form method="post" class="row">
                    <input type="hidden" name="action" value="stock">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <label class="sr-only" for="s<?= $p['id'] ?>">Stock for <?= e($p['name']) ?></label>
                    <input type="number" id="s<?= $p['id'] ?>" name="stock" value="<?= (int)$p['stock'] ?>" min="0">
                    <button type="submit">Save</button>
                </form>
            </td>
            <td><?= $p['discontinued'] ? 'Discontinued' : 'Active' ?></td>
            <td>
                <a href="admin_edit.php?id=<?= $p['id'] ?>">Edit</a>
                <form method="post" class="row">
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <button type="submit"><?= $p['discontinued'] ? 'Reactivate' : 'Discontinue' ?></button>
                </form>
                <form method="post" class="row" onsubmit="return confirm('Delete this product?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <button type="submit" class="danger">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<h2>Latest orders</h2>
<?php if (empty($orders)): ?>
    <p>No orders yet.</p>
<?php else: ?>
<div class="scroll">
<table>
    <thead><tr><th>Order</th><th>Date</th><th>Customer</th><th>Payment</th><th>Total</th><th>Status</th></tr></thead>
    <tbody>
    <?php foreach ($orders as $o): ?>
        <tr>
            <td><?= e(orderCode($o['id'])) ?></td>
            <td><?= e($o['created_at']) ?></td>
            <td><?= e($o['customer_name']) ?></td>
            <td><?= e($o['payment_method']) ?></td>
            <td><?= money($o['total']) ?></td>
            <td><?= e($o['status']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>
<?php require 'footer.php'; ?>
