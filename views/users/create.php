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

// Only include header for non-AJAX requests
require_once '../layouts/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Create New User</h1>
        <a href="/acrapi/views/users/index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Users
        </a>
    </div>

    <div id="message"></div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form id="createUserForm">
            <div class="mb-4">
                <label for="first_name" class="block text-gray-700 text-sm font-bold mb-2">First Name:</label>
                <input type="text" id="first_name" name="first_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <div class="mb-4">
                <label for="last_name" class="block text-gray-700 text-sm font-bold mb-2">Last Name:</label>
                <input type="text" id="last_name" name="last_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" id="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                <input type="password" id="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <div class="mb-6">
                <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-2">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Create User
                </button>
                <a href="/acrapi/views/users/index.php" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('createUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form values
    const first_name = document.getElementById('first_name').value;
    const last_name = document.getElementById('last_name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirm_password = document.getElementById('confirm_password').value;
    
    // Client-side validation
    if (!first_name || !last_name || !email || !password || !confirm_password) {
        showMessage('error', 'All fields are required');
        return;
    }
    
    if (password !== confirm_password) {
        showMessage('error', 'Passwords do not match');
        return;
    }
    
    if (!validateEmail(email)) {
        showMessage('error', 'Invalid email format');
        return;
    }
    
    const data = {
        first_name: first_name,
        last_name: last_name,
        email: email,
        password: password
    };
    
    // Show loading state
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML = 'Creating user...';
    submitBtn.disabled = true;
    
    // Send request to the server
    fetch('create.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(`Server returned ${response.status}: ${text.substring(0, 150)}...`);
            });
        }
        
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.indexOf('application/json') !== -1) {
            return response.json();
        } else {
            return response.text().then(text => {
                throw new Error('Expected JSON response but got HTML/text');
            });
        }
    })
    .then(result => {
        if (result.status === 'success') {
            showMessage('success', 'User created successfully!');
            document.getElementById('createUserForm').reset();
            
            // Redirect to users list after a short delay
            setTimeout(() => {
                window.location.href = '/acrapi/views/users/index.php';
            }, 2000);
        } else {
            showMessage('error', result.error || 'Failed to create user');
        }
    })
    .catch(error => {
        showMessage('error', 'An error occurred: ' + error.message);
        console.error('Error details:', error);
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
    });
});

function showMessage(type, message) {
    const messageDiv = document.getElementById('message');
    const className = type === 'success' 
        ? 'bg-green-100 border border-green-400 text-green-700' 
        : 'bg-red-100 border border-red-400 text-red-700';
    
    messageDiv.innerHTML = `
        <div class="${className} px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">${message}</span>
        </div>
    `;
    
    // Scroll to the message
    messageDiv.scrollIntoView({ behavior: 'smooth' });
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
}
</script>

<?php require_once '../layouts/footer.php'; ?>