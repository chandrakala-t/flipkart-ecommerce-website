
<?php
session_start();

// Clear all session variables
$_SESSION = [];

// Destroy the current session
session_destroy();

// Redirect to homepage
header("Location: index.php");
exit();
?>
