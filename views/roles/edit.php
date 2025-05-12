<?php
require_once '../layouts/header.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/acrapi/curl_helper.php';

// Initialize variables
$error = '';
$roleName = '';
$roleDescription = '';
$roleId = '';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?error=Invalid role ID');
    exit();
}

$roleId = $_GET['id'];

// Fetch existing role data
try {
    $api_url = "http://localhost/acrapi/api/v1/roles/{$roleId}";
    $response = sendRequest($api_url, 'GET');
    $parsed = json_decode($response, true);

    if (isset($parsed['status']) && $parsed['status'] === 'success') {
        $role = $parsed['data']['role'] ?? [];
        $roleName = $role['name'] ?? '';
        $roleDescription = $role['description'] ?? '';
    } else {
        $error = $parsed['message'] ?? 'Failed to fetch role details.';
    }
} catch (Exception $e) {
    $error = 'An error occurred: ' . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $roleName = trim($_POST['role_name'] ?? '');
    $roleDescription = trim($_POST['role_description'] ?? '');

    // Basic validation
    if (empty($roleName)) {
        $error = 'Role name is required.';
    } else {
        try {
            // Prepare data for API
            $putData = json_encode([
                'name' => $roleName,
                'description' => $roleDescription,
                'created_by' => 1 // This should match the created_by from the role being edited
            ]);

            // Send PUT request to roles API
            $update_url = "http://localhost/acrapi/api/v1/roles/{$roleId}";
            $response = sendRequest($update_url, 'PUT', $putData);
            
            // Debug response if needed
            // echo '<pre>';
            // print_r($response);
            // echo '</pre>';
            // exit();
            
            $parsed = json_decode($response, true);

            if (isset($parsed['status']) && $parsed['status'] === 'success') {
                // Redirect to roles index on successful update
                header('Location: index.php?success=Role updated successfully');
                exit();
            } else {
                // Handle API error
                $error = $parsed['message'] ?? 'Failed to update role. Please try again.';
            }
        } catch (Exception $e) {
            $error = 'An error occurred: ' . $e->getMessage();
        }
    }
}
?>

<div class="edit-role-container max-w-md mx-auto mt-10">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h1 class="text-2xl font-bold mb-6 text-center">Edit Role</h1>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="role_name">
                    Role Name
                </label>
                <input 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                    id="role_name" 
                    name="role_name" 
                    type="text" 
                    placeholder="Enter role name"
                    value="<?= htmlspecialchars($roleName) ?>"
                    required
                >
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="role_description">
                    Role Description
                </label>
                <textarea 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" 
                    id="role_description" 
                    name="role_description" 
                    placeholder="Optional role description"
                    rows="4"
                ><?= htmlspecialchars($roleDescription) ?></textarea>
            </div>
            <div class="flex items-center justify-between">
                <button 
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                    type="submit"
                >
                    Update Role
                </button>
                <a 
                    href="index.php" 
                    class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800"
                >
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../layouts/footer.php'; ?>