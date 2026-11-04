<?php
require_once 'db.php';
if (empty($_SESSION['admin'])) { header('Location: admin_login.php'); exit; }

$id = (int)($_GET['id'] ?? 0);

$p = ['category_id' => 1, 'name' => '', 'brand' => '', 'unit' => '', 'price' => '', 'stock' => '0',
      'sku' => '', 'icon' => '📦', 'description' => '', 'ingredients' => '', 'allergens' => '',
      'storage' => '', 'discontinued' => 0];

if ($id) {   
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) { header('Location: admin.php'); exit; }
    $p = $row;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['name', 'brand', 'unit', 'price', 'stock', 'sku', 'icon', 'description', 'ingredients', 'allergens', 'storage'] as $k) {
        $p[$k] = trim($_POST[$k] ?? '');
    }
    $p['category_id']  = (int)($_POST['category_id'] ?? 0);
    $p['discontinued'] = isset($_POST['discontinued']) ? 1 : 0;

    if ($p['name'] === '' || $p['brand'] === '' || $p['unit'] === '' || $p['sku'] === '') $errors[] = 'Name, brand, unit and SKU are required.';
    if (!is_numeric($p['price']) || $p['price'] < 0)  $errors[] = 'Price must be a number, 0 or more.';
    if (!ctype_digit($p['stock']))                     $errors[] = 'Stock must be a whole number, 0 or more.';
    if ($p['icon'] === '')                             $p['icon'] = '📦';

    if (empty($errors)) {
        $values = [$p['category_id'], $p['name'], $p['brand'], $p['unit'], $p['price'], $p['stock'], $p['sku'],
                   $p['description'], $p['ingredients'], $p['allergens'], $p['storage'], $p['icon'], $p['discontinued']];
        try {
            if ($id) {
                $sql = 'UPDATE products SET category_id=?, name=?, brand=?, unit=?, price=?, stock=?, sku=?,
                        description=?, ingredients=?, allergens=?, storage=?, icon=?, discontinued=? WHERE id=?';
                $values[] = $id;
            } else {
                $sql = 'INSERT INTO products (category_id, name, brand, unit, price, stock, sku,
                        description, ingredients, allergens, storage, icon, discontinued, added_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE())';
            }
            $pdo->prepare($sql)->execute($values);
            $_SESSION['msg'] = $id ? 'Product updated.' : 'Product added.';
            header('Location: admin.php');
            exit;
        } catch (PDOException $ex) {
            $errors[] = 'Could not save the product. Is the SKU already used by another product?';
        }
    }
}

$pageTitle = $id ? 'Edit product' : 'Add product';
require 'header.php';
?>
<h1><?= e($pageTitle) ?></h1>
<p><a href="admin.php">&laquo; Back to dashboard</a></p>

<?php if ($errors): ?>
    <div class="errors" role="alert"><ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<form method="post" class="form">
    <label for="category_id">Category</label>
    <select name="category_id" id="category_id">
        <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $p['category_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="name">Name</label>
    <input type="text" id="name" name="name" value="<?= e($p['name']) ?>" required>

    <label for="brand">Brand</label>
    <input type="text" id="brand" name="brand" value="<?= e($p['brand']) ?>" required>

    <label for="unit">Weight / volume unit (e.g. 500 g)</label>
    <input type="text" id="unit" name="unit" value="<?= e($p['unit']) ?>" required>

    <label for="price">Price (₹)</label>
    <input type="number" id="price" name="price" step="0.01" min="0" value="<?= e($p['price']) ?>" required>

    <label for="stock">Stock</label>
    <input type="number" id="stock" name="stock" min="0" value="<?= e($p['stock']) ?>" required>

    <label for="sku">SKU (must be unique)</label>
    <input type="text" id="sku" name="sku" value="<?= e($p['sku']) ?>" required>

    <label for="icon">Emoji icon</label>
    <input type="text" id="icon" name="icon" value="<?= e($p['icon']) ?>" maxlength="4">

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="3"><?= e($p['description']) ?></textarea>

    <label for="ingredients">Ingredients / materials</label>
    <textarea id="ingredients" name="ingredients" rows="2"><?= e($p['ingredients']) ?></textarea>

    <label for="allergens">Allergen notice</label>
    <textarea id="allergens" name="allergens" rows="2"><?= e($p['allergens']) ?></textarea>

    <label for="storage">Shelf life &amp; storage</label>
    <textarea id="storage" name="storage" rows="2"><?= e($p['storage']) ?></textarea>

    <label class="inline"><input type="checkbox" name="discontinued" <?= $p['discontinued'] ? 'checked' : '' ?>> Discontinued (hide from the shop)</label>

    <button type="submit">Save product</button>
</form>
<?php require 'footer.php'; ?>
