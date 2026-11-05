
<?php
session_start();
include 'db.php';

// Customer must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$cart = $_SESSION['cart'] ?? [];
$message = "";

// Handle order placement
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($cart)) {

    mysqli_begin_transaction($conn);

    try {
        $items = [];
        $total = 0;

        // Fetch current prices and stock from database
        foreach ($cart as $product_id => $quantity) {

            $product_id = (int) $product_id;
            $quantity = (int) $quantity;

            if ($product_id <= 0 || $quantity <= 0) {
                throw new Exception("Invalid cart details.");
            }

            $stmt = mysqli_prepare(
                $conn,
                "SELECT id, name, price, stock FROM products WHERE id = ?"
            );

            mysqli_stmt_bind_param($stmt, "i", $product_id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $product = mysqli_fetch_assoc($result);

            if (!$product || $quantity > $product['stock']) {
                throw new Exception("Product unavailable or insufficient stock.");
            }

            $subtotal = $product['price'] * $quantity;
            $total += $subtotal;

            $items[] = [
                'id' => $product_id,
                'quantity' => $quantity,
                'price' => $product['price']
            ];
        }

        if (empty($items)) {
            throw new Exception("Your cart is empty.");
        }

        // Insert main order
        $status = "Pending";

        $orderStmt = mysqli_prepare(
            $conn,
            "INSERT INTO orders (user_id, total_amount, status)
             VALUES (?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $orderStmt,
            "ids",
            $user_id,
            $total,
            $status
        );

        mysqli_stmt_execute($orderStmt);

        $order_id = mysqli_insert_id($conn);

        // Insert individual order items and reduce stock
        foreach ($items as $item) {

            $itemStmt = mysqli_prepare(
                $conn,
                "INSERT INTO order_items
                 (order_id, product_id, quantity, price)
                 VALUES (?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $itemStmt,
                "iiid",
                $order_id,
                $item['id'],
                $item['quantity'],
                $item['price']
            );

            mysqli_stmt_execute($itemStmt);

            $stockStmt = mysqli_prepare(
                $conn,
                "UPDATE products SET stock = stock - ?
                 WHERE id = ? AND stock >= ?"
            );

            mysqli_stmt_bind_param(
                $stockStmt,
                "iii",
                $item['quantity'],
                $item['id'],
                $item['quantity']
            );

            mysqli_stmt_execute($stockStmt);

            if (mysqli_stmt_affected_rows($stockStmt) != 1) {
                throw new Exception("Stock changed. Please try again.");
            }
        }

        mysqli_commit($conn);

        // Clear cart after successful order
        unset($_SESSION['cart']);

        header("Location: checkout.php?success=" . $order_id);
        exit();

    } catch (Exception $e) {

        mysqli_rollback($conn);
        $message = $e->getMessage();
    }
}

// Display success message
if (isset($_GET['success'])) {
    $message = "Your order #" .
        (int) $_GET['success'] .
        " has been placed successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Flipkart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="navbar">
    <h2 class="logo">Flipkart - Checkout</h2>
    <a href="index.php" class="nav-link">Home</a>
    <a href="cart.php" class="nav-link">Cart</a>
</header>

<section class="products">

    <div class="product-card">

        <h2>Checkout</h2>

        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>

        <?php if (isset($_GET['success'])) { ?>

            <h3>Thank you for shopping!</h3>
            <a href="index.php">Continue Shopping</a>

        <?php } elseif (!empty($cart)) { ?>

            <h3>Order Summary</h3>

            <?php
            $total = 0;

            foreach ($cart as $product_id => $quantity) {

                $stmt = mysqli_prepare(
                    $conn,
                    "SELECT name, price FROM products WHERE id = ?"
                );

                mysqli_stmt_bind_param($stmt, "i", $product_id);
                mysqli_stmt_execute($stmt);

                $result = mysqli_stmt_get_result($stmt);
                $product = mysqli_fetch_assoc($result);

                if ($product) {
                    $subtotal = $product['price'] * $quantity;
                    $total += $subtotal;
            ?>

                <p>
                    <?php echo htmlspecialchars($product['name']); ?>
                    - Quantity: <?php echo (int)$quantity; ?>
                    - ₹<?php echo number_format($subtotal, 2); ?>
                </p>

            <?php
                }
            }
            ?>

            <h3>Total: ₹<?php echo number_format($total, 2); ?></h3>

            <form method="POST" action="checkout.php">
                <button type="submit">Place Order</button>
            </form>

        <?php } else { ?>

            <p>Your cart is empty.</p>
            <a href="index.php">Continue Shopping</a>

        <?php } ?>

    </div>

</section>

</body>
</html>
