<?php
session_start();

// Database connection
include 'db.php';

// Ensure only sellers can access this functionality
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'seller') {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $price = $conn->real_escape_string($_POST['price']);
    $description = $conn->real_escape_string($_POST['description']);
    $stock = $conn->real_escape_string($_POST['stock']);
    
    // Handle image upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $image_tmp = $_FILES['product_image']['tmp_name'];
        $image_name = basename($_FILES['product_image']['name']);
        $upload_dir = 'uploads/';

        // Ensure the uploads directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $target_path = $upload_dir . $image_name;

        // Move uploaded file to uploads directory
        if (move_uploaded_file($image_tmp, $target_path)) {
            $image_path = $target_path; // Path to save in database

            // Insert product data into the database
            $sql = "INSERT INTO add_product (product_name, product_price, description, product_stock, img_path) VALUES ('$product_name', '$price', '$description', '$stock', '$image_path')";

            if ($conn->query($sql) === TRUE) {
                $_SESSION['success_message'] = "Product added successfully!";
                header("Location: seller.php");
            } else {
                $_SESSION['error_message'] = "Error: " . $conn->error;
                header("Location: seller_page.php");
            }
        } else {
            $_SESSION['error_message'] = "Failed to save uploaded image.";
            header("Location: seller_page.php");
        }
    } else {
        $_SESSION['error_message'] = "No image uploaded or there was an upload error.";
        header("Location: seller_page.php");
    }
}
?>
