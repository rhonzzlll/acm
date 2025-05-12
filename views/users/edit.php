<?php

$pageTitle = 'Edit User';
require_once '../layouts/header.php';

// Get user ID from URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$user_id) {
    header('Location: /acrapi/views/users/index.php');
    exit;
}

$user = null;
$success = null;
$error = null;

// Connect to the API
require_once($_SERVER['DOCUMENT_ROOT'].'/acrapi/curl_helper.php');
$restAPIBaseURL = "http://localhost/acrapi";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    // Validate form data
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error = 'All fields are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } else {
        try {
            // Prepare the data for API request
            $data = [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email
            ];
            
            // Send the request to update user API endpoint
            $response = sendRequest($restAPIBaseURL.'/api.php/users/'.$user_id, 'PUT', json_encode($data));
            $responseData = json_decode($response, true);
            
            // Check if update was successful
            if (isset($responseData['status']) && $responseData['status'] === 'success') {
                $success = 'User updated successfully!';
                // Update the user data with new values
                $user = $data;
                $user['id'] = $user_id;
            } else {
                $error = $responseData['error'] ?? 'Failed to update user';
            }
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
} else {
    // Fetch user data
    try {
        $response = sendRequest($restAPIBaseURL.'/api.php/users/'.$user_id, 'GET');
        $responseData = json_decode($response, true);
        
        if (isset($responseData['data']['user'])) {
            $user = $responseData['data']['user'];
        } else {
            $error = 'User not found';
        }
    } catch (Exception $e) {
        $error = 'Error fetching user data: ' . $e->getMessage();
    }
}
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Edit User</h1>
        <a href="/acrapi/views/users/index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Users
        </a>
    </div>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($success); ?></span>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <?php if ($user): ?>
        <div class="bg-white shadow-md rounded-lg p-6">
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="first_name" class="block text-gray-700 text-sm font-bold mb-2">First Name:</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="last_name" class="block text-gray-700 text-sm font-bold mb-2">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-6">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Update User
                    </button>
                    <a href="/acrapi/views/users/index.php" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="bg-white shadow-md rounded-lg p-6">
            <p class="text-center text-gray-700">User not found or error loading user data.</p>
            <div class="mt-4 text-center">
                <a href="/acrapi/views/users/index.php" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Return to User List
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../layouts/footer.php'; ?>