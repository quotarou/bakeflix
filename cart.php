<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    if ($action === 'add') {
        $product = getProduct($pdo, $id);
        if ($product) {
            $stock = (int)$product['stock'];
            $qty   = max(1, (int)($_POST['qty'] ?? 1));
            $new   = ($_SESSION['cart'][$id] ?? 0) + $qty;
            if ($stock <= 0) {
                $_SESSION['msg'] = 'Sorry, that item is out of stock.';
            } else {
                if ($new > $stock) {                    
                    $new = $stock;
                    $_SESSION['msg'] = 'Only ' . $stock . ' available - quantity limited to stock.';
                } else {
                    $_SESSION['msg'] = $product['name'] . ' added to cart.';
                }
                $_SESSION['cart'][$id] = $new;
            }
        }
        $back = $_POST['back'] ?? 'cart.php';
        if ($back === '' || $back[0] !== '/' || strpos($back, '//') === 0) { $back = 'cart.php'; }
        header('Location: ' . $back);
        exit;
    }

    if ($action === 'update') {
        foreach (($_POST['qty'] ?? []) as $pid => $qty) {
            $pid = (int)$pid; $qty = (int)$qty;
            $product = getProduct($pdo, $pid);
            if (!$product || $qty <= 0) { unset($_SESSION['cart'][$pid]); }
            else                        { $_SESSION['cart'][$pid] = min($qty, (int)$product['stock']); }
        }
        $_SESSION['msg'] = 'Cart updated.';
    }

    if ($action === 'remove') {
        unset($_SESSION['cart'][$id]);
        $_SESSION['msg'] = 'Item removed.';
    }

    header('Location: cart.php');
    exit;
}

$pageTitle = 'Your Cart';
require 'header.php';

$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);

$items    = [];
$subtotal = 0;
foreach ($_SESSION['cart'] as $id => $qty) {
    $p = getProduct($pdo, $id);
    if (!$p) { unset($_SESSION['cart'][$id]); continue; }   
    $items[]   = ['p' => $p, 'qty' => $qty];
    $subtotal += $p['price'] * $qty;
}
$tax      = round($subtotal * TAX_RATE, 2);
$delivery = ($subtotal > 0 && $subtotal < FREE_DELIVERY) ? DELIVERY_FEE : 0;
$total    = $subtotal + $tax + $delivery;
?>
<h1>Your Cart</h1>
<?php if ($msg): ?><p class="notice" role="status"><?= e($msg) ?></p><?php endif; ?>

<?php if (empty($items)): ?>
    <p>Your cart is empty. <a href="category.php">Start shopping</a></p>
<?php else: ?>
    <form action="cart.php" method="post">
        <input type="hidden" name="action" value="update">
        <table>
            <thead>
                <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): $p = $item['p']; $id = $p['id']; ?>
                <tr>
                    <td><?= productImage($p, 'mini-img') ?> <?= e($p['name']) ?> <span class="muted">(<?= e($p['unit']) ?>)</span></td>
                    <td><?= money($p['price']) ?></td>
                    <td>
                        <label class="sr-only" for="q<?= $id ?>">Quantity for <?= e($p['name']) ?></label>
                        <input type="number" id="q<?= $id ?>" name="qty[<?= $id ?>]" value="<?= $item['qty'] ?>" min="0" max="<?= $p['stock'] ?>">
                    </td>
                    <td><?= money($p['price'] * $item['qty']) ?></td>
                    <td><button type="submit" form="remove<?= $id ?>">Remove</button></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit">Update cart</button>
    </form>

    <?php foreach ($items as $item): ?>
        <form id="remove<?= $item['p']['id'] ?>" action="cart.php" method="post">
            <input type="hidden" name="action" value="remove">
            <input type="hidden" name="id" value="<?= $item['p']['id'] ?>">
        </form>
    <?php endforeach; ?>

    <div class="summary">
        <p>Subtotal: <strong><?= money($subtotal) ?></strong></p>
        <p>Tax (<?= TAX_RATE * 100 ?>%): <strong><?= money($tax) ?></strong></p>
        <p>Delivery: <strong><?= $delivery ? money($delivery) : 'Free' ?></strong></p>
        <p class="grand">Total: <strong><?= money($total) ?></strong></p>
        <p class="muted">Free delivery on orders of <?= money(FREE_DELIVERY) ?> or more.</p>
        <a class="button" href="checkout.php">Proceed to checkout</a>
    </div>
<?php endif; ?>
<?php require 'footer.php'; ?>
