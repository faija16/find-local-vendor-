session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch vendor from service_vendors table
    $stmt = $pdo->prepare("SELECT * FROM service_vendors WHERE email = ?");
    $stmt->execute([$email]);
    $vendor = $stmt->fetch();

    if ($vendor && password_verify($password, $vendor['password'])) {
        $_SESSION['vendor_id'] = $vendor['id'];
        header("Location: service_dashboard.php");
        exit;
    } else {
        echo "Invalid email or password.";
    }
}
