<?php
// Back-end proxy logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once($_SERVER['DOCUMENT_ROOT'].'/acrapi/curl_helper.php');
    $restAPIBaseURL = "http://localhost/acrapi";

    header('Content-Type: application/json');

    try {
        $input = file_get_contents('php://input');
        $response = sendRequest($restAPIBaseURL."/api.php/register", 'POST', $input);
        echo $response;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Control Management - Register</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .register-container {
            display: flex;
            min-height: 100vh;
        }
        .register-form-container {
            width: 375px;
            background-color: #fff;
            border-right: 1px solid #dee2e6;
            padding: 2rem;
        }
        .register-content {
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
        .register-btn {
            width: 100%;
            border-radius: 50px;
            padding: 0.5rem;
        }
        .login-btn {
            border-radius: 50px;
            padding: 0.5rem 2rem;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <!-- Register Form Section -->
        <div class="register-form-container">
            <h4 class="mb-4">Create your account</h4>

            <div id="message"></div>

            <form id="registerForm">
                <div class="mb-3">
                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" required>
                </div>
                <div class="mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary register-btn">Register</button>
            </form>
            <p class="mt-3 text-center">Already have an account? <a href="/acrapi/views/auth/login.php">Login here</a>.</p>
        </div>

        <!-- Main Content Section -->
        <div class="register-content">
            <div class="content-box">
                <h1 class="display-4 fw-bold">ACCESS CONTROL<br>MANAGEMENT</h1>
                <h3 class="text-muted">REST API</h3>
                <p class="lead my-4">
                    Access Control Management System is a web-based application that allows you to manage user access and permissions efficiently. It provides a secure and user-friendly interface for administrators to control user roles, permissions, and access levels.
                </p>
                <a href="/acrapi/views/auth/login.php" class="btn btn-outline-primary login-btn">Login</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const data = {
                first_name: document.getElementById('first_name').value,
                last_name: document.getElementById('last_name').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            };
            
            fetch('register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    return response.json().then(data => ({ isJson: true, data }));
                } else {
                    return response.text().then(text => ({ isJson: false, data: text }));
                }
            })
            .then(result => {
                if (result.isJson) {
                    if (result.data.error) {
                        document.getElementById('message').innerHTML =
                            '<div class="alert alert-danger" role="alert">' + result.data.error + '</div>';
                    } else {
                        document.getElementById('message').innerHTML =
                            '<div class="alert alert-success" role="alert">Registration successful! You can now <a href="/acrapi/views/auth/login.php">log in</a>.</div>';
                        document.getElementById('registerForm').reset();
                    }
                } else {
                    document.getElementById('message').innerHTML =
                        '<div class="alert alert-danger" role="alert">' + result.data + '</div>';
                }
            })
            .catch(error => {
                document.getElementById('message').innerHTML =
                    '<div class="alert alert-danger" role="alert">An error occurred: ' + error.message + '</div>';
            });
        });
    </script>
</body>
</html>
