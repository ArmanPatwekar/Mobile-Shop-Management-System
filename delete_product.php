<?php
// Database connection
$conn =mysqli_connect('localhost', 'root', '', 'mobile_shop');

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if 'id' is passed via GET
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize the input

    // Delete the product
    $delete_query = "DELETE FROM products WHERE id = $id";

    if ($conn->query($delete_query) === TRUE) {
        // Reset auto-increment to 1 if no products remain
        $check = $conn->query("SELECT COUNT(*) as cnt FROM products");
        $remaining = $check->fetch_assoc()['cnt'];
        if ($remaining == 0) {
            $conn->query("ALTER TABLE products AUTO_INCREMENT = 1");
        }
        header('Location: index.php');
        exit;
    } else {
        echo "Error deleting product: " . $conn->error;
    }
} else {
    die("Invalid product ID!");
}
?>