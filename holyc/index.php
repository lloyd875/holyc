<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOLYC - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General body style for dark theme */
        body {
            background-color: #121212;
            color: #ffffff;
            height: 100vh;
        }

        .btn-darker{
            background-color: #444;
            color: #ffffff;
        }

        /* Card appearance */
        .card {
            
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.7);
            color: #ffffff;
            width: 500px;
        }

        /* Card Header */
        .card-header {
            background-color: #444;
            color: #ffffff;
        }

        /* Input fields & buttons */
        .form-control {
            
            border: 1px solid #555;
            color: #ffffff;
        }

        /* Buttons */
        .btn-success {
            background-color: #28a745;
            border: none;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn:hover {
            opacity: 0.8;
        }

        /* Alerts */
        .alert-danger {
            background-color: #d9534f;
            color: #ffffff;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
<div class=" mt-5">
    <div class="justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header text-center">
                    <h4><i class="fas fa-beer"></i> HOLYC - Login</h4>
                </div>
                <div class="card-body">
                    <?php
                    session_start();
                    include 'db.php';
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $email = $conn->real_escape_string($_POST['email']);
                        $password = $conn->real_escape_string($_POST['password']);

                        // Fetch user details
                        $sql = "SELECT id, role FROM users WHERE email='$email' AND password='$password'";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            $user = $result->fetch_assoc();
                            $_SESSION['id'] = $user['id'];
                            $_SESSION['role'] = $user['role'];

                            // Redirect based on role
                            if ($user['role'] === 'admin') {
                                header("Location: admin_page.php");
                            } elseif ($user['role'] === 'seller') {
                                header("Location: seller.php");
                            } elseif ($user['role'] === 'buyer') {
                                header("Location: buyer.php");
                            }
                            exit;
                        } else {
                            echo '<div class="alert alert-danger">Invalid email or password.</div>';
                        }
                    }
                    ?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                           
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-dark w-100 mb-2"><i class="fas fa-sign-in-alt"></i> Login</button>
                        <a href="reg.php" class="btn btn-darker w-100"><i class="fas fa-user-plus"></i> Register</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
