<?php
session_start();

// Check if user is logged in and is a seller
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'seller') {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Query to fetch products
$productQuery = "SELECT * FROM add_product";
$result = $conn->query($productQuery);


// Query to calculate total amount for this seller where the order status is 'Received'
$sellerId = $_SESSION['id'];
$totalQuery = "
    SELECT SUM(total_amount) AS total
    FROM orders
    WHERE seller_id = ? AND order_status = 'Received'
";
$stmt = $conn->prepare($totalQuery);
$stmt->bind_param("i", $sellerId);
$stmt->execute();
$result1 = $stmt->get_result();
$totalRow = $result1->fetch_assoc();
$totalAmount = $totalRow['total'] ? number_format($totalRow['total'], 2) : "0.00";

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOLYC - Seller Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .bg-darker{
            background-color: #444;
            color: #ffffff;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg bg-darker text-light">
        <div class="container-fluid">
            <a class="navbar-brand text-light" href="seller.php">HOLYC</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse text-light" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link text-light" href="seller.php" data-bs-toggle="modal" data-bs-target="#addProductModal"><i class="fas fa-plus"></i> Add Product</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link text-light" href="orders.php"><i class="fas fa-tasks"></i> Order Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="index.php"><i class="fas fa-sign-out-alt"></i> Log Out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Welcome, Seller</h2>
        <p>Manage your products, view sales records, and handle orders easily through the dashboard.</p>
        <!-- Full Card Design to Display Total Amount Received -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card bg-light shadow-sm">
                    <div class="card-header text-center bg-darker text-white">
                        <h4 class="mb-0">Total Amount Received</h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <i class="fas fa-peso-sign fa-5x text-success me-3"></i>
                            <h2 class="text-success" style="font-size: 2.5rem;"><?php echo $totalAmount; ?></h2>
                        </div>
                        <p class="text-muted">This represents the total amount from orders with the status <strong>'Received'</strong>.</p>
                        <a href="orders.php" class="btn btn-dark mt-3">View Orders</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Display success/error messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; ?></div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

       
        <!-- Display Products -->
        <h4 class="mt-4">Available Products</h4>
        <div class="row">
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($product['img_path']); ?>" class="card-img-top img-fluid" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                            <h6 class="card-subtitle mb-2 text-muted">Price: $<?php echo number_format($product['product_price'], 2); ?></h6>
                            <p class="card-text">Stock: <?php echo $product['product_stock']; ?></p>
                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-dark btn-sm w-100 mb-1">Edit</a>
                            <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm w-100" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="add_product.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="productName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="productName" name="product_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="productPrice" class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" id="productPrice" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="productDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="productDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="productStock" class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" id="productStock" name="stock" required>
                        </div>
                        <div class="mb-3">
                            <label for="productImage" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="productImage" name="product_image" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-dark w-100">Add Product</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$conn->close(); // Close database connection
?>