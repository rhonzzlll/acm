<?php
 
require_once $_SERVER['DOCUMENT_ROOT'].'/acrapi/curl_helper.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?error=Invalid role ID');
    exit();
}

$roleId = $_GET['id'];

try {
    // Send DELETE request to roles API
    $delete_url = "http://localhost/acrapi/api/v1/roles/{$roleId}";
    $response = sendRequest($delete_url, 'DELETE');
    
    $parsed = json_decode($response, true);
    
    if (isset($parsed['status']) && $parsed['status'] === 'success') {
        // Redirect to roles index on successful deletion
        header('Location: index.php?success=Role deleted successfully');
    } else {
        // Redirect with error message
        header('Location: index.php?error=' . urlencode($parsed['message'] ?? 'Failed to delete role'));
    }
} catch (Exception $e) {
    // Redirect with error message
    header('Location: index.php?error=' . urlencode('An error occurred: ' . $e->getMessage()));
}
exit();
?>