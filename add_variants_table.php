<?php
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Check if table already exists
$check = $conn->query("SHOW TABLES LIKE 'product_variants'");
if ($check->num_rows == 0) {
    $sql = "CREATE TABLE product_variants (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        color_name VARCHAR(100) NOT NULL,
        color_hex VARCHAR(7) DEFAULT '#000000',
        price DECIMAL(10,2) NOT NULL,
        stock INT DEFAULT 10,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB";
    if ($conn->query($sql)) {
        echo "Table 'product_variants' created successfully.\n";
    } else {
        echo "Error: " . $conn->error . "\n";
    }
} else {
    echo "Table 'product_variants' already exists.\n";
}

// Add color column to cart table
$check2 = $conn->query("SHOW COLUMNS FROM cart LIKE 'color_name'");
if ($check2->num_rows == 0) {
    $conn->query("ALTER TABLE cart ADD COLUMN color_name VARCHAR(100) DEFAULT NULL AFTER product_id");
    $conn->query("ALTER TABLE cart ADD COLUMN color_hex VARCHAR(7) DEFAULT NULL AFTER color_name");
    echo "Added color columns to cart table.\n";
} else {
    echo "Color columns already exist in cart table.\n";
}

// Add color column to order_items table
$check3 = $conn->query("SHOW COLUMNS FROM order_items LIKE 'color_name'");
if ($check3->num_rows == 0) {
    $conn->query("ALTER TABLE order_items ADD COLUMN color_name VARCHAR(100) DEFAULT NULL AFTER product_id");
    $conn->query("ALTER TABLE order_items ADD COLUMN color_hex VARCHAR(7) DEFAULT NULL AFTER color_name");
    echo "Added color columns to order_items table.\n";
} else {
    echo "Color columns already exist in order_items table.\n";
}

$conn->close();
echo "Done.\n";
?>
