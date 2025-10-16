<?php 
include 'includes/config.php';
include 'includes/db.php';
include 'includes/header.php'; // Include the header with navbar

// Get user coordinates from query parameters (if present)
$user_lat = $_GET['user_lat'] ?? null;
$user_lon = $_GET['user_lon'] ?? null;
$distance_limit = 10; // in KM

// Get filters for category, price, and search keyword
$selected_category = $_GET['category'] ?? '';
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 10000;
$search_keyword = $_GET['search'] ?? ''; // New search keyword

// Query products with filters, including search keyword
$query = "SELECT p.*, c.name AS category_name, u.latitude, u.longitude 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          LEFT JOIN users u ON p.vendor_id = u.id 
          WHERE 1=1";
$params = [];

if ($selected_category) {
    $query .= " AND p.category_id = ?";
    $params[] = $selected_category;
}
if ($min_price > 0) {
    $query .= " AND p.price >= ?";
    $params[] = $min_price;
}
if ($max_price < 10000) {
    $query .= " AND p.price <= ?";
    $params[] = $max_price;
}
if ($search_keyword) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search_keyword%";
    $params[] = "%$search_keyword%";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$all_products = $stmt->fetchAll();

// Filter products by distance if coordinates are provided
$products = [];
if ($user_lat && $user_lon) {
    foreach ($all_products as $product) {
        if (isset($product['latitude']) && isset($product['longitude'])) {
            $earth_radius = 6371; // km
            $lat_from = deg2rad($user_lat);
            $lon_from = deg2rad($user_lon);
            $lat_to = deg2rad($product['latitude']);
            $lon_to = deg2rad($product['longitude']);

            $lat_delta = $lat_to - $lat_from;
            $lon_delta = $lon_to - $lon_from;

            $angle = 2 * asin(sqrt(pow(sin($lat_delta / 2), 2) +
                cos($lat_from) * cos($lat_to) * pow(sin($lon_delta / 2), 2)));

            $distance = $earth_radius * $angle;

            // Only include products within the specified distance limit
            if ($distance <= $distance_limit) {
                $products[] = $product;
            }
        }
    }
} else {
    // If no location is provided, show all products
    $products = $all_products;
}

// Group products by category
$grouped = [];
foreach ($products as $product) {
    $category = $product['category_name'] ?? 'Uncategorized';
    $grouped[$category][] = $product;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nearby Vendors</title>
    <style>
        /* Your styling remains the same */
        .category-section { margin-bottom: 50px; }
        .category-title { margin-bottom: 10px; border-left: 5px solid blue; padding-left: 10px; font-size: 1.5em; }
        .arrow-nav { display: flex; align-items: center; position: relative; }
        .arrow { font-size: 30px; cursor: pointer; padding: 10px; user-select: none; }
        .scroll-container { display: flex; overflow-x: auto; gap: 20px; scroll-behavior: smooth; padding: 10px 0; width: 100%; }
        .product-card { flex: 0 0 calc((100% - 80px) / 5); box-sizing: border-box; border: 1px solid #ccc; padding: 10px; text-align: center; border-radius: 8px; background-color: #fff; }
        .product-card img { width: 100%; height: 150px; object-fit: cover; margin-bottom: 10px; }
        .product-card .btn { display: inline-block; padding: 6px 12px; background-color: blue; color: white; border-radius: 5px; text-decoration: none; }
    </style>
</head>
<body>

<!-- Nearby Button -->
<div style="text-align:center; margin: 20px 0;">
    <button onclick="getNearbyVendors()" style="padding:10px 20px; font-size:16px;">Show Nearby Vendors</button>
</div>

<script>
function getNearbyVendors() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            // Redirect to the same page with user_lat and user_lon query params
            window.location.href = `index.php?user_lat=${lat}&user_lon=${lon}`;
        }, function(error) {
            alert("Location access denied or unavailable.");
        });
    } else {
        alert("Geolocation is not supported by your browser.");
    }
}
</script>

<!-- Display the products grouped by category -->
<?php if (count($products) === 0): ?>
    <p>No products found. Try adjusting your search or filters.</p>
<?php else: ?>
    <?php foreach ($grouped as $category_name => $items): ?>
        <div class="category-section">
            <h2 class="category-title"><?= htmlspecialchars($category_name) ?></h2>
            <div class="arrow-nav">
                <span class="arrow">&#10094;</span>
                <div class="scroll-container" id="slider-<?= md5($category_name) ?>">
                    <?php foreach ($items as $product): ?>
                        <div class="product-card" onclick="window.location.href='product_details.php?id=<?= $product['id'] ?>'">
                            <div class="product-image">
                                <img src="uploads/<?= htmlspecialchars($product['image1']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
                            </div>
                            <div class="product-info">
                                <h3><?= htmlspecialchars($product['name']) ?></h3>
                                <p>$<?= number_format($product['price'], 2) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <span class="arrow">&#10095;</span>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>

<?php include 'includes/footer.php'; ?>
