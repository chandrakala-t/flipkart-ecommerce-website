
<?php
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        $message = "All fields are required!";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Enter a valid email address!";
    }
    elseif (strlen($password) < 6) {
        $message = "Password must contain at least 6 characters!";
    }
    else {
        $check = mysqli_prepare(
            $conn,
            "SELECT id FROM users WHERE email = ?"
        );

        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        $result = mysqli_stmt_get_result($check);

        if (mysqli_num_rows($result) > 0) {
            $message = "Email already registered!";
        }
        else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $name,
                $email,
                $hashed_password
            );

            if (mysqli_stmt_execute($stmt)) {
                $message = "Registration successful!";
            } else {
                $message = "Registration failed!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Flipkart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="products">
    <div class="product-card">
        <h2>Create Your Account</h2>

        <p><?php echo htmlspecialchars($message); ?></p>

        <form method="POST" action="register.php">
            <input type="text" name="name"
                   placeholder="Full Name" required>

            <br><br>

            <input type="email" name="email"
                   placeholder="Email Address" required>

            <br><br>

            <input type="password" name="password"
                   placeholder="Password (minimum 6 characters)"
                   minlength="6" required>

            <br><br>

            <button type="submit">Register</button>
        </form>

        <p>Already have an account?
            <a href="login.php">Login here</a>
        </p>

        <a href="index.php">Back to Home</a>
    </div>
</div>

</body>
</html>
