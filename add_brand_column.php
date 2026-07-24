<?php
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Check if column already exists
$check = $conn->query("SHOW COLUMNS FROM products LIKE 'product_brand'");
if ($check->num_rows == 0) {
    $sql = "ALTER TABLE products ADD COLUMN product_brand VARCHAR(100) DEFAULT 'General' AFTER product_category";
    if ($conn->query($sql)) {
        echo "Column 'product_brand' added successfully.\n";
        // Update existing products with sample brands
        $conn->query("UPDATE products SET product_brand = 'Apple' WHERE product_name LIKE '%iPhone%' OR product_name LIKE '%AirPods%'");
        $conn->query("UPDATE products SET product_brand = 'Samsung' WHERE product_name LIKE '%Samsung%' OR product_name LIKE '%Galaxy%'");
        $conn->query("UPDATE products SET product_brand = 'Apple' WHERE product_name LIKE '%Watch%' AND product_brand = 'General'");
        echo "Sample brands assigned to existing products.\n";
    } else {
        echo "Error: " . $conn->error . "\n";
    }
} else {
    echo "Column 'product_brand' already exists.\n";
}
$conn->close();
echo "Done.\n";
?>
