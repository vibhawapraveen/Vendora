<?php

class AdminModel extends Model
{
    public function checkLogin($email, $password)
    {
        try {
            // Get admin by email
            $stmt = $this->db->prepare("SELECT * FROM admins WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            // If admin not found
            if (!$admin) {
                return false;
            }

            // Verify password
            if (password_verify($password, $admin['password'])) {
                return [
                    'id' => $admin['id'],
                    'email' => $admin['email'],
                    'name' => $admin['name']
                ];
            }

            return false;
        } catch (PDOException $e) {
            error_log("Admin login error: " . $e->getMessage());
            return false;
        }
    }

    public function getAdminById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT id, name, email, created_at FROM admins WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get admin error: " . $e->getMessage());
            return false;
        }
    }

    public function getAllAdmins()
    {
        try {
            $stmt = $this->db->query("SELECT id, name, email, created_at FROM admins ORDER BY created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all admins error: " . $e->getMessage());
            return [];
        }
    }

    public function createAdmin($name, $email, $password)
    {
        try {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("INSERT INTO admins (name, email, password) VALUES (:name, :email, :password)");
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => $passwordHash
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            // Check for duplicate email
            if ($e->getCode() == 23000) {
                return false;
            }
            error_log("Create admin error: " . $e->getMessage());
            return false;
        }
    }

    public function updateAdmin($id, $name, $email)
    {
        try {
            $stmt = $this->db->prepare("UPDATE admins SET name = :name, email = :email WHERE id = :id");
            return $stmt->execute([
                'id' => $id,
                'name' => $name,
                'email' => $email
            ]);
        } catch (PDOException $e) {
            error_log("Update admin error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteAdmin($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM admins WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Delete admin error: " . $e->getMessage());
            return false;
        }
    }
}

?>
