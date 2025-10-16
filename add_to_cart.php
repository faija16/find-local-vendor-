<?php
session_start();
include 'includes/db.php'; // Database connection

// Check if the request method is POST and necessary data is sent
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate product ID and quantity
    if (!isset($_POST['product_id']) || !isset($_POST['quantity']) || !is_numeric($_POST['quantity']) || $_POST['quantity'] <= 0) {
        echo "Invalid request. Please check the product and quantity.";
        exit;
    }

    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Validate product ID to be an integer to prevent SQL Injection
    if (!filter_var($product_id, FILTER_VALIDATE_INT)) {
        echo "Invalid product ID.";
        exit;
    }

    // Check if the product exists in the database
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        echo "Product not found.";
        exit;
    }

    // Initialize cart if not already done
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if the product is already in the cart and update quantity
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity; // Update quantity if already in cart
    } else {
        $_SESSION['cart'][$product_id] = $quantity; // Add new product to cart
    }

    // Redirect to cart page after adding the product
    header('Location: cart.php');
    exit();
} else {
    echo "Invalid request method.";
}
?>
