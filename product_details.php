
<?php
session_start();
include 'db.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: index.php");
    exit();
}

$product_id = (int) $_GET['id'];

$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "Product not found!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="navbar">
    <h2 class="logo">Flipkart</h2>
    <a href="index.php" class="nav-link">Home</a>
    <a href="cart.php" class="nav-link">Cart</a>
</header>

<section class="products">
    <div class="product-card">

        <h2><?php echo htmlspecialchars($product['name']); ?></h2>
        <img
    src="<?php echo htmlspecialchars($product['image']); ?>"
    alt="<?php echo htmlspecialchars($product['name']); ?>"
    style="width: 220px;
           height: 220px;
           object-fit: contain;
           display: block;
           margin: 15px auto;"
>

        <h3>
            Price: ₹<?php echo number_format($product['price'], 2); ?>
        </h3>

        <p>
            Description:
            <?php echo htmlspecialchars($product['description'] ?? 'No description available'); ?>
        </p>

        <p>
            Available Stock:
            <?php echo (int)$product['stock']; ?>
        </p>

        <?php if ($product['stock'] > 0) { ?>
            <form action="add_to_cart.php" method="POST">
                <input type="hidden" name="product_id"
                       value="<?php echo $product['id']; ?>">
                <button type="submit">Add to Cart</button>
            </form>
        <?php } else { ?>
            <p>Out of Stock</p>
        <?php } ?>

    </div>
</section>

</body>
</html>
