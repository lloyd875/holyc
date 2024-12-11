<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_id'])) {
    $userId = $_SESSION['id'];
    $productId = $conn->real_escape_string($_POST['product_id']);

    // Check if the product is already in favorites
    $checkSql = "SELECT * FROM favorites WHERE user_id = '$userId' AND product_id = '$productId'";
    $result = $conn->query($checkSql);

    if ($result && $result->num_rows > 0) {
        // Product is already in favorites
        header("Location: buyer.php?message=already_added");
    } else {
        // Insert the product into the favorites database table
        $insertSql = "INSERT INTO favorites (user_id, product_id) VALUES ('$userId', '$productId')";
        
        if ($conn->query($insertSql) === TRUE) {
            header("Location: buyer.php?message=added_to_favorites");
        } else {
            header("Location: buyer.php?error=failed_to_add");
        }
    }
} else {
    header("Location: index.php");
}
?>
