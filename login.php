<?php
session_start();  // Start the session at the very beginning
include 'includes/db.php';  // Database connection should come after session start
  // Ensure no output in header.php before session_start

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        // Redirect based on user role
        if ($user['role'] === 'user') {
            header("Location: index.php");
            exit;
        } elseif ($user['role'] === 'vendor') {
            header("Location: vendor/dashboard.php");
            exit;
        } elseif ($user['role'] === 'service_vendor') {
            header("Location: vendor/service_dashboard.php");
            exit;
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: Arial; background: #f2f2f2; }
        .form-container {
            max-width: 400px; margin: 60px auto; padding: 20px;
            background: #fff; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; }
        input {
            width: 100%; padding: 10px; margin: 8px 0;
            border: 1px solid #ccc; border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #4CAF50; color: white;
            cursor: pointer; border: none;
        }
        input[type="submit"]:hover { background-color: #45a049; }
        .error { color: red; text-align: center; }
        .register-link { text-align: center; margin-top: 10px; }
        .success { text-align: center; color: green; }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Login</h2>
    <?php if (!empty($_GET['registered'])): ?>
        <p class="success">Registration successful. You can now log in.</p>
    <?php endif; ?>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
        <input name="email" type="email" placeholder="Email" required>
        <input name="password" type="password" placeholder="Password" required>
        <input type="submit" value="Login">
    </form>
    <div class="register-link">
        Don't have an account? <a href="register.php">Register here</a>
    </div>
</div>
</body>
</html>

<?php include 'includes/footer.php'; ?>
