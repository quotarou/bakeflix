<?php
require_once 'db.php';

$orderId = (int)($_SESSION['last_order'] ?? 0);
if (!$orderId) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
$stmt->execute([$orderId]);
$order = $stmt->fetch();

$stmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
$stmt->execute([$orderId]);
$lines = $stmt->fetchAll();

$pageTitle = 'Order confirmed';
require 'header.php';
?>
<h1>Thank you for your order! 🎉</h1>
<p class="notice" role="status">
    Your order ID is <strong><?= e(orderCode($order['id'])) ?></strong>.
    A confirmation has been sent to <?= e($order['email']) ?>.
</p>

<table>
    <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th></tr></thead>
    <tbody>
    <?php foreach ($lines as $l): ?>
        <tr>
            <td><?= e($l['product_name']) ?></td>
            <td><?= money($l['unit_price']) ?></td>
            <td><?= (int)$l['quantity'] ?></td>
            <td><?= money($l['unit_price'] * $l['quantity']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="summary">
    <p>Subtotal: <strong><?= money($order['subtotal']) ?></strong></p>
    <p>Tax: <strong><?= money($order['tax']) ?></strong></p>
    <p>Delivery: <strong><?= $order['delivery_fee'] > 0 ? money($order['delivery_fee']) : 'Free' ?></strong></p>
    <p class="grand">Total paid: <strong><?= money($order['total']) ?></strong></p>
</div>

<h2>Delivering to</h2>
<p><?= e($order['customer_name']) ?><br><?= nl2br(e($order['shipping_address'])) ?><br><?= e($order['postal_code']) ?></p>
<p><a class="button" href="index.php">Continue shopping</a></p>
<?php require 'footer.php'; ?>
