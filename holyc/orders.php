<?php
session_start();

// Check if user is logged in and is a seller
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'seller') {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Fetch pending orders
$pendingOrdersQuery = "
    SELECT DISTINCT orders.id, orders.order_details, orders.order_status 
    FROM orders 
    JOIN add_product 
    ON JSON_CONTAINS(orders.order_details, JSON_OBJECT('product_id', add_product.id)) 
    WHERE add_product.seller = ? AND orders.order_status = 'Order Placed'";
$stmt = $conn->prepare($pendingOrdersQuery);
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$pendingOrdersResult = $stmt->get_result();

// Fetch approved orders
$approvedOrdersQuery = "
    SELECT DISTINCT orders.id, orders.order_details, orders.order_status 
    FROM orders 
    JOIN add_product 
    ON JSON_CONTAINS(orders.order_details, JSON_OBJECT('product_id', add_product.id)) 
    WHERE add_product.seller = ? AND orders.order_status = 'Approved'";
$stmt = $conn->prepare($approvedOrdersQuery);
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$approvedOrdersResult = $stmt->get_result();

// Fetch received orders
$receivedOrdersQuery = "
    SELECT DISTINCT orders.id, orders.order_details, orders.order_status 
    FROM orders 
    JOIN add_product 
    ON JSON_CONTAINS(orders.order_details, JSON_OBJECT('product_id', add_product.id)) 
    WHERE add_product.seller = ? AND orders.order_status = 'Received'";
$stmt = $conn->prepare($receivedOrdersQuery);
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$receivedOrdersResult = $stmt->get_result();
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
        <?php
if (isset($_GET['message'])) {
    echo "<div class='alert alert-success'>{$_GET['message']}</div>";
}

if (isset($_GET['error'])) {
    echo "<div class='alert alert-danger'>{$_GET['error']}</div>";
}
?>
        <!-- Display Pending Orders -->
        <h4 class="mt-4">Pending Orders</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Details</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $pendingOrdersResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td>
                            <?php 
                                $order_details = json_decode($order['order_details'], true);
                                foreach ($order_details as $item) {
                                    echo htmlspecialchars($item['product_name']) . " (x" . $item['quantity'] . ")<br>";
                                }
                            ?>
                        </td>
                        <td>
                            <a href="approve_order.php?id=<?php echo $order['id']; ?>" class="btn btn-success btn-sm">Approve</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Display Approved Orders -->
        <h4 class="mt-4">Approved Orders</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Details</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $approvedOrdersResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td>
                            <?php 
                                $order_details = json_decode($order['order_details'], true);
                                foreach ($order_details as $item) {
                                    echo htmlspecialchars($item['product_name']) . " (x" . $item['quantity'] . ")<br>";
                                }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                        <td>
                            <a href="mark_shipped.php?id=<?php echo $order['id']; ?>" class="btn btn-primary btn-sm">Mark as Shipped</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <!-- Display Approved Orders -->
        <h4 class="mt-4">Approved Orders</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Details</th>
                    <th>Status</th>
                   
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $receivedOrdersResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td>
                            <?php 
                                $order_details = json_decode($order['order_details'], true);
                                foreach ($order_details as $item) {
                                    echo htmlspecialchars($item['product_name']) . " (x" . $item['quantity'] . ")<br>";
                                }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                        
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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
