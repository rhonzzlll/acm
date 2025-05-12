<?php
$pageTitle = 'View User';
require_once '../layouts/header.php';
require_once($_SERVER['DOCUMENT_ROOT'].'/acrapi/curl_helper.php');

$restAPIBaseURL = "http://localhost/acrapi";

$userId = $_GET['id'] ?? null;
$user = null;
$error = null;
$rawResponse = null; // For debugging

if ($userId) {
    try {
        $response = sendRequest("$restAPIBaseURL/api.php/users/$userId", 'GET');
        $rawResponse = $response; // Store for debugging
        $responseData = json_decode($response, true);

        // Debug output - uncomment to see the response structure
        // echo '<pre>' . print_r($responseData, true) . '</pre>'; exit;

        // Check various possible structures of the response
        if (isset($responseData['data']['user'])) {
            // This matches the structure in your get_user method
            $user = $responseData['data']['user'];
        } elseif (isset($responseData['data']) && is_array($responseData['data'])) {
            // Maybe the user data is directly in 'data'
            $user = $responseData['data'];
        } elseif (isset($responseData['user'])) {
            // Or directly under 'user'
            $user = $responseData['user'];
        } else {
            $error = "User data structure not recognized.";
        }
    } catch (Exception $e) {
        $error = "Error connecting to API: " . $e->getMessage();
    }
} else {
    $error = "No user ID provided.";
}
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">User Details</h1>
        <a href="/acrapi/views/users/index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Users
        </a>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
            
            <!-- Debug information -->
            <?php if ($rawResponse): ?>
            <div class="mt-4 p-3 bg-gray-100 rounded overflow-auto max-h-64">
                <strong>Raw API Response:</strong>
                <pre class="text-xs"><?php echo htmlspecialchars($rawResponse); ?></pre>
                
                <strong>Parsed Response:</strong>
                <pre class="text-xs"><?php echo print_r(json_decode($rawResponse, true), true); ?></pre>
            </div>
            <?php endif; ?>
        </div>
    <?php elseif ($user): ?>
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6">
                <?php if (isset($user['first_name']) && isset($user['last_name'])): ?>
                <div class="mb-4 flex items-center justify-center">
                    <div class="h-24 w-24 rounded-full bg-gray-200 flex items-center justify-center text-2xl text-gray-600 font-bold">
                        <?php echo strtoupper(substr($user['first_name'] ?? '', 0, 1) . substr($user['last_name'] ?? '', 0, 1)); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border-b py-2">
                        <h3 class="text-gray-500 text-sm">User ID</h3>
                        <p class="text-lg"><?php echo htmlspecialchars($user['id'] ?? 'N/A'); ?></p>
                    </div>
                    
                    <div class="border-b py-2">
                        <h3 class="text-gray-500 text-sm">First Name</h3>
                        <p class="text-lg"><?php echo htmlspecialchars($user['first_name'] ?? 'N/A'); ?></p>
                    </div>
                    
                    <div class="border-b py-2">
                        <h3 class="text-gray-500 text-sm">Last Name</h3>
                        <p class="text-lg"><?php echo htmlspecialchars($user['last_name'] ?? 'N/A'); ?></p>
                    </div>
                    
                    <div class="border-b py-2">
                        <h3 class="text-gray-500 text-sm">Email</h3>
                        <p class="text-lg"><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></p>
                    </div>
                    
                    <?php if (isset($user['created_at'])): ?>
                    <div class="border-b py-2">
                        <h3 class="text-gray-500 text-sm">Created At</h3>
                        <p class="text-lg"><?php echo htmlspecialchars($user['created_at']); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($user['updated_at'])): ?>
                    <div class="border-b py-2">
                        <h3 class="text-gray-500 text-sm">Last Updated</h3>
                        <p class="text-lg"><?php echo htmlspecialchars($user['updated_at']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="mt-6 flex space-x-4">
                    <a href="/acrapi/views/users/edit.php?id=<?php echo $user['id']; ?>" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                            <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                        </svg>
                        Edit User
                    </a>
                    <button onclick="confirmDelete(<?php echo $user['id']; ?>)" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Delete User
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function confirmDelete(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        window.location.href = `/acrapi/views/users/delete.php?id=${userId}`;
    }
}
</script>

<?php require_once '../layouts/footer.php'; ?>
