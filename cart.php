
<?php
session_start();
include 'db.php';

$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Shopping Cart</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<header class="navbar">
    <h2 class="logo">Flipkart - My Cart</h2>
    <a href="index.php" style="color:white;">
        Continue Shopping
    </a>
</header>

<section class="products">

    <h2>My Shopping Cart</h2>

    <?php if (empty($cart)) { ?>

        <p>Your cart is empty!</p>

    <?php } else { ?>

        <?php foreach ($cart as $product_id => $quantity) {

            $stmt = mysqli_prepare(
                $conn,
                "SELECT * FROM products WHERE id = ?"
            );

            mysqli_stmt_bind_param($stmt, "i", $product_id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $product = mysqli_fetch_assoc($result);

            if (!$product) {
                continue;
            }

            $subtotal = $product['price'] * $quantity;
            $total += $subtotal;
        ?>

            <div class="product-card">

                <h3>
                    <?php echo htmlspecialchars($product['name']); ?>
                </h3>

                <p>
                    Price: ₹<?php echo number_format($product['price'], 2); ?>
                </p>

                <p>
                    Subtotal: ₹<?php echo number_format($subtotal, 2); ?>
                </p>

                <form action="update_cart.php" method="POST">

                    <input type="hidden" name="product_id"
                           value="<?php echo $product_id; ?>">

                    <input type="number" name="quantity"
                           value="<?php echo $quantity; ?>"
                           min="1" required>

                    <button type="submit" name="action" value="update">
                        Update Quantity
                    </button>

                    <button type="submit" name="action" value="remove">
                        Remove
                    </button>

                </form>

            </div>

        <?php } ?>

        <h2>Total Amount: ₹<?php echo number_format($total, 2); ?></h2>
        <a href="checkout.php" class="checkout-btn">
    Proceed to Checkout
</a>

    <?php } ?>

</section>

</body>
</html>
