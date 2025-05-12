<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/acrapi/error/http_error.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/acrapi/handler/try_catch_handler.php');

    class RoleController {

		private $conn;
		
		public function __construct($conn) {
			$this->conn = $conn;
		}

        // CREATE
        public function add($data, $query_data = null) {
            return handle(function() use ($data, $query_data) {
                $created_by = mysqli_real_escape_string($this->conn, $data['created_by']);
                $name = mysqli_real_escape_string($this->conn, $data['name']);
                $description = mysqli_real_escape_string($this->conn, $data['description']);
    
                $query = "INSERT INTO roles (created_by, name, description) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($this->conn, $query);
                mysqli_stmt_bind_param($stmt, "iss", $created_by, $name, $description);
                $result = mysqli_stmt_execute($stmt);

                if($result) {
                    return [
                        "status" => "success",
                        "data" => [
                            "code" => 200,
                            "message" => "New role successfully created."
                        ]
                    ];
                } else {
                    throw new HttpError("400 Bad Request");
                }
            });
        }

        // CREATE | ASSIGN ROLE
        public function assign_role($data) {
            return handle(function() use ($data) {
                $company_id = mysqli_real_escape_string($this->conn, $data['company_id']);
                $user_id = mysqli_real_escape_string($this->conn, $data['user_id']);
                $role_id = mysqli_real_escape_string($this->conn, $data['role_id']);
    
                $query = "INSERT INTO role_groups (company_id, user_id, role_id) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($this->conn, $query);
                mysqli_stmt_bind_param($stmt, "iii", $company_id, $user_id, $role_id);
                $result = mysqli_stmt_execute($stmt);

                if($result) {
                    return [
                        "status" => "success",
                        "data" => [
                            "code" => 200,
                            "message" => "Assigning role successfully created."
                        ]
                    ];
                } else {
                    throw new HttpError("400 Bad Request");
                }
            });
        }

        // RETRIEVE
        public function get_all() {
            return handle(function() {
                $query = "
                    SELECT r.id, r.name, r.description, r.created_by, GROUP_CONCAT(p.name SEPARATOR ', ') AS permissions
                    FROM roles r
                    LEFT JOIN permission_groups rp ON rp.role_id = r.id
                    LEFT JOIN permissions p ON rp.permission_id = p.id
                    GROUP BY r.id
                ";
                $result = mysqli_query($this->conn, $query);
                $roles = array();
                
                while($row = mysqli_fetch_assoc($result)) {
                    $roles[] = $row;
                }

                return [
                    "status" => "success",
                    "data" => [
                        "code" => 200,
                        "roles" => $roles
                    ]
                ];
            });
        }

        // RETRIEVE
        public function get_all_created_by($created_by) {
            return handle(function() use ($created_by) {
                $created_by = mysqli_real_escape_string($this->conn, $created_by);
                
                $query = "
                    SELECT r.id, r.name, r.description, r.created_by, GROUP_CONCAT(p.name SEPARATOR ', ') AS permissions
                    FROM roles r
                    LEFT JOIN permission_groups rp ON rp.role_id = r.id
                    LEFT JOIN permissions p ON rp.permission_id = p.id
                    WHERE r.created_by = ?
                    GROUP BY r.id
                ";
                
                $stmt = mysqli_prepare($this->conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $created_by);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                $roles = array();
                while($row = mysqli_fetch_assoc($result)) {
                    $roles[] = $row;
                }

                if($created_by) {
                    return [
                        "status" => "success",
                        "data" => [
                            "code" => 200,
                            "roles" => $roles
                        ]
                    ];
                } else {
                    throw new HttpError("400 Bad Request");
                }
            });
        }

        // UPDATE
        public function update($role_id, $created_by, $data) {
            return handle(function() use ($role_id, $created_by, $data) {
                $role_id = mysqli_real_escape_string($this->conn, $role_id);
                $created_by = mysqli_real_escape_string($this->conn, $created_by);
                $name = mysqli_real_escape_string($this->conn, $data['name']);
                $description = mysqli_real_escape_string($this->conn, $data['description']);
                
                $query = "
                    UPDATE roles
                    SET
                        name = ?,
                        description = ?
                    WHERE
                        id = ? AND
                        created_by = ?
                ";
                
                $stmt = mysqli_prepare($this->conn, $query);
                mysqli_stmt_bind_param($stmt, "ssii", $name, $description, $role_id, $created_by);
                $result = mysqli_stmt_execute($stmt);

                if($result) {
                    return [
                        "status" => "success",
                        "data" => [
                            "code" => 200,
                            "message" => "Role successfully updated."
                        ]
                    ];
                } else {
                    throw new HttpError("400 Bad Request");
                }
            });
        }

        // DELETE ROLE GROUP
        public function delete_role_group($company, $user, $role) {
            return handle(function() use ($company, $user, $role) {
                $company = mysqli_real_escape_string($this->conn, $company);
                $user = mysqli_real_escape_string($this->conn, $user);
                $role = mysqli_real_escape_string($this->conn, $role);
                
                $query = "
                    DELETE FROM role_groups
                    WHERE
                        company_id = ? AND
                        user_id = ? AND
                        role_id = ?
                ";
                
                $stmt = mysqli_prepare($this->conn, $query);
                mysqli_stmt_bind_param($stmt, "iii", $company, $user, $role);
                $result = mysqli_stmt_execute($stmt);

                if($result) {
                    return [
                        "status" => "success",
                        "data" => [
                            "code" => 200,
                            "message" => "Role assignment successfully deleted."
                        ]
                    ];
                } else {
                    throw new HttpError("400 Bad Request");
                }
            });
        }
        
        // DELETE ROLE BY ID - Add this new method to match your frontend
        public function delete_role($role_id) {
            return handle(function() use ($role_id) {
                $role_id = mysqli_real_escape_string($this->conn, $role_id);
                
                // First delete any role assignments
                $query1 = "DELETE FROM role_groups WHERE role_id = ?";
                $stmt1 = mysqli_prepare($this->conn, $query1);
                mysqli_stmt_bind_param($stmt1, "i", $role_id);
                mysqli_stmt_execute($stmt1);
                
                // Then delete any permission assignments
                $query2 = "DELETE FROM permission_groups WHERE role_id = ?";
                $stmt2 = mysqli_prepare($this->conn, $query2);
                mysqli_stmt_bind_param($stmt2, "i", $role_id);
                mysqli_stmt_execute($stmt2);
                
                // Finally delete the role itself
                $query3 = "DELETE FROM roles WHERE id = ?";
                $stmt3 = mysqli_prepare($this->conn, $query3);
                mysqli_stmt_bind_param($stmt3, "i", $role_id);
                $result = mysqli_stmt_execute($stmt3);

                if($result) {
                    return [
                        "status" => "success",
                        "data" => [
                            "code" => 200,
                            "message" => "Role successfully deleted."
                        ]
                    ];
                } else {
                    throw new HttpError("400 Bad Request");
                }
            });
        }

        // GET ROLE BY ID
        public function get_by_id($role_id) {
            return handle(function() use ($role_id) {
                $role_id = mysqli_real_escape_string($this->conn, $role_id);
                
                $query = "
                    SELECT r.id, r.name, r.description, r.created_by
                    FROM roles r
                    WHERE r.id = ?
                ";
                
                $stmt = mysqli_prepare($this->conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $role_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                $role = mysqli_fetch_assoc($result);
                
                if($role) {
                    return [
                        "status" => "success",
                        "data" => [
                            "code" => 200,
                            "role" => $role
                        ]
                    ];
                } else {
                    throw new HttpError("404 Not Found");
                }
            });
        }
    }

    // For your API endpoint (e.g., api/v1/roles/index.php)
    // In the DELETE handler section:

    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // Extract role_id from URL 
        $url_parts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $role_id = end($url_parts); // Get the last part of the URL which should be the role_id
        
        if (is_numeric($role_id)) {
            $response = $role_controller->delete_role($role_id);
            echo json_encode($response);
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Invalid role ID"
            ]);
        }
    }

?>