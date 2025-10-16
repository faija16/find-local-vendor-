<?php
ob_start();
session_start();
include 'includes/db.php'; 
include 'includes/header.php'; 

echo "<h1>Your Shopping Cart</h1>";

// Handle removing an item from the cart
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    // Remove the product from the cart
    if (isset($_SESSION['cart'][$remove_id])) {
        unset($_SESSION['cart'][$remove_id]);
        echo "<p style='color:red;'>Item removed from your cart.</p>";
    }
    // Redirect to the cart page after removal
    header('Location: cart.php');
    exit();
}

if (empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty.</p>";
} else {
    echo "<table border='1' cellpadding='10' cellspacing='0' style='width:80%; margin:auto; text-align:center;'>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
            </tr>";

    $grand_total = 0;

    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        // Fetch product details from the database
        $stmt = $pdo->prepare("SELECT name, price FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product) {
            $total = $product['price'] * $quantity;
            $grand_total += $total;

            echo "<tr>
                    <td>".htmlspecialchars($product['name'])."</td>
                    <td>$".number_format($product['price'], 2)."</td>
                    <td>$quantity</td>
                    <td>$".number_format($total, 2)."</td>
                    <td><a href='cart.php?remove=$product_id' style='color:red;'>Remove</a></td>
                  </tr>";
        } else {
            // If the product is not found in the database, remove it from the cart
            unset($_SESSION['cart'][$product_id]);
        }
    }

    echo "<tr>
            <td colspan='3'><strong>Grand Total</strong></td>
            <td colspan='2'><strong>$".number_format($grand_total, 2)."</strong></td>
          </tr>";
    echo "</table>";
}
ob_end_flush();
?>

<?php if (!empty($_SESSION['cart'])): ?>
    <div style="text-align:center; margin-top:20px;">
        <a href="checkout.php" style="background:blue; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;">Proceed to Checkout</a>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
