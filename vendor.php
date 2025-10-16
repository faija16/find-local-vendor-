<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM service_vendors WHERE name IS NOT NULL AND experience IS NOT NULL");
$vendors = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Our Vendors</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            padding: 20px 0;
            color: #333;
        }

        .vendor-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .vendor-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.08);
            padding: 20px;
            width: 300px;
            transition: transform 0.3s ease;
        }

        .vendor-card:hover {
            transform: translateY(-5px);
        }

        .vendor-card h3 {
            margin-top: 0;
            color: #007BFF;
        }

        .vendor-card p {
            margin: 8px 0;
            color: #333;
        }

        .contact-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 12px;
            background-color: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.3s;
        }

        .contact-btn:hover {
            background-color: #0056b3;
        }

        @media (max-width: 600px) {
            .vendor-card {
                width: 90%;
            }
        }
    </style>
</head>
<body>

<h2>Our Verified Service Vendors</h2>
<div class="vendor-list">
    <?php foreach ($vendors as $vendor): ?>
        <div class="vendor-card">
            <h3><?= htmlspecialchars($vendor['name']) ?></h3>
            <p><strong>Experience:</strong> <?= htmlspecialchars($vendor['experience']) ?> years</p>
            <p><strong>Skills:</strong> <?= htmlspecialchars($vendor['skills']) ?></p>
            <p><strong>Service Area:</strong> <?= htmlspecialchars($vendor['service_area']) ?></p>
           <a href="vendor/contact_vendor.php?id=<?= $vendor['user_id'] ?>">Contact Vendor</a>
           


        </div>
    <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
