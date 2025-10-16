<?php
include 'includes/header.php';
include 'includes/db.php';

$stmt = $pdo->query("SELECT * FROM vendors WHERE name IS NOT NULL AND experience IS NOT NULL");
$vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 style="text-align:center;">Our Service Vendors</h2>
<div style="display:flex; flex-wrap:wrap; justify-content:center;">
    <?php foreach ($vendors as $vendor): ?>
        <div style="border:1px solid #ccc; margin:10px; padding:20px; width:300px;">
            <h3><?= htmlspecialchars($vendor['name']) ?></h3>
            <p><strong>Experience:</strong> <?= htmlspecialchars($vendor['experience']) ?> years</p>
            <p><strong>Skills:</strong> <?= htmlspecialchars($vendor['skills']) ?></p>
            <p><strong>Service Area:</strong> <?= htmlspecialchars($vendor['service_area']) ?></p>
        </div>
    <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>
