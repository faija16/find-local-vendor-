<?php
include 'includes/db.php';
include 'includes/header.php';

// Get product ID from URL and fetch from database
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        echo "<p>Product not found.</p>";
        include 'includes/footer.php';
        exit;
    }
} else {
    echo "<p>No product ID provided.</p>";
    include 'includes/footer.php';
    exit;
}
?>

<style>
.product-details-container {
    display: flex;
    flex-wrap: wrap;
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
    background: #fff;
    border-radius: 10px;
    gap: 40px;
}

.product-image-section {
    flex: 1;
    min-width: 300px;
    max-width: 400px;
}

.product-image-section img {
    width: 100%;
    height: 350px;
    border-radius: 10px;
    object-fit: contain;
    background-color: #f0f0f0;
}

.product-actions {
    margin-top: 20px;
    display: flex;
    gap: 15px;
}

.product-actions a {
    padding: 10px 20px;
    border: none;
    background-color: #2874f0;
    color: white;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
}

.product-actions a:hover {
    background-color: #0a58ca;
}

.product-info-section {
    flex: 2;
    min-width: 300px;
}

.product-info-section h2 {
    margin-bottom: 10px;
    font-size: 28px;
}

.product-info-section p.price {
    color: #388e3c;
    font-size: 24px;
    margin-bottom: 20px;
    font-weight: bold;
}

.product-info-section p.description {
    white-space: pre-line;
    font-size: 16px;
    color: #444;
    line-height: 1.6;
}
</style>

<div class="product-details-container">
    <div class="product-image-section">
        <!-- Check if image exists, otherwise provide a default image -->
        <img src="uploads/<?= htmlspecialchars($product['image1'] ?: 'default-product.jpg') ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        <div class="product-actions">
            <a href="#">Buy Now</a>
            <form method="POST" action="add_to_cart.php" style="display:inline;">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="number" name="quantity" value="1" min="1" required>
                <button type="submit" style="padding: 10px 20px; border-radius: 5px; background-color: #2874f0; color: white; border: none;">Add to Cart</button>
            </form>
        </div>
    </div>
    <div class="product-info-section">
        <h2>Product Name - <?= htmlspecialchars($product['name']) ?></h2>
        <p class="price">Rate - $<?= number_format($product['price'], 2) ?></p>
        <p class="description">Description - <?= nl2br(htmlspecialchars($product['description'])) ?></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
