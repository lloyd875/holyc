<!-- registration.php: Registration form -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #121212; /* Dark gray background */
            color: #e0e0e0; /* Light gray text for contrast */
            height: 100vh;
        }

        .card {
             /* Darker card background */
            color: #e0e0e0; /* Text color in the card */
            border: none;
            width: 500px;
        }
        .btn-darker{
            background-color: #444;
            color: #ffffff;
        }

        .card-header {
            background-color: #444;
            color: #ffffff;
        }

        .form-control {
             /* Input field background */
            color: #e0e0e0; /* Input text */
            border: 1px solid #555; /* Subtle border */
        }

        .btn-success {
            background-color: #28a745; /* Dark green button */
            border: none;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-primary {
            background-color: #007bff; /* Blue button */
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .alert-success {
            background-color: #28a745;
            color: #fff;
        }

        .alert-danger {
            background-color: #dc3545;
            color: #fff;
        }

        i {
            color: #e0e0e0;
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center">
    <div class=" mt-5">
        <div class="justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header text-center">
                        <h4><i class="fas fa-user-plus"></i> Registration Form</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        include 'db.php';
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {

                            $name = $conn->real_escape_string($_POST['name']);
                            $email = $conn->real_escape_string($_POST['email']);
                            $password = $conn->real_escape_string($_POST['password']);
                            $role = $conn->real_escape_string($_POST['role']);

                            // Save to database
                            $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";

                            if ($conn->query($sql) === TRUE) {
                                echo '<div class="alert alert-success">Registration successful!</div>';
                            } else {
                                echo '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
                            }
                        }
                        ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                                </div>
                            </div>
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
                            <div class="mb-3">
                               
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                    <select class="form-control" id="role" name="role" required>
                                        <option value="">Select Role</option>
                                        <option value="seller">Seller</option>
                                        <option value="buyer">Buyer</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-dark w-100 mb-2"><i class="fas fa-check"></i> Register</button>
                            <a href="index.php" class="btn btn-darker w-100"><i class="fas fa-user-plus"></i> Login</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
