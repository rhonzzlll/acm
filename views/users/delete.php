<?php
session_start();
require_once '../layouts/header.php';

// Get user ID from URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$user_id) {
    header('Location: /acrapi/views/users/index.php');
    exit;
}

// Connect to the API
require_once($_SERVER['DOCUMENT_ROOT'].'/acrapi/curl_helper.php');
$restAPIBaseURL = "http://localhost/acrapi";

try {
    // Send delete request to API
    $response = sendRequest($restAPIBaseURL.'/api.php/users/'.$user_id, 'DELETE');
    $responseData = json_decode($response, true);
    
    // Set flash message based on result
    $_SESSION['flash_message'] = isset($responseData['status']) && $responseData['status'] === 'success' 
        ? 'User deleted successfully!' 
        : 'Failed to delete user';
    
    // Set flash message type
    $_SESSION['flash_message_type'] = isset($responseData['status']) && $responseData['status'] === 'success'
        ? 'success'
        : 'error';
        
} catch (Exception $e) {
    // Set error flash message
    $_SESSION['flash_message'] = 'Error: ' . $e->getMessage();
    $_SESSION['flash_message_type'] = 'error';
}

// Redirect back to users index
header('Location: /acrapi/views/users/index.php');
exit;