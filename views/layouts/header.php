<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: /acrapi/views/auth/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Access Control Management'; ?></title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/acrapi/assets/css/custom.css" rel="stylesheet">
</head>
<body class="bg-gray-100 antialiased">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-screen flex-col">
            <!-- Navigation -->
            <nav class="bg-white shadow-md">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <div class="flex items-center">
                            <a href="/acrapi/views/dashboard/index.php" class="flex-shrink-0">
     
                            </a>
                            <div class="ml-10 flex items-baseline space-x-4">
                                <a href="/acrapi/views/dashboard/index.php" class="text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                        
                            </div>
                        </div>
                        <div class="ml-4 flex items-center md:ml-6">
                            <!-- Direct Logout Link -->
                            <a href="/acrapi/views/auth/logout.php" class="text-red-600 hover:text-red-800 px-3 py-2 rounded-md text-sm font-medium">Logout</a>
                            
                            <!-- Profile dropdown -->
                            <div class="ml-3 relative">
                                <div>
                                    <button type="button" class="max-w-xs bg-gray-200 rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="user-menu" aria-expanded="false" aria-haspopup="true">
                                        <span class="sr-only">Open user menu</span>
                             
                                    </button>
                                </div>
                                <!-- Dropdown menu -->
                                <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu">
                                    <a href="/acrapi/views/profile/index.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Your Profile</a>
                                    <a href="/acrapi/views/auth/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Sign out</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content Container -->
            <main class="flex-grow container mx-auto mt-8 px-4 sm:px-6 lg:px-8">