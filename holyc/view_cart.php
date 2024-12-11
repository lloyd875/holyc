<?php
session_start();
include 'db.php';

// Ensure only authorized buyers can view their cart
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'buyer') {
    header("Location: login.php");
    exit;
}

// Fetch all cart items for the logged-in buyer
$buyer_id = $_SESSION['id'];
$query = $conn->prepare("SELECT c.id, a.product_name, a.product_price, c.quantity FROM cart c JOIN add_product a ON c.product_id = a.id WHERE c.buyer_id = ?");
$query->bind_param("i", $buyer_id);
$query->execute();
$cart_items = $query->get_result();

// Fetch order status monitoring information
$orderStatusQuery = "
    SELECT o.id, o.order_status, o.order_details
    FROM orders o
    WHERE o.buyer_id = ? AND o.order_status = 'Shipped'";
$orderStatusStmt = $conn->prepare($orderStatusQuery);
$orderStatusStmt->bind_param("i", $buyer_id);
$orderStatusStmt->execute();
$shippedOrdersResult = $orderStatusStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center mb-4">Your Shopping Cart</h1>
    
    <?php
    if (isset($_SESSION['success_message'])) {
        echo "<div class='alert alert-success text-center'>{$_SESSION['success_message']}</div>";
        unset($_SESSION['success_message']);
    }
    
    if (isset($_SESSION['error_message'])) {
        echo "<div class='alert alert-danger text-center'>{$_SESSION['error_message']}</div>";
        unset($_SESSION['error_message']);
    }
    ?>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($cart_items->num_rows > 0): ?>
                <?php while ($item = $cart_items->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td>$<?php echo number_format($item['product_price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['product_price'] * $item['quantity'], 2); ?></td>
                        <td>
                            <form action="remove_from_cart.php" method="POST">
                                <input type="hidden" name="cart_item_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Your cart is empty.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Checkout Button -->
    <div class="text-end mt-3">
        <form action="checkout.php" method="POST">
            <button type="submit" class="btn btn-success">Proceed to Checkout</button>
        </form>
    </div>
    <!-- Display Shipped Orders -->
    <h3 class="mt-5">Shipped Orders Monitoring</h3>
    <?php if ($shippedOrdersResult->num_rows > 0): ?>
        <table class="table table-success table-hover">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Details</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $shippedOrdersResult->fetch_assoc()): ?>
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
                            <form action="receive_order.php" method="POST">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <button type="submit" class="btn btn-primary btn-sm">Receive Order</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No shipped orders to monitor at the moment.</p>
    <?php endif; ?>

    <!-- Back Button -->
    <div class="text-start mt-3">
        <a href="buyer.php" class="btn btn-secondary">Go Back</a>
    </div>
</div>

<footer class="text-center mt-5 mb-3">
    &copy; <?php echo date('Y'); ?> Buyer Dashboard
</footer>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>
</html>
