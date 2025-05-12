<?php 
$pageTitle = 'Dashboard';
require_once '../layouts/header.php';
?>

<div class="dashboard">
    <h1 class="text-3xl font-bold mb-6">Dashboard</h1>

    <!-- Main Content Section -->
    <div class="mt-2">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Users Card -->
            <a href="../users/index.php" class="bg-white rounded-lg shadow-md p-6 hover:bg-blue-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-gray-500 text-sm uppercase">Manage Users</h3>
                        <p class="text-gray-700 mt-1">User administration</p>
                    </div>
                    <div class="bg-blue-100 text-blue-500 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </a>

            <!-- Roles Card -->
            <a href="../roles/index.php" class="bg-white rounded-lg shadow-md p-6 hover:bg-green-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-gray-500 text-sm uppercase">Manage Roles</h3>
                        <p class="text-gray-700 mt-1">Role configuration</p>
                    </div>
                    <div class="bg-green-100 text-green-500 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                </div>
            </a>

            <!-- Companies Card -->
            <a href="../companies/index.php" class="bg-white rounded-lg shadow-md p-6 hover:bg-yellow-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-gray-500 text-sm uppercase">Manage Companies</h3>
                        <p class="text-gray-700 mt-1">Company profiles</p>
                    </div>
                    <div class="bg-yellow-100 text-yellow-500 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
            </a>

            <!-- Permissions Card -->
            <a href="../permissions/index.php" class="bg-white rounded-lg shadow-md p-6 hover:bg-red-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-gray-500 text-sm uppercase">Manage Permissions</h3>
                        <p class="text-gray-700 mt-1">Access control</p>
                    </div>
                    <div class="bg-red-100 text-red-500 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                </div>
            </a>

            <!-- Role Groups Card -->
            
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<?php require_once '../layouts/footer.php'; ?>