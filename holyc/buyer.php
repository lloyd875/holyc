<?php
session_start();
include 'db.php';

// Ensure only authorized buyers can access this page
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'buyer') {
    header("Location: login.php");
    exit;
}

// Fetch all products from the database
$query = $conn->prepare("SELECT * FROM add_product");
$query->execute();
$products = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* Sidebar full height */
        #sidebar {
            background-color: #343a40;
            color: #fff;
            height: 100vh; /* Full viewport height */
            padding: 15px;
            overflow-y: auto;
            width: 300px;
        }

        /* Sidebar menu link styles */
        #sidebar a {
            color: #fff;
            text-decoration: none;
            transition: color 0.3s ease-in-out;
        }

        #sidebar a:hover {
            color: #00bcd4;
        }

        /* Content scrollable area */
        #content {
            overflow-y: auto;
            height: 100vh; /* Set the scrollable area to occupy the full viewport height */
            padding: 15px;
        }

        /* Style the product cards */
        .product-card {
            height: 100%;
        }

        /* Sidebar responsiveness */
        @media (max-width: 992px) {
            #sidebar {
                position: absolute;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                z-index: 1000;
            }

            #sidebar.show {
                transform: translateX(0);
            }
        }

        /* Optional: Ensure smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body>
    <!-- Sidebar Section -->
    <div class="d-flex">
        <!-- Full-height Sidebar -->
        <div id="sidebar">
            <h4 class="text-white mb-4 text-center">Buyer Menu</h4>
            <nav class="nav flex-column">
                <a class="nav-link mb-2" href="#"><i class="fa fa-home"></i> Home</a>
                <a class="nav-link mb-2" href="view_cart.php"><i class="fa fa-shopping-cart"></i> View Cart</a>
                <a class="nav-link mb-2" href="profile.php"><i class="fa fa-user"></i> Profile</a>
                <a class="nav-link mb-2" id="showFavoritesBtn">
        <i class="fa fa-heart"></i> Show Favorites
    </a>
                <a class="nav-link mb-2" href="index.php"><i class="fa fa-sign-out-alt"></i> Log Out</a>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div id="content">
            <div class="mt-3">
                <h1 class="text-center mb-4">Welcome to the Buyer Dashboard</h1>
                <?php if (isset($_GET['message'])): ?>
    <?php if ($_GET['message'] === 'added_to_favorites'): ?>
        <div class="alert alert-success">Product added to favorites successfully!</div>
    <?php elseif ($_GET['message'] === 'already_added'): ?>
        <div class="alert alert-warning">Product is already added to favorites.</div>
    <?php endif; ?>
<?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger">Failed to add product to favorites.</div>
<?php endif; ?>
                <!-- Product Display Section -->
                <div class="row row-cols-1 row-cols-md-3 g-4">
    <?php
    while ($product = $products->fetch_assoc()):

        // Check if the product is already in favorites
        $userId = $_SESSION['id'];
        $productId = $product['id'];
        $checkSql = "SELECT * FROM favorites WHERE user_id = '$userId' AND product_id = '$productId'";
        $checkResult = $conn->query($checkSql);
        $isFavorite = ($checkResult && $checkResult->num_rows > 0);
    ?>
        <div class="col">
            <div class="card h-100 shadow-sm product-card">
                <img src="<?php echo htmlspecialchars($product['img_path']); ?>" class="card-img-top" alt="Product Image">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                    <p class="card-text text-success">Price: $<?php echo htmlspecialchars($product['product_price']); ?></p>
                </div>
                <div class="card-footer text-center">
                    <!-- Add to Cart Form -->
                    <form action="add_to_cart.php" method="POST" class="mb-2">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <div class="mb-2">
                            <label for="quantity_<?php echo $product['id']; ?>">Quantity:</label>
                            <input type="number" name="quantity" id="quantity_<?php echo $product['id']; ?>" class="form-control" value="1" min="1" required>
                        </div>
                        <button type="submit" class="btn btn-dark btn-block w-100">
                            <i class="fa fa-cart-plus"></i> Add to Cart
                        </button>
                    </form>

                    <!-- Add to Favorites / Remove Logic -->
                    <?php if ($isFavorite): ?>
                        <!-- If already added to favorites, clicking will remove -->
                        <form action="remove_from_favorites.php" method="POST" class="mt-2">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="btn btn-success btn-block w-100">
                                <i class="fa fa-check"></i> Remove from Favorites
                            </button>
                        </form>
                    <?php else: ?>
                        <!-- Else show clickable heart button to add -->
                        <form action="add_to_favorites.php" method="POST" class="mt-2">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-block w-100">
                                <i class="fa fa-heart"></i> Add to Favorites
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-3 mb-3">
        &copy; <?php echo date('Y'); ?> Buyer Dashboard
    </footer>
<!-- Modal for displaying favorites -->
<div class="modal fade" id="favoritesModal" tabindex="-1" aria-labelledby="favoritesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="favoritesModalLabel">Your Favorites</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="favoritesTable">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Description</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Favorites will dynamically populate here via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <!-- Include jQuery & Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    $('#showFavoritesBtn').click(function() {
        // Open modal
        $('#favoritesModal').modal('show');

        // Fetch favorites using AJAX
        $.ajax({
            url: 'fetch_favorites.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                // Clear the table before adding data
                $('#favoritesTable tbody').empty();

                if (response.success) {
                    if (response.data.length > 0) {
                        response.data.forEach(function(fav) {
                            $('#favoritesTable tbody').append(`
                                <tr>
                                    <td>${fav.product_name}</td>
                                    <td>${fav.description}</td>
                                    <td>$${fav.product_price}</td>
                                </tr>
                            `);
                        });
                    } else {
                        $('#favoritesTable tbody').append(`
                            <tr>
                                <td colspan="3" class="text-center">No favorites found.</td>
                            </tr>
                        `);
                    }
                } else {
                    alert('Could not fetch favorites.');
                }
            },
            error: function() {
                alert('An error occurred while fetching favorites.');
            }
        });
    });
});
</script>
</body>

</html>
