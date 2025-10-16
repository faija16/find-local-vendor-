<?php
session_start();
session_unset();
session_destroy();

include 'includes/config.php'; // make sure this sets $base_url

// Redirect to login using base URL
header("Location: {$base_url}/login.php");
exit;
?>
