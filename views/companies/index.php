<?php
session_start();
require_once '../layouts/header.php';
// Authentication check has been removed to allow anyone to access

require_once($_SERVER['DOCUMENT_ROOT'].'/acrapi/config/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/acrapi/controller/company_controller.php');

$company_controller = new CompanyController($conn);

// Handle form submissions
$message = '';
$message_type = '';

// Add Company
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $data = [
        'user_id' => 1, // Default user ID since we're not using authentication
        'name' => $_POST['name'],
        'industry' => $_POST['industry'],
        'location' => $_POST['location'],
        'tell_no' => $_POST['tell_no'],
        'founded_year' => $_POST['founded_year']
    ];
    
    $result = json_decode($company_controller->add($data), true);
    
    if ($result['status'] === 'success') {
        $message = $result['data']['message'];
        $message_type = 'success';
        // Redirect to prevent form resubmission on refresh
        header('Location: index.php?msg=added');
        exit;
    } else {
        $message = "Error adding company";
        $message_type = 'error';
    }
}

// Update Company
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $data = [
        'name' => $_POST['name'],
        'industry' => $_POST['industry'],
        'location' => $_POST['location'],
        'tell_no' => $_POST['tell_no'],
        'founded_year' => $_POST['founded_year']
    ];
    
    $result = json_decode($company_controller->update($_POST['company_id'], $data), true);
    
    if ($result['status'] === 'success') {
        $message = $result['data']['message'];
        $message_type = 'success';
        // Redirect to prevent form resubmission on refresh
        header('Location: index.php?msg=updated');
        exit;
    } else {
        $message = "Error updating company";
        $message_type = 'error';
    }
}

// Delete Company
if (isset($_GET['delete'])) {
    $result = json_decode($company_controller->delete($_GET['delete']), true);
    
    if ($result['status'] === 'success') {
        $message = "Company deleted successfully";
        $message_type = 'success';
        // Redirect to prevent action resubmission on refresh
        header('Location: index.php?msg=deleted');
        exit;
    } else {
        $message = "Error deleting company";
        $message_type = 'error';
    }
}

// Set message based on URL parameter
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'added':
            $message = "Company added successfully";
            $message_type = 'success';
            break;
        case 'updated':
            $message = "Company updated successfully";
            $message_type = 'success';
            break;
        case 'deleted':
            $message = "Company deleted successfully";
            $message_type = 'success';
            break;
    }
}

// Fetch Companies
$companies_response = json_decode($company_controller->get_all(), true);
$companies = $companies_response['data']['companies'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Companies Management</title>
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
        .form-container select {
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
        .delete-btn {
            color: red;
            text-decoration: none;
        }
        .edit-btn {
            color: blue;
            text-decoration: none;
        }
    </style>
    <script>
        function confirmDelete(id, name) {
            if (confirm('Are you sure you want to delete company "' + name + '"?')) {
                window.location.href = '?delete=' + id;
            }
        }
    </script>
</head>
<body>
    <h1>Companies Management</h1>

    <?php if (!empty($message)): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <h2><?php echo isset($_GET['edit']) ? 'Edit Company' : 'Add New Company'; ?></h2>
        <form method="POST" action="">
            <?php 
            // Prepare edit mode if an edit is requested
            $edit_company = null;
            if (isset($_GET['edit'])) {
                foreach ($companies as $company) {
                    if ($company['id'] == $_GET['edit']) {
                        $edit_company = $company;
                        break;
                    }
                }
            }
            ?>
            <input type="hidden" name="action" value="<?php echo isset($_GET['edit']) ? 'update' : 'add'; ?>">
            <?php if (isset($_GET['edit'])): ?>
                <input type="hidden" name="company_id" value="<?php echo htmlspecialchars($edit_company['id']); ?>">
            <?php endif; ?>

            <label for="name">Company Name:</label>
            <input type="text" id="name" name="name" 
                   value="<?php echo isset($edit_company) ? htmlspecialchars($edit_company['name']) : ''; ?>" 
                   required>

            <label for="industry">Industry:</label>
            <input type="text" id="industry" name="industry" 
                   value="<?php echo isset($edit_company) ? htmlspecialchars($edit_company['industry']) : ''; ?>" 
                   required>

            <label for="location">Location:</label>
            <input type="text" id="location" name="location" 
                   value="<?php echo isset($edit_company) ? htmlspecialchars($edit_company['location']) : ''; ?>" 
                   required>

            <label for="tell_no">Contact Number:</label>
            <input type="text" id="tell_no" name="tell_no" 
                   value="<?php echo isset($edit_company) ? htmlspecialchars($edit_company['tell_no']) : ''; ?>" 
                   required>

            <label for="founded_year">Founded Year:</label>
            <input type="number" id="founded_year" name="founded_year" 
                   value="<?php echo isset($edit_company) ? htmlspecialchars($edit_company['founded_year']) : ''; ?>" 
                   required>

            <input type="submit" value="<?php echo isset($_GET['edit']) ? 'Update Company' : 'Add Company'; ?>">
            
            <?php if (isset($_GET['edit'])): ?>
                <a href="index.php" style="display: inline-block; margin-left: 10px; color: red; text-decoration: none;">Cancel</a>
            <?php endif; ?>
        </form>
    </div>

    <h2>Companies List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Name</th>
                <th>Industry</th>
                <th>Location</th>
                <th>Contact Number</th>
                <th>Founded Year</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($companies as $company): ?>
                <tr>
                    <td><?php echo htmlspecialchars($company['id']); ?></td>
                    <td><?php echo htmlspecialchars($company['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($company['name']); ?></td>
                    <td><?php echo htmlspecialchars($company['industry']); ?></td>
                    <td><?php echo htmlspecialchars($company['location']); ?></td>
                    <td><?php echo htmlspecialchars($company['tell_no']); ?></td>
                    <td><?php echo htmlspecialchars($company['founded_year']); ?></td>
                    <td class="actions">
                        <a href="?edit=<?php echo $company['id']; ?>" class="edit-btn">Edit</a>
                        <a href="javascript:void(0)" onclick="confirmDelete('<?php echo $company['id']; ?>', '<?php echo addslashes(htmlspecialchars($company['name'])); ?>')" class="delete-btn">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
<?php require_once '../layouts/footer.php'; ?>