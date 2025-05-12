<?php
session_start();

 
if (isset($_SESSION['user_id'])) {
    header('Location: /acrapi/views/auth/login.php');
    exit;
}

// Handle login form submission
$error = '';
$debug = ''; // Add debug variable
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form inputs
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        // Connect to the API for authentication
        require_once($_SERVER['DOCUMENT_ROOT'].'/acrapi/curl_helper.php');
        $restAPIBaseURL = "http://localhost/acrapi";

        try {
            // Prepare the data for API request
            $data = [
                'email' => $email,
                'password' => $password
            ];

            // Add debug info
            $debug = "Sending request to: " . $restAPIBaseURL . "/api.php/login-email";
            
            // Send the request to login API endpoint
            $response = sendRequest($restAPIBaseURL."/api.php/login-email", 'POST', json_encode($data));
            
            // Add raw response to debug
            $debug .= "<br>Raw response: " . htmlspecialchars($response);
            
            $responseData = json_decode($response, true);
            
            // Add decoded response to debug
            $debug .= "<br>Decoded response: " . print_r($responseData, true);

            // Check if login was successful
            if (isset($responseData['user']) && !isset($responseData['error'])) {
                // User found, set session variables
                $_SESSION['user_id'] = $responseData['user']['id'];
                $_SESSION['first_name'] = $responseData['user']['first_name'];
                $_SESSION['last_name'] = $responseData['user']['last_name'];
                $_SESSION['email'] = $responseData['user']['email'];

                // Set admin status if it exists in the response
                if (isset($responseData['user']['is_admin'])) {
                    $_SESSION['is_admin'] = $responseData['user']['is_admin'];
                }

                // Force session to be saved
                session_write_close();

                // Redirect to dashboard
                header('Location: /acrapi/views/dashboard/index.php');
                exit;
            } else {
                // Login failed, show error message
                $error = $responseData['error'] ?? 'Invalid email or password';
            }
        } catch (Exception $e) {
            // Log the error and show a generic message
            error_log("Login error: " . $e->getMessage());
            $error = 'Login failed. Please try again later.';
            $debug .= "<br>Exception: " . $e->getMessage();
        }
    }
}

// Page title for header
$pageTitle = 'Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Control Management - Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            display: flex;
            min-height: 100vh;
        }
        .login-form-container {
            width: 375px;
            background-color: #fff;
            border-right: 1px solid #dee2e6;
            padding: 2rem;
        }
        .login-content {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            background: linear-gradient(135deg, #f8f9fa 25%, #e9ecef 100%);
        }
        .content-box {
            max-width: 600px;
            text-align: center;
        }
        .form-control {
            border-radius: 0.25rem;
            margin-bottom: 1rem;
        }
        .login-btn {
            width: 100%;
            border-radius: 50px;
            padding: 0.5rem;
        }
        .register-btn {
            border-radius: 50px;
            padding: 0.5rem 2rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Login Form Section -->
        <div class="login-form-container">
            <h4 class="mb-4">Login your account</h4>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="Email" required 
                           value="<?php echo htmlspecialchars($email ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary login-btn">Login</button>
            </form>
            <p class="mt-3 text-center">Don't have an account? <a href="/acrapi/views/auth/register.php">Register here</a>.</p>
        </div>

        <!-- Main Content Section -->
        <div class="login-content">
            <div class="content-box">
                <h1 class="display-4 fw-bold">ACCESS CONTROL<br>MANAGEMENT</h1>
                <h3 class="text-muted">REST API</h3>
                <p class="lead my-4">
                    Access Control Management System is a web-based application that allows you to manage user access and permissions efficiently. It provides a secure and user-friendly interface for administrators to control user roles, permissions, and access levels.
                </p>
                <a href="/acrapi/views/auth/register.php" class="btn btn-outline-primary register-btn">Register</a>
            </div>
        </div>
    </div>

    <!-- Debug Information -->
    <?php if (!empty($debug) && isset($_GET['debug'])): ?>
        <div class="alert alert-info">
            <strong>Debug Information:</strong>
            <pre><?php echo $debug; ?></pre>
        </div>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>