<?php
session_start();
include 'db.php';

// Ensure only authorized users can edit
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'seller') {
    header("Location: login.php");
    exit;
}

// Get the product ID from the URL
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $query = $conn->prepare("SELECT * FROM add_product WHERE id = ?");
    $query->bind_param("i", $product_id);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
    } else {
        $_SESSION['error_message'] = "Product not found.";
        header("Location: seller.php");
        exit;
    }
} else {
    $_SESSION['error_message'] = "Invalid request.";
    header("Location: seller.php");
    exit;
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $price = $conn->real_escape_string($_POST['price']);
    $description = $conn->real_escape_string($_POST['description']);
    $stock = $conn->real_escape_string($_POST['stock']);

    $update_sql = "UPDATE add_product SET product_name = '$product_name', product_price = '$price', description = '$description', product_stock = '$stock' WHERE id = '$product_id'";

    if ($conn->query($update_sql) === TRUE) {
        $_SESSION['success_message'] = "Product updated successfully!";
        header("Location: seller.php");
    } else {
        $_SESSION['error_message'] = "Error updating product: " . $conn->error;
    }
}
?>

<!-- Edit Product Page with Design -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="seller.php">Product Management</a>
        </div>
    </nav>

    <!-- Edit Product Form -->
    <div class="container mt-3">
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h4>Edit Product</h4>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="productName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="productName" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="productPrice" class="form-label">Price ($)</label>
                        <input type="number" step="0.01" class="form-control" id="productPrice" name="price" value="<?php echo htmlspecialchars($product['product_price']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="productDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="productDescription" name="description" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="productStock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="productStock" name="stock" value="<?php echo htmlspecialchars($product['product_stock']); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-success">Update Product</button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete Product</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this product? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn btn-danger">Delete Product</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>
