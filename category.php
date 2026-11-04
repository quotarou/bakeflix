<?php
require 'header.php';

$slug = $_GET['cat'] ?? 'all';
$q    = trim($_GET['q'] ?? '');
$sort = $_GET['sort'] ?? 'name';

$orders = [
    'name'       => 'p.name ASC',
    'price_asc'  => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'newest'     => 'p.added_at DESC',
];
if (!isset($orders[$sort])) { $sort = 'name'; }

$heading = 'All products';
if ($slug !== 'all') {
    $stmt = $pdo->prepare('SELECT name FROM categories WHERE slug = ?');
    $stmt->execute([$slug]);
    $found = $stmt->fetch();
    if ($found) { $heading = $found['name']; } else { $slug = 'all'; }
}
if ($q !== '') { $heading = 'Search results for "' . $q . '"'; }

$sql    = 'SELECT p.* FROM products p JOIN categories c ON p.category_id = c.id WHERE p.discontinued = 0';
$params = [];
if ($slug !== 'all') {
    $sql .= ' AND c.slug = ?';
    $params[] = $slug;
}
if ($q !== '') {
    $sql .= ' AND (p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?)';
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like);
}
$sql .= ' ORDER BY ' . $orders[$sort];

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$list = $stmt->fetchAll();
?>
<h1><?= e($heading) ?></h1>

<form method="get" class="sort-form">
    <input type="hidden" name="cat" value="<?= e($slug) ?>">
    <input type="hidden" name="q" value="<?= e($q) ?>">
    <label for="sort">Sort by:</label>
    <select name="sort" id="sort" onchange="this.form.submit()">
        <option value="name"       <?= $sort === 'name' ? 'selected' : '' ?>>Alphabetical</option>
        <option value="price_asc"  <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: low to high</option>
        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: high to low</option>
        <option value="newest"     <?= $sort === 'newest' ? 'selected' : '' ?>>Newest arrivals</option>
    </select>
    <noscript><button type="submit">Apply</button></noscript>
</form>

<?php if (empty($list)): ?>
    <p>No products found.</p>
<?php else: ?>
    <div class="grid">
        <?php foreach ($list as $p): ?>
            <?php include 'product_card.php'; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require 'footer.php'; ?>
