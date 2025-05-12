<?php
// filepath: c:\xampp\htdocs\acrapi\views\roles\create.php
require_once '../layouts/header.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/acrapi/curl_helper.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role_data = [
        'name' => $_POST['name'] ?? '',
        'description' => $_POST['description'] ?? ''
    ];
    
    try {
        $api_url = 'http://localhost/acrapi/api/v1/roles/';
        $response = sendRequest($api_url, 'POST', json_encode($role_data));
        $parsed = json_decode($response, true);
        
        if (isset($parsed['status']) && $parsed['status'] === 'success') {
            $message = 'Role created successfully!';
            $messageType = 'success';
            // Redirect after short delay
            header("refresh:2;url=/roles/index.php");
        } else {
            $message = $parsed['message'] ?? 'An error occurred while creating the role.';
            $messageType = 'error';
        }
    } catch (Exception $e) {
        $message = 'Error connecting to API: ' . $e->getMessage();
        $messageType = 'error';
    }
}
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Create New Role</h1>
        <a href="/roles/index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Roles
        </a>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="mb-4 p-4 rounded-lg <?= $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <form method="POST" action="">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Role Name *</label>
                <input type="text" id="name" name="name" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Enter role name">
            </div>
            
            <div class="mb-6">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <textarea id="description" name="description" rows="4"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Enter role description"></textarea>
            </div>
            
            <div class="flex items-center justify-end">
                <button type="submit" 
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Create Role
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../layouts/footer.php'; ?>