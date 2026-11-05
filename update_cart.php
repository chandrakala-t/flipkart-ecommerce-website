
<?php
session_start();
include 'db.php';

// Check whether the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $action = $_POST['action'] ?? '';

    // Check whether the product exists in the cart
    if (!$product_id || !isset($_SESSION['cart'][$product_id])) {
        header("Location: cart.php");
        exit();
    }

    // Remove product from cart
    if ($action == "remove") {
        unset($_SESSION['cart'][$product_id]);
    }

    // Update product quantity
    elseif ($action == "update") {

        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

        if ($quantity && $quantity > 0) {

            $stmt = mysqli_prepare(
                $conn,
                "SELECT stock FROM products WHERE id = ?"
            );

            mysqli_stmt_bind_param($stmt, "i", $product_id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $product = mysqli_fetch_assoc($result);

            if ($product && $quantity <= $product['stock']) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                echo "Requested quantity is not available in stock.";
                exit();
            }

        } else {
            echo "Please enter a valid quantity.";
            exit();
        }
    }

    // Return to cart page
    header("Location: cart.php");
    exit();
}

header("Location: cart.php");
exit();
?>
