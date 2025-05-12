 
<?php
require_once '../layouts/header.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/acrapi/curl_helper.php';

// Initialize variables
$error = '';
$role = null;

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
        $role = $parsed['data']['role'];
    } else {
        $error = $parsed['message'] ?? 'Failed to fetch role details.';
    }
} catch (Exception $e) {
    $error = 'An error occurred: ' . $e->getMessage();
}
?>

<div class="view-role-container max-w-md mx-auto mt-10">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h1 class="text-2xl font-bold mb-6 text-center">Role Details</h1>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php elseif ($role): ?>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">ID</label>
                <p class="py-2 px-3 bg-gray-100 rounded"><?= htmlspecialchars($role['id'] ?? '') ?></p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Role Name</label>
                <p class="py-2 px-3 bg-gray-100 rounded"><?= htmlspecialchars($role['name'] ?? '') ?></p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <p class="py-2 px-3 bg-gray-100 rounded"><?= htmlspecialchars($role['description'] ?? '') ?></p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Created By</label>
                <p class="py-2 px-3 bg-gray-100 rounded"><?= htmlspecialchars($role['created_by'] ?? '') ?></p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Permissions</label>
                <p class="py-2 px-3 bg-gray-100 rounded"><?= htmlspecialchars($role['permissions'] ?? 'None') ?></p>
            </div>
            
            <div class="flex items-center justify-between">
                <a 
                    href="edit.php?id=<?= htmlspecialchars($role['id'] ?? '') ?>" 
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                >
                    Edit
                </a>
                <a 
                    href="index.php" 
                    class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800"
                >
                    Back to List
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../layouts/footer.php'; ?>