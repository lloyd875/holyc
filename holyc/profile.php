<?php
session_start();
include 'db.php';

// Check if user is logged in and has buyer role
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'buyer') {
    header("Location: login.php");
    exit();
}

// Fetch user information from the database
$userId = $_SESSION['id'];
$sql = "SELECT * FROM users WHERE id = '$userId'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $region = $conn->real_escape_string($_POST['region']);
    $province = $conn->real_escape_string($_POST['province']);
    $municipality = $conn->real_escape_string($_POST['municipality']);
    $barangay = $conn->real_escape_string($_POST['barangay']);
    $zipCode = $conn->real_escape_string($_POST['zipCode']);

    $updateSql = "UPDATE users SET region = '$region', province = '$province', municipality = '$municipality', barangay = '$barangay', zipCode = '$zipCode' WHERE id = '$userId'";
    
    if ($conn->query($updateSql) === TRUE) {
        echo "<div class='alert alert-success'>Information saved successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error saving information: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Sidebar custom styling */
        #sidebar {
            background-color: #343a40;
            color: #fff;
            padding: 15px;
            height: 100vh;
            overflow-y: auto;
        }

        #sidebar a {
            color: #fff;
            text-decoration: none;
            transition: color 0.3s ease-in-out;
        }

        #sidebar a:hover {
            color: #00bcd4;
        }

        /* Styling for alerts if needed */
        .alert-success,
        .alert-danger {
            margin-bottom: 20px;
        }

        /* Optional: Adjustments for better spacing */
        #content {
            padding: 20px;
        }

        /* Optional: Ensure responsiveness on smaller devices */
        @media (max-width: 992px) {
            #sidebar {
                height: auto;
                position: static;
                transform: none;
            }
        }
    </style>
</head>

<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <!-- Sidebar Section -->
        <div id="sidebar" class="col-md-3 col-sm-4 col-12">
            <h4 class="text-white mb-4 text-center">Buyer Menu</h4>
            <nav class="nav flex-column">
                <a class="nav-link mb-2" href="buyer.php"><i class="fa fa-home"></i> Home</a>
                <a class="nav-link mb-2" href="view_cart.php"><i class="fa fa-shopping-cart"></i> View Cart</a>
                <a class="nav-link mb-2" href="profile.php"><i class="fa fa-user"></i> Profile</a>
                
                <a class="nav-link mb-2" href="index.php"><i class="fa fa-sign-out-alt"></i> Log Out</a>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div id="content" class="col-md-9 col-sm-8 col-12">
            <div class="container">
                <!-- Page Title -->
                <h2 class="text-center mb-4">Buyer Profile</h2>
                
                <!-- User Information Section -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white text-center">
                        <h5>Registered Buyer Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                </div>

                <!-- Address Update Section -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white text-center">
                        <h5>Update Your Address Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="region" class="form-label">Region</label>
                                <input type="text" class="form-control" id="region" name="region" placeholder="Enter Region" value="<?php echo htmlspecialchars($user['region']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="province" class="form-label">Province</label>
                                <input type="text" class="form-control" id="province" name="province" placeholder="Enter Province" value="<?php echo htmlspecialchars($user['province']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="municipality" class="form-label">Municipality</label>
                                <input type="text" class="form-control" id="municipality" name="municipality" placeholder="Enter Municipality" value="<?php echo htmlspecialchars($user['municipality']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="barangay" class="form-label">Barangay</label>
                                <input type="text" class="form-control" id="barangay" name="barangay" placeholder="Enter Barangay" value="<?php echo htmlspecialchars($user['barangay']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="zipCode" class="form-label">Zip Code</label>
                                <input type="text" class="form-control" id="zipCode" name="zipCode" placeholder="Enter Zip Code" value="<?php echo htmlspecialchars($user['zipCode']); ?>">
                            </div>
                            <button type="submit" class="btn btn-success w-100">Save Address Information</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
