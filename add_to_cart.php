
<?php

session_start();
include 'db.php';

// Check whether a product ID was submitted
if (isset($_POST['product_id'])) {

    $product_id = (int) $_POST['product_id'];

    // Check whether the product exists
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id FROM products WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {

        // Create cart if it does not exist
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Increase quantity if already added
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]++;
        } else {
            $_SESSION['cart'][$product_id] = 1;
        }

        header("Location: index.php");
        exit();

    } else {
        echo "Product not found!";
    }

} else {
    echo "Invalid request!";
}

?>
