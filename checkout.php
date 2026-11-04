<?php
require_once 'db.php';

$methods = [
    'card'       => 'Credit / Debit card',
    'upi'        => 'UPI',
    'netbanking' => 'Net banking',
    'wallet'     => 'Digital wallet',
];

// this is a demo payment which always succeeds

function processPayment($method, $amount) {
    return true;
}

$items    = [];
$subtotal = 0;
foreach ($_SESSION['cart'] as $id => $qty) {
    $p = getProduct($pdo, $id);
    if (!$p) { unset($_SESSION['cart'][$id]); continue; }
    $items[]   = ['p' => $p, 'qty' => $qty];
    $subtotal += $p['price'] * $qty;
}
if (empty($items)) { header('Location: cart.php'); exit; }

$tax      = round($subtotal * TAX_RATE, 2);
$delivery = ($subtotal < FREE_DELIVERY) ? DELIVERY_FEE : 0;
$total    = $subtotal + $tax + $delivery;

$f = ['name' => '', 'email' => '', 'phone' => '', 'address' => '', 'postal' => '',
      'same' => '1', 'billing' => '', 'payment' => 'upi'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['name', 'email', 'phone', 'address', 'postal', 'billing', 'payment'] as $k) {
        $f[$k] = trim($_POST[$k] ?? '');
    }
    $f['same'] = isset($_POST['same']) ? '1' : '';

    if ($f['name'] === '')                               $errors[] = 'Please enter the recipient name.';
    if (!filter_var($f['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (!preg_match('/^\d{10}$/', $f['phone']))          $errors[] = 'Phone number must be 10 digits.';
    if ($f['address'] === '')                            $errors[] = 'Please enter the shipping address.';
    if (!$f['same'] && $f['billing'] === '')             $errors[] = 'Please enter the billing address.';
    if (!isset($methods[$f['payment']]))                 $errors[] = 'Please choose a payment method.';

    if (!preg_match('/^\d{6}$/', $f['postal'])) {
        $errors[] = 'Postal code must be 6 digits.';
    } else {
        $stmt = $pdo->prepare('SELECT 1 FROM serviceable_areas WHERE postal_code = ?');
        $stmt->execute([$f['postal']]);
        if (!$stmt->fetch()) $errors[] = 'Sorry, we do not deliver to that postal code yet.';
    }

    if (empty($errors)) {
        $billingAddress = $f['same'] ? $f['address'] : $f['billing'];
        try {
            $pdo->beginTransaction();

            foreach ($items as $it) {
                $stmt = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ? AND discontinued = 0');
                $stmt->execute([$it['qty'], $it['p']['id'], $it['qty']]);
                if ($stmt->rowCount() === 0) {
                    throw new RuntimeException('Sorry, "' . $it['p']['name'] . '" no longer has enough stock. Please update your cart.');
                }
            }

            $stmt = $pdo->prepare('INSERT INTO orders (customer_name, email, phone, shipping_address, billing_address, postal_code,
                                   payment_method, subtotal, tax, delivery_fee, total)
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$f['name'], $f['email'], $f['phone'], $f['address'], $billingAddress, $f['postal'],
                            $f['payment'], $subtotal, $tax, $delivery, $total]);
            $orderId = (int)$pdo->lastInsertId();

            $stmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity) VALUES (?, ?, ?, ?, ?)');
            foreach ($items as $it) {
                $stmt->execute([$orderId, $it['p']['id'], $it['p']['name'], $it['p']['price'], $it['qty']]);
            }

            if (!processPayment($f['payment'], $total)) {
                throw new RuntimeException('Payment failed. You have not been charged.');
            }

            $pdo->commit();

            $body = "Hi {$f['name']},\n\nThank you for your order " . orderCode($orderId) . ".\n\n";
            foreach ($items as $it) {
                $body .= $it['qty'] . ' x ' . $it['p']['name'] . ' - ' . money($it['p']['price'] * $it['qty']) . "\n";
            }
            $body .= "\nTotal paid: " . money($total) . "\n\nBakeflix";
            @mail($f['email'], 'Your Bakeflix order ' . orderCode($orderId), $body, 'From: orders@bakeflix.local');

            $_SESSION['cart']       = [];
            $_SESSION['last_order'] = $orderId;
            header('Location: order_success.php');
            exit;

        } catch (PDOException $ex) {
            $pdo->rollBack();
            $errors[] = 'Something went wrong while placing your order. Please try again.';
        } catch (RuntimeException $ex) {
            $pdo->rollBack();
            $errors[] = $ex->getMessage();
        }
    }
}

$pageTitle = 'Checkout';
require 'header.php';
?>
<h1>Checkout</h1>

<?php if ($errors): ?>
    <div class="errors" role="alert">
        <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="summary left">
    <h2>Your order</h2>
    <?php foreach ($items as $it): ?>
        <p><span><?= e($it['p']['name']) ?> &times; <?= $it['qty'] ?></span> <strong><?= money($it['p']['price'] * $it['qty']) ?></strong></p>
    <?php endforeach; ?>
    <p>Subtotal: <strong><?= money($subtotal) ?></strong></p>
    <p>Tax (<?= TAX_RATE * 100 ?>%): <strong><?= money($tax) ?></strong></p>
    <p>Delivery: <strong><?= $delivery ? money($delivery) : 'Free' ?></strong></p>
    <p class="grand">Total: <strong><?= money($total) ?></strong></p>
</div>

<form method="post" class="form">
    <h2>Recipient &amp; shipping</h2>
    <label for="name">Full name</label>
    <input type="text" id="name" name="name" value="<?= e($f['name']) ?>" required>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="<?= e($f['email']) ?>" required>

    <label for="phone">Phone (10 digits)</label>
    <input type="tel" id="phone" name="phone" value="<?= e($f['phone']) ?>" required>

    <label for="address">Shipping address</label>
    <textarea id="address" name="address" rows="3" required><?= e($f['address']) ?></textarea>

    <label for="postal">Postal code</label>
    <input type="text" id="postal" name="postal" value="<?= e($f['postal']) ?>" maxlength="6" required>

    <h2>Billing address</h2>
    <label class="inline"><input type="checkbox" name="same" <?= $f['same'] ? 'checked' : '' ?>> Same as shipping address</label>
    <label for="billing">Billing address (only if different)</label>
    <textarea id="billing" name="billing" rows="3"><?= e($f['billing']) ?></textarea>

    <h2>Payment method</h2>
    <?php foreach ($methods as $key => $label): ?>
        <label class="inline">
            <input type="radio" name="payment" value="<?= $key ?>" <?= $f['payment'] === $key ? 'checked' : '' ?>> <?= e($label) ?>
        </label>
    <?php endforeach; ?>
    <p class="muted">Demo mode: no real payment is taken and no card details are collected.</p>

    <button type="submit">Pay <?= money($total) ?></button>
</form>
<?php require 'footer.php'; ?>
