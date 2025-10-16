<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $experience = $_POST['experience'] ?? '';
    $skills = $_POST['skills'] ?? '';
    $service_area = $_POST['service_area'] ?? '';

    // Check if details already exist
    $checkStmt = $pdo->prepare("SELECT * FROM service_vendors WHERE user_id = ?");
    $checkStmt->execute([$user_id]);
    $existing = $checkStmt->fetch();

    if ($existing) {
        $updateStmt = $pdo->prepare("UPDATE service_vendors SET experience = ?, skills = ?, service_area = ? WHERE user_id = ?");
        $updateStmt->execute([$experience, $skills, $service_area, $user_id]);
        $message = "Details updated successfully.";
    } else {
        $insertStmt = $pdo->prepare("INSERT INTO service_vendors (user_id, experience, skills, service_area) VALUES (?, ?, ?, ?)");
        $insertStmt->execute([$user_id, $experience, $skills, $service_area]);
        $message = "Details added successfully.";
    }
}

// Fetch latest vendor info
$stmt = $pdo->prepare("SELECT * FROM service_vendors WHERE user_id = ?");
$stmt->execute([$user_id]);
$vendor = $stmt->fetch();
?>

<h2>Welcome to Your Service Vendor Dashboard</h2>

<?php if (!empty($message)): ?>
    <p style="color: green;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if (!$vendor): ?>
    <p>You haven't added your service details yet. Please fill in the form below.</p>
<?php endif; ?>

<form method="POST">
    <label>Experience (in years):</label><br>
    <input type="number" name="experience" value="<?= htmlspecialchars($vendor['experience'] ?? '') ?>" required><br><br>

    <label>Skills:</label><br>
    <textarea name="skills" required><?= htmlspecialchars($vendor['skills'] ?? '') ?></textarea><br><br>

    <label>Service Area:</label><br>
    <input type="text" name="service_area" value="<?= htmlspecialchars($vendor['service_area'] ?? '') ?>" required><br><br>

    <input type="submit" value="<?= $vendor ? 'Update Details' : 'Add Details' ?>">
</form>

<?php if ($vendor): ?>
    <hr>
    <h3>Your Submitted Details</h3>
    <p><strong>Experience:</strong> <?= htmlspecialchars($vendor['experience']) ?> years</p>
    <p><strong>Skills:</strong> <?= htmlspecialchars($vendor['skills']) ?></p>
    <p><strong>Service Area:</strong> <?= htmlspecialchars($vendor['service_area']) ?></p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
