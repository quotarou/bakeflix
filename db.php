<?php

$dbHost = 'localhost';
$dbName = 'bakeflix';
$dbUser = 'root';
$dbPass = '';          

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $ex) {
    die('Could not connect to the database. Did you import schema.sql and check the settings in db.php?');
}

if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }

const TAX_RATE      = 0.05;  
const DELIVERY_FEE  = 50;    
const FREE_DELIVERY = 500;   

function e($text)  { return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8'); }
function money($n) { return '₹' . number_format($n, 2); }

function orderCode($id) { return 'BF' . str_pad($id, 6, '0', STR_PAD_LEFT); }

function getProduct($pdo, $id) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? AND discontinued = 0');
    $stmt->execute([$id]);
    return $stmt->fetch();   
}

function productImage($p, $class = 'thumb-img') {
    if (!empty($p['image'])) {
        return '<img class="' . $class . '" src="images/' . e($p['image']) . '" alt="' . e($p['name']) . '">';
    }
    return '<span role="img" aria-label="' . e($p['name']) . '">' . e($p['icon']) . '</span>';
}

?>