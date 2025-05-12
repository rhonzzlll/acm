<?php
 
$pageTitle = 'Edit Permission';
require_once '../views/layouts/header.php'; 
require_once '../model/permission.php';

// Initialize database connection (replace with your actual connection code)
$conn = new mysqli("localhost", "root", "", "acrapi_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?message=Invalid+permission+ID");
    exit;
}

$id = intval($_GET['id']);

// Fetch permission data
$query = "SELECT id, name, description FROM permissions WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php?message=Permission+not+found");
    exit;
}

$permission = $result->fetch_assoc();
$stmt->close();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $errors = [];
    
    $name = trim($_POST['name'] ?? '');
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    $description = trim($_POST['description'] ?? '');
    
    // Check if permission name already exists (excluding current permission)
    $checkQuery = "SELECT id FROM permissions WHERE name = ? AND id != ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("si", $name, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $errors[] = "A permission with this name already exists";
    }
    $stmt->close();
    
    // If no errors, update the permission
    if (empty($errors)) {
        $updateQuery = "UPDATE permissions SET name = ?, description = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssi", $name, $description, $id);
        
        if ($stmt->execute()) {
            header("Location: index.php?message=Permission+updated+successfully");
            exit;
        } else {
            $errors[] = "Error updating permission: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<div class="permission-edit max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">Edit Permission</h1>
                <p class="text-sm text-gray-600">Update permission details</p>
            </div>
            <div>
                <a href="index.php" class="text-blue-500 hover:underline text-sm">Back to Permissions</a>
            </div>
        </div>
        
        <form action="edit.php?id=<?php echo $id; ?>" method="post" class="p-6">
            <!-- Error messages -->
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <ul class="mt-1 list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Permission Name <span class="text-red-600">*</span></label>
                <input type="text" name="name" id="name" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                       value="<?php echo htmlspecialchars($permission['name']); ?>" required placeholder="e.g., user_create, role_view">
                <p class="mt-1 text-xs text-gray-500">Use snake_case format, e.g., resource_action</p>
            </div>
            
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="description" rows="3" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                          placeholder="Briefly describe what this permission allows"><?php echo htmlspecialchars($permission['description']); ?></textarea>
            </div>
            
            <div class="flex justify-end">
                <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md mr-2">Cancel</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">Update Permission</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../views/layouts/footer.php'; ?>