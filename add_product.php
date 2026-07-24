<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['product_name'];
    $category = $_POST['product_category'];
    $brand = $_POST['product_brand'];
    if ($brand === 'Other' && !empty($_POST['product_brand_custom'])) {
        $brand = $_POST['product_brand_custom'];
    }
    $price = $_POST['product_price'];
    $description = $_POST['product_description'];

    // Handle image upload
    $image = $_FILES['product_image']['name'];
    $image = str_replace(' ', '_', $image);
    $target = "uploads/" . basename($image);

    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

    if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target)) {
        $sql = "INSERT INTO products (product_name, product_category, product_brand, product_price, product_description, product_image) 
                VALUES ('$name', '$category', '$brand', '$price', '$description', '$image')";
        if ($conn->query($sql) === TRUE) {
            $product_id = $conn->insert_id;
            
            // Insert color variants if provided
            if (isset($_POST['color_names']) && is_array($_POST['color_names'])) {
                $stmt = $conn->prepare("INSERT INTO product_variants (product_id, color_name, color_hex, price, stock) VALUES (?, ?, ?, ?, ?)");
                foreach ($_POST['color_names'] as $i => $cname) {
                    $cname = trim($cname);
                    if (empty($cname)) continue;
                    $cprice = !empty($_POST['color_price'][$i]) ? floatval($_POST['color_price'][$i]) : floatval($price);
                    $cstock = !empty($_POST['color_stock'][$i]) ? intval($_POST['color_stock'][$i]) : 10;
                    $chex = '#000000';
                    $stmt->bind_param("issdi", $product_id, $cname, $chex, $cprice, $cstock);
                    $stmt->execute();
                }
                $stmt->close();
            }
            
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Failed to upload image. Ensure the 'uploads' folder has correct permissions.";
    }
} else {
    echo "Invalid request method.";
}
$conn->close();
?>
