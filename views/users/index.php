<?php 
$pageTitle = 'User Management';
require_once '../layouts/header.php'; // Corrected path without "views/"

// Fetch users from the API
require_once($_SERVER['DOCUMENT_ROOT'].'/acrapi/curl_helper.php');
$restAPIBaseURL = "http://localhost/acrapi";

try {
    // Call the users API endpoint
    $response = sendRequest($restAPIBaseURL.'/api.php/users', 'GET');
    $responseData = json_decode($response, true);
    
    // Check if users data exists in response
    if (isset($responseData['data']['users'])) {
        $users = $responseData['data']['users'];
    } else {
        $users = [];
        $error = "No users found or error fetching users.";
    }
} catch (Exception $e) {
    $users = [];
    $error = "Error connecting to API: " . $e->getMessage();
}
?>

<div class="users-management">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">User Management</h1>
        <a href="/acrapi/views/users/create.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add New User
        </a>
    </div>

    <?php if (isset($error)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
    </div>
    <?php endif; ?>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-100 text-gray-600 uppercase text-sm font-bold">
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left">ID</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left">Name</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left">Email</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left">Created At</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($user['id']); ?></p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">
                                    <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                </p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($user['email']); ?></p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($user['created_at'] ?? 'N/A'); ?></p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 text-sm text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="/acrapi/views/users/view.php?id=<?php echo $user['id']; ?>" class="text-blue-500 hover:text-blue-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                    <a href="/acrapi/views/users/edit.php?id=<?php echo $user['id']; ?>" class="text-green-500 hover:text-green-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                            <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                    <button onclick="confirmDelete(<?php echo $user['id']; ?>)" class="text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-5 py-5 border-b border-gray-200 text-sm text-center">
                            No users found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function confirmDelete(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        // Implement delete logic here - could make API call directly or redirect
        window.location.href = `/acrapi/views/users/delete.php?id=${userId}`;
    }
}
</script>

<?php require_once '../layouts/footer.php'; ?>