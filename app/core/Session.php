<?php
class Session
{
    public static function login($user)
    {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'] ?? '',
            'email' => $user['email'],
            'mobile_number' => $user['mobile_number'] ?? '',
            'profile_picture' => $user['profile_picture'] ?? '',
            'store_name' => $user['store_name'],
            'store_id' => $user['store_id'],
            'role' => $user['role']
        ];
        session_regenerate_id(true);
    }

    public static function loginCustomer($user)
    {
        $_SESSION['user'] = [
            'customer_id' => $user['id'],
            'customer_email' => $user['email'],
            'customer_name' => $user['name'],
        ];
        session_regenerate_id(true);
    }

    public static function loginAdmin($admin)
    {
        $_SESSION['user'] = [
            'id' => $admin['id'],
            'email' => $admin['email'],
            'name' => $admin['name'] ?? 'Admin',
            'role' => 'admin'
        ];
        session_regenerate_id(true);
    }

    public static function logout()
    {
        unset($_SESSION['user']);
        session_destroy();
    }

    public static function user()
    {
        return $_SESSION['user'] ?? null;
    }

    public static function check()
    {
        return isset($_SESSION['user']);
    }

    public static function role($role)
    {
        return (self::check() && $_SESSION['user']['role'] === $role);
    }

    public static function requireRole($roles = [], $redirect_url = "auth/login")
    {
        if (!self::check() || !in_array($_SESSION['user']['role'], $roles)) {
            $redirect_url = ROOT . $redirect_url;
            header("Location: $redirect_url");
            exit;
        }
    }
}
