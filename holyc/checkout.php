<?php
session_start();
include 'db.php';

// Ensure only authorized buyers can perform checkout
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'buyer') {
    header("Location: login.php");
    exit;
}

$buyer_id = $_SESSION['id'];

// Fetch all cart items for the logged-in buyer
$query = $conn->prepare("
    SELECT c.id, a.product_price, c.quantity, a.seller, a.id AS product_id, a.product_stock
    FROM cart c 
    JOIN add_product a ON c.product_id = a.id 
    WHERE c.buyer_id = ?
");

$query->bind_param("i", $buyer_id);
$query->execute();
$cart_items = $query->get_result();

// If there are no items in the cart
if ($cart_items->num_rows == 0) {
    $_SESSION['error_message'] = "Your cart is empty.";
    header("Location: view_Cart.php");
    exit;
}

// Calculate total amount and identify unique sellers
$total_amount = 0;
$seller_ids = [];
$cart_items_array = [];
$cart_items_array1 = [];
while ($item = $cart_items->fetch_assoc()) {
    $total_amount += $item['product_price'] * $item['quantity'];
    $seller_ids[$item['seller']] = true; // Map unique sellers involved in the order
    $cart_items_array[] = $item;
    
    $product_name_query = $conn->prepare("SELECT product_name FROM add_product WHERE id = ?");
    $product_name_query->bind_param("i", $item['product_id']);
    $product_name_query->execute();
    $product_name_result = $product_name_query->get_result();
    $product_name_row = $product_name_result->fetch_assoc();

    // Add product_id, product_name, and quantity to the cart items array
    $cart_items_array1[] = [
        'product_id' => $item['product_id'],
        'product_name' => $product_name_row['product_name'],
        'quantity' => $item['quantity']
    ];// Save the cart items for quantity updates
}

// Convert seller IDs to a comma-separated string
$seller_ids_str = implode(',', array_keys($seller_ids));
// Serialize the cart_items_array to JSON
$order_details_json = json_encode($cart_items_array1);
// Insert into the orders table
$insert_order_query = $conn->prepare("
    INSERT INTO orders (buyer_id, seller_id, total_amount,order_details) 
    VALUES (?, ?, ?,?)
");

$insert_order_query->bind_param("iids", $buyer_id, $seller_ids_str, $total_amount, $order_details_json);

if ($insert_order_query->execute()) {
    // Get the order ID of the new order
    $order_id = $conn->insert_id;

    // Update the quantities of the purchased products
    $update_success = true;

    foreach ($cart_items_array as $item) {
        // Check if there’s enough stock to fulfill the order
        if ($item['product_stock'] < $item['quantity']) {
            $_SESSION['error_message'] = "Not enough stock available for product: {$item['product_id']}.";
            header("Location: view_Cart.php");
            exit;
        }

        // Prepare and execute the stock deduction query
        $update_query = $conn->prepare("
            UPDATE add_product 
            SET product_stock = product_stock - ? 
            WHERE id = ?
        ");

        $update_query->bind_param("ii", $item['quantity'], $item['product_id']);

        if (!$update_query->execute()) {
            $update_success = false;
            break; // Stop the process if any query fails
        }
    }

    if ($update_success) {
        // Clear the user's cart only after all updates are successful
        $clear_cart_query = $conn->prepare("DELETE FROM cart WHERE buyer_id = ?");
        $clear_cart_query->bind_param("i", $buyer_id);

        if ($clear_cart_query->execute()) {
            $_SESSION['success_message'] = "Checkout successful! Your order has been placed.";
            header("Location: buyer.php");
        } else {
            $_SESSION['error_message'] = "Checkout succeeded, but failed to clear your cart.";
            header("Location: view_cart.php");
        }
    } else {
        $_SESSION['error_message'] = "Error processing checkout. Product quantities could not be updated.";
        header("Location: view_cart.php");
    }
} else {
    $_SESSION['error_message'] = "Error processing the checkout.";
    header("Location: view_cart.php");
}
?>
