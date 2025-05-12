<?php
session_start();
// Authentication check has been removed to allow anyone to access
require_once '../layouts/header.php'; // Corrected path without "views/"
require_once($_SERVER['DOCUMENT_ROOT'].'/acrapi/config/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/acrapi/controller/role_controller.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/acrapi/controller/permission_controller.php');

$role_controller = new RoleController($conn);
$permission_controller = new PermissionController($conn);

// Handle form submissions
$message = '';
$message_type = '';

// Add Role
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $data = [
        'created_by' => 1, // Default user ID since we're not using authentication
        'name' => $_POST['name'],
        'description' => $_POST['description'] ?? ''
    ];
    
    $result = json_decode($role_controller->add($data), true);
    
    if ($result['status'] === 'success') {
        $_SESSION['message'] = $result['data']['message'];
        $_SESSION['message_type'] = 'success';
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['message'] = "Error adding role";
        $_SESSION['message_type'] = 'error';
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

// Update Role
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $data = [
        'name' => $_POST['name'],
        'description' => $_POST['description'] ?? ''
    ];
    
    $result = json_decode($role_controller->update($_POST['role_id'], 1, $data), true);
    
    if ($result['status'] === 'success') {
        $_SESSION['message'] = $result['data']['message'];
        $_SESSION['message_type'] = 'success';
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['message'] = "Error updating role";
        $_SESSION['message_type'] = 'error';
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

// Assign Role
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'assign_role') {
    $data = [
        'company_id' => $_POST['company_id'],
        'user_id' => $_POST['user_id'],
        'role_id' => $_POST['role_id']
    ];
    
    $result = json_decode($role_controller->assign_role($data), true);
    
    if ($result['status'] === 'success') {
        $_SESSION['message'] = $result['data']['message'];
        $_SESSION['message_type'] = 'success';
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['message'] = "Error assigning role";
        $_SESSION['message_type'] = 'error';
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

// Delete Role
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $role_id = $_POST['role_id'];
    
    $result = json_decode($role_controller->delete_role($role_id), true);
    
    if ($result['status'] === 'success') {
        $_SESSION['message'] = $result['data']['message'];
        $_SESSION['message_type'] = 'success';
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['message'] = "Error deleting role";
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

// Fetch Roles
$roles_response = json_decode($role_controller->get_all(), true);
$roles = $roles_response['data']['roles'] ?? [];

// Fetch Permissions (for assignable permissions)
$permissions_response = json_decode($permission_controller->get_all(), true);
$permissions = $permissions_response['data']['roles'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles Management</title>
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
        .actions a {
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
        }
        .edit-action {
            background-color: #4CAF50;
            color: white;
        }
        .delete-action {
            background-color: #f44336;
            color: white;
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
        .permissions-list {
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>Roles Management</h1>

    <?php if (!empty($message)): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="tabs">
        <div class="tab active" onclick="showTab('roles')">Roles</div>
        <div class="tab" onclick="showTab('assign')">Assign Role</div>
    </div>

    <div id="roles" class="tab-content active">
        <div class="form-container">
            <h2><?php echo isset($_GET['edit']) ? 'Edit Role' : 'Add New Role'; ?></h2>
            <form method="POST" action="">
                <?php 
                // Prepare edit mode if an edit is requested
                $edit_role = null;
                if (isset($_GET['edit'])) {
                    foreach ($roles as $role) {
                        if ($role['id'] == $_GET['edit']) {
                            $edit_role = $role;
                            break;
                        }
                    }
                }
                ?>
                <input type="hidden" name="action" value="<?php echo isset($_GET['edit']) ? 'update' : 'add'; ?>">
                <?php if (isset($_GET['edit'])): ?>
                    <input type="hidden" name="role_id" value="<?php echo htmlspecialchars($edit_role['id']); ?>">
                <?php endif; ?>

                <label for="name">Role Name:</label>
                <input type="text" id="name" name="name" 
                       value="<?php echo isset($edit_role) ? htmlspecialchars($edit_role['name']) : ''; ?>" 
                       required>

                <label for="description">Description (Optional):</label>
                <textarea id="description" name="description" rows="3"><?php 
                    echo isset($edit_role) ? htmlspecialchars($edit_role['description'] ?? '') : ''; 
                ?></textarea>

                <input type="submit" value="<?php echo isset($_GET['edit']) ? 'Update Role' : 'Add Role'; ?>">
                
                <?php if (isset($_GET['edit'])): ?>
                    <a href="index.php" style="display: inline-block; margin-left: 10px; color: red; text-decoration: none;">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <h2>Roles List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Permissions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $role): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($role['id']); ?></td>
                        <td><?php echo htmlspecialchars($role['name']); ?></td>
                        <td><?php echo htmlspecialchars($role['description'] ?? ''); ?></td>
                        <td class="permissions-list">
                            <?php 
                            $permissions_list = $role['permissions'] ?? 'No permissions';
                            echo htmlspecialchars($permissions_list); 
                            ?>
                        </td>
                        <td class="actions">
                            <a href="?edit=<?php echo $role['id']; ?>" class="edit-action">Edit</a>
                            <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="role_id" value="<?php echo $role['id']; ?>">
                                <input type="submit" value="Delete" class="delete-action" style="background:none; color:#f44336; border:none; padding:0; cursor:pointer;">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div id="assign" class="tab-content">
        <div class="form-container">
            <h2>Assign Role to User</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="assign_role">

                <label for="company_id">Company ID:</label>
                <input type="number" id="company_id" name="company_id" required>

                <label for="user_id">User ID:</label>
                <input type="number" id="user_id" name="user_id" required>

                <label for="role_id">Role ID:</label>
                <input type="number" id="role_id" name="role_id" required>

                <input type="submit" value="Assign Role">
            </form>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.getElementById('roles').classList.remove('active');
            document.getElementById('assign').classList.remove('active');

            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));

            // Show selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Highlight selected tab
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
<?php require_once '../layouts/footer.php'; ?>