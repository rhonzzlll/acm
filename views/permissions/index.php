<?php
session_start();
// Authentication check has been removed to allow anyone to access
require_once '../layouts/header.php';
require_once($_SERVER['DOCUMENT_ROOT'].'/acrapi/config/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/acrapi/controller/permission_controller.php');

$permission_controller = new PermissionController($conn);

// Handle form submissions
$message = '';
$message_type = '';

// Add Permission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $data = [
        'created_by' => 1, // Default user ID since we're not using authentication
        'name' => $_POST['name'],
        'description' => $_POST['description'] ?? ''
    ];
    
    $result = json_decode($permission_controller->add($data), true);
    
    if ($result['status'] === 'success') {
        $_SESSION['message'] = $result['data']['message'];
        $_SESSION['message_type'] = 'success';
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['message'] = "Error adding permission";
        $_SESSION['message_type'] = 'error';
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

// Update Permission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $data = [
        'name' => $_POST['name'],
        'description' => $_POST['description'] ?? ''
    ];
    
    $result = json_decode($permission_controller->update($_POST['permission_id'], 1, $data), true);
    
    if ($result['status'] === 'success') {
        $_SESSION['message'] = $result['data']['message'];
        $_SESSION['message_type'] = 'success';
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['message'] = "Error updating permission";
        $_SESSION['message_type'] = 'error';
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

// Delete Permission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $permission_id = $_POST['permission_id'];
    
    $result = json_decode($permission_controller->delete($permission_id, 1), true);
    
    if ($result['status'] === 'success') {
        $_SESSION['message'] = $result['data']['message'] ?? "Permission deleted successfully";
        $_SESSION['message_type'] = 'success';
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['message'] = $result['message'] ?? "Error deleting permission";
        $_SESSION['message_type'] = 'error';
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

// Assign Permission to Role (Optional)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'assign') {
    $data = [
        'role_id' => $_POST['role_id'],
        'permission_id' => $_POST['permission_id']
    ];
    
    $result = json_decode($permission_controller->assign_permission($data), true);
    
    if ($result['status'] === 'success') {
        $_SESSION['message'] = $result['data']['message'];
        $_SESSION['message_type'] = 'success';
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['message'] = "Error assigning permission";
        $_SESSION['message_type'] = 'error';
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

// Check for messages from redirect
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    // Clear the session variables
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// Fetch Permissions
$permissions_response = json_decode($permission_controller->get_all(), true);
$permissions = $permissions_response['data']['roles'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permissions Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .form-container {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .form-container input, 
        .form-container select,
        .form-container textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .tabs {
            display: flex;
            margin-bottom: 20px;
        }
        .tab {
            padding: 10px 15px;
            background-color: #f1f1f1;
            cursor: pointer;
            border: 1px solid #ddd;
            margin-right: 5px;
        }
        .tab.active {
            background-color: #ddd;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
        }
        .delete-btn:hover {
            background-color: #d32f2f;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 40%;
            border-radius: 5px;
        }
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            gap: 10px;
        }
        .btn-confirm {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
            border-radius: 3px;
        }
        .btn-cancel {
            background-color: #ccc;
            color: black;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <h1>Permissions Management</h1>

    <?php if (!empty($message)): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="tabs">
        <div class="tab active" onclick="showTab('permissions')">Permissions</div>
        <div class="tab" onclick="showTab('assign')">Assign Permission</div>
    </div>

    <div id="permissions" class="tab-content active">
        <div class="form-container">
            <h2><?php echo isset($_GET['edit']) ? 'Edit Permission' : 'Add New Permission'; ?></h2>
            <form method="POST" action="">
                <?php 
                // Prepare edit mode if an edit is requested
                $edit_permission = null;
                if (isset($_GET['edit'])) {
                    foreach ($permissions as $permission) {
                        if ($permission['id'] == $_GET['edit']) {
                            $edit_permission = $permission;
                            break;
                        }
                    }
                }
                ?>
                <input type="hidden" name="action" value="<?php echo isset($_GET['edit']) ? 'update' : 'add'; ?>">
                <?php if (isset($_GET['edit'])): ?>
                    <input type="hidden" name="permission_id" value="<?php echo htmlspecialchars($edit_permission['id']); ?>">
                <?php endif; ?>

                <label for="name">Permission Name:</label>
                <input type="text" id="name" name="name" 
                       value="<?php echo isset($edit_permission) ? htmlspecialchars($edit_permission['name']) : ''; ?>" 
                       required>

                <label for="description">Description (Optional):</label>
                <textarea id="description" name="description" rows="3"><?php 
                    echo isset($edit_permission) ? htmlspecialchars($edit_permission['description'] ?? '') : ''; 
                ?></textarea>

                <input type="submit" value="<?php echo isset($_GET['edit']) ? 'Update Permission' : 'Add Permission'; ?>">
                
                <?php if (isset($_GET['edit'])): ?>
                    <a href="index.php" style="display: inline-block; margin-left: 10px; color: red; text-decoration: none;">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <h2>Permissions List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($permissions as $permission): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($permission['id']); ?></td>
                        <td><?php echo htmlspecialchars($permission['name']); ?></td>
                        <td><?php echo htmlspecialchars($permission['description'] ?? ''); ?></td>
                        <td class="actions">
                            <a href="?edit=<?php echo $permission['id']; ?>" style="color: blue;">Edit</a>
                            <button class="delete-btn" onclick="confirmDelete(<?php echo $permission['id']; ?>, '<?php echo htmlspecialchars($permission['name']); ?>')">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div id="assign" class="tab-content">
        <div class="form-container">
            <h2>Assign Permission to Role</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="assign">

                <label for="role_id">Role ID:</label>
                <input type="number" id="role_id" name="role_id" required>

                <label for="permission_id">Permission ID:</label>
                <input type="number" id="permission_id" name="permission_id" required>

                <input type="submit" value="Assign Permission">
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Confirm Delete</h3>
            <p>Are you sure you want to delete the permission "<span id="permissionName"></span>"?</p>
            <div class="modal-actions">
                <form id="deleteForm" method="POST" action="">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" id="deletePermissionId" name="permission_id" value="">
                    <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
                    <button type="submit" class="btn-confirm">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.getElementById('permissions').classList.remove('active');
            document.getElementById('assign').classList.remove('active');

            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));

            // Show selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Highlight selected tab
            event.target.classList.add('active');
        }
        
        // Delete confirmation modal functions
        function confirmDelete(permissionId, permissionName) {
            document.getElementById('deletePermissionId').value = permissionId;
            document.getElementById('permissionName').textContent = permissionName;
            document.getElementById('deleteModal').style.display = 'block';
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
        
        // Close modal when clicking outside the modal content
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                closeDeleteModal();
            }
        }
    </script>
</body>
</html>
<?php require_once '../layouts/footer.php'; ?>