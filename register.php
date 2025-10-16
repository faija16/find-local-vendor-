<?php
session_start();
include 'includes/db.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!in_array($role, ['user', 'vendor', 'service_vendor'])) {
        $error = "Invalid role selected.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashed_password, $role]);

            $userId = $pdo->lastInsertId();
            if ($role === 'service_vendor') {
                $stmt2 = $pdo->prepare("INSERT INTO service_vendors (user_id, name) VALUES (?, ?)");
                $stmt2->execute([$userId, $name]);
            }

            header("Location: login.php?registered=1");
            exit;
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<?php include 'includes/header.php';?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body { font-family: Arial; background: #f2f2f2; }
        .form-container {
            max-width: 400px; margin: 60px auto; padding: 20px;
            background: #fff; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; }
        input, select {
            width: 100%; padding: 10px; margin: 8px 0;
            border: 1px solid #ccc; border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #4CAF50; color: white;
            cursor: pointer; border: none;
        }
        input[type="submit"]:hover { background-color: #45a049; }
        .error { color: red; text-align: center; }
        .login-link { text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Register</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
        <input name="name" placeholder="Full Name" required>
        <input name="email" type="email" placeholder="Email" required>
        <input name="password" type="password" placeholder="Password" required>
        <input name="confirm_password" type="password" placeholder="Confirm Password" required>
        <select name="role" required>
            <option value="">Select Role</option>
            <option value="user">User</option>
            <option value="vendor">Product Vendor</option>
            <option value="service_vendor">Service Vendor</option>
        </select>
        <input type="submit" value="Register">
    </form>
    <div class="login-link">
        Already have an account? <a href="login.php">Login here</a>
    </div>
</div>
</body>
</html>
<?php include 'includes/footer.php'; ?>