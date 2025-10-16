<?php
include 'includes/db.php';
include 'includes/header.php';
include 'includes/config.php';

$lat = $_GET['lat'] ?? null;
$lng = $_GET['lng'] ?? null;

echo "<h2>Nearby Vendor Products</h2>";

if ($lat && $lng) {
    $distanceLimit = 10; // kilometers

    $stmt = $pdo->prepare("
        SELECT p.*, u.latitude, u.longitude,
        (6371 * ACOS(
            COS(RADIANS(:lat)) * COS(RADIANS(u.latitude)) *
            COS(RADIANS(u.longitude) - RADIANS(:lng)) +
            SIN(RADIANS(:lat)) * SIN(RADIANS(u.latitude))
        )) AS distance
        FROM products p
        JOIN users u ON p.vendor_id = u.id
        WHERE u.latitude IS NOT NULL AND u.longitude IS NOT NULL
        HAVING distance <= :distance
        ORDER BY distance ASC
    ");

    $stmt->execute([
        ':lat' => $lat,
        ':lng' => $lng,
        ':distance' => $distanceLimit
    ]);

    $products = $stmt->fetchAll();

    if (count($products) > 0) {
        foreach ($products as $product) {
            echo "<div style='border:1px solid #ccc; padding:10px; margin:10px;'>
                    <strong>{$product['name']}</strong><br>
                    Price: ₹{$product['price']}<br>
                    Distance: " . round($product['distance'], 2) . " km<br>
                    <img src='uploads/{$product['image1']}' width='100' alt='Product Image'>
                  </div>";
        }
    } else {
        echo "<p>No products found nearby. Try increasing the distance or checking location settings.</p>";
    }
} else {
    echo "<p>Detecting your location...</p>";
}
?>

<script>
// If no lat/lng in URL, get from browser
if (!window.location.search.includes('lat=') && navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        window.location.href = `nearby.php?lat=${lat}&lng=${lng}`;
    }, function(error) {
        alert("Location access denied. Nearby search requires your location.");
    });
} else if (!navigator.geolocation) {
    alert("Geolocation is not supported by your browser.");
}
</script>
