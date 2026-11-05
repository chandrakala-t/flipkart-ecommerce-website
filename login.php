
<?php
session_start();
include 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check customer details in database
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, name, email, password FROM users WHERE email = ?"
    );

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {

        // Verify password
        if (password_verify($password, $user['password'])) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            header("Location: index.php");
            exit();

        } else {
            $error = "Incorrect email or password!";
        }

    } else {
        $error = "Incorrect email or password!";
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Customer Login - Flipkart</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f1f3f6;
            min-height: 100vh;
        }

        /* Navigation Bar */
        .navbar {
            background-color: #2874f0;
            color: white;
            padding: 18px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-size: 25px;
            font-weight: bold;
        }

        .home-link {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        /* Login Container */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 85vh;
            padding: 20px;
        }

        /* Login Card */
        .login-box {
            background-color: white;
            width: 100%;
            max-width: 420px;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        }

        .login-box h2 {
            text-align: center;
            color: #2874f0;
            margin-bottom: 10px;
            font-size: 27px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
            font-size: 14px;
        }

        /* Input Fields */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 13px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            font-size: 15px;
        }

        .form-group input:focus {
            border-color: #2874f0;
        }

        /* Login Button */
        .login-btn {
            width: 100%;
            padding: 13px;
            background-color: #fb641b;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 5px;
        }

        .login-btn:hover {
            background-color: #e85b16;
        }

        /* Error Message */
        .error {
            background-color: #ffe5e5;
            color: #d00000;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 18px;
            text-align: center;
            font-size: 14px;
        }

        /* Register Link */
        .register-link {
            text-align: center;
            margin-top: 22px;
            color: #555;
            font-size: 14px;
        }

        .register-link a {
            color: #2874f0;
            text-decoration: none;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 500px) {
            .login-box {
                padding: 25px 20px;
            }

            .navbar {
                padding: 15px 20px;
            }
        }

    </style>
</head>

<body>

    <!-- Navigation Bar -->
    <header class="navbar">

        <h2 class="logo">Flipkart</h2>

        <a href="index.php" class="home-link">
            Home
        </a>

    </header>

    <!-- Customer Login -->
    <div class="login-container">

        <div class="login-box">

            <h2>Customer Login</h2>

            <p class="subtitle">
                Welcome back! Login to continue shopping.
            </p>

            <?php if (!empty($error)) { ?>
                <div class="error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php } ?>

            <form method="POST" action="login.php">

                <div class="form-group">

                    <label for="email">Email Address</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                    >

                </div>

                <div class="form-group">

                    <label for="password">Password</label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >

                </div>

                <button type="submit" class="login-btn">
                    Login
                </button>

            </form>

            <div class="register-link">

                Don't have an account?

                <a href="register.php">
                    Create Account
                </a>

            </div>

        </div>

    </div>

</body>
</html>
