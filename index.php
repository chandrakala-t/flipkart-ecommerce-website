
<?php
session_start();
include 'db.php';

// Get selected category from URL
$category_id = isset($_GET['category'])
    ? (int) $_GET['category']
    : 0;

// Get search text from search form
$search = isset($_GET['search'])
    ? trim($_GET['search'])
    : '';

// Prepare product query
if ($search !== '' && $category_id > 0) {

    // Search products within selected category
    $searchTerm = "%" . $search . "%";

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM products
         WHERE category_id = ?
         AND (name LIKE ? OR description LIKE ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "iss",
        $category_id,
        $searchTerm,
        $searchTerm
    );

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

} elseif ($search !== '') {

    // Search across all products
    $searchTerm = "%" . $search . "%";

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM products
         WHERE name LIKE ? OR description LIKE ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ss",
        $searchTerm,
        $searchTerm
    );

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

} elseif ($category_id > 0) {

    // Show products from selected category
    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM products WHERE category_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $category_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

} else {

    // Show all products
    $result = mysqli_query($conn, "SELECT * FROM products");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Flipkart - Online Shopping</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<!-- Navigation Bar -->

<header class="navbar">

    <div class="nav-left">
        <h2 class="logo">Flipkart</h2>
        <span class="tagline">Explore Plus</span>
    </div>

    <!-- Functional Search Bar -->

    <form action="index.php" method="GET" class="nav-search">

        <?php if ($category_id > 0) { ?>
            <input type="hidden" name="category"
                   value="<?php echo $category_id; ?>">
        <?php } ?>

        <input
            type="text"
            name="search"
            placeholder="Search for products, brands and more"
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <button type="submit">Search</button>

    </form>

    <div class="nav-right">

        <?php if (isset($_SESSION['user_id'])) { ?>

            <span class="welcome-user">
                Welcome,
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>

            <a href="logout.php" class="nav-link">Logout</a>

        <?php } else { ?>

            <a href="login.php" class="nav-link">Login</a>

        <?php } ?>

        <a href="register.php" class="nav-link">Register</a>

        <a href="cart.php" class="nav-link">Cart</a>

        <a href="#" class="nav-link">Become a Seller</a>

    </div>

</header>


<!-- Categories -->

<section class="categories">

    <a href="index.php"
       class="<?php echo ($category_id == 0) ? 'active' : ''; ?>">
        All Products
    </a>

    <a href="index.php?category=1"
       class="<?php echo ($category_id == 1) ? 'active' : ''; ?>">
        Mobiles
    </a>

    <a href="index.php?category=3"
       class="<?php echo ($category_id == 3) ? 'active' : ''; ?>">
        Fashion
    </a>

    <a href="index.php?category=2"
       class="<?php echo ($category_id == 2) ? 'active' : ''; ?>">
        Electronics
    </a>

    <a href="index.php?category=4"
       class="<?php echo ($category_id == 4) ? 'active' : ''; ?>">
        Home
    </a>

    <a href="index.php?category=5"
       class="<?php echo ($category_id == 5) ? 'active' : ''; ?>">
        Appliances
    </a>

    <a href="index.php?category=6"
       class="<?php echo ($category_id == 6) ? 'active' : ''; ?>">
        Books
    </a>

</section>


<!-- Main Banner -->

<section class="banner">

    <h1>Big Saving Days!</h1>

    <p>
        Discover amazing deals on your favourite products.
    </p>

</section>


<!-- Products Section -->

<section class="products">

    <?php if ($search !== '') { ?>

        <h2>
            Search Results for:
            "<?php echo htmlspecialchars($search); ?>"
        </h2>

    <?php } elseif ($category_id > 0) { ?>

        <h2>Category Products</h2>

    <?php } else { ?>

        <h2>Featured Products</h2>

    <?php } ?>


    <div class="product-container">

        <?php

        if ($result && mysqli_num_rows($result) > 0) {

            while ($product = mysqli_fetch_assoc($result)) {

        ?>

            <div class="product-card">

                <!-- Product Image -->

                <img
                    src="<?php echo htmlspecialchars($product['image']); ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                    class="product-image"
                    style="width:120px; height:120px; max-width:120px; object-fit:contain; display:block; margin:0 auto 12px;"
                >

                <!-- Product Name -->

                <h3>
                    <a href="product_details.php?id=<?php echo (int)$product['id']; ?>">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </a>
                </h3>

                <!-- Product Description -->

                <p>
                    <?php echo htmlspecialchars($product['description']); ?>
                </p>

                <!-- Product Price -->

                <h3>
                    ₹<?php echo htmlspecialchars($product['price']); ?>
                </h3>

                <!-- Available Stock -->

                <p>
                    Available Stock:
                    <?php echo htmlspecialchars($product['stock']); ?>
                </p>

                <!-- Add to Cart -->

                <form action="add_to_cart.php" method="POST">

                    <input
                        type="hidden"
                        name="product_id"
                        value="<?php echo (int)$product['id']; ?>"
                    >

                    <button type="submit">
                        Add to Cart
                    </button>

                </form>

            </div>

        <?php

            }

        } else {

            if ($search !== '') {
                echo "<p>No products found matching your search.</p>";
            } else {
                echo "<p>No products available.</p>";
            }

        }

        ?>

    </div>

</section>

</body>
</html>
