<?php

class Member
{
    private $pdo;
    private const ADMIN_SECRET_CODE = 'WILLEM_ADMIN_2026';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // 1. Audited Server-Side Validation for Member & Admin Registration
    public function validateRegistration($data)
    {
        $errors = [];

        $username        = trim($data['username'] ?? '');
        $fullName        = trim($data['full_name'] ?? '');
        $email           = trim($data['email'] ?? '');
        $phone           = trim($data['phone_number'] ?? '');
        $role            = strtolower(trim($data['role'] ?? 'member'));
        $adminCode       = trim($data['admin_code'] ?? '');
        $password        = $data['password'] ?? '';
        $confirmPassword = $data['confirm_password'] ?? '';

        // Username validation (required, 3-50 chars, unique)
        if ($username === '') {
            $errors['username'] = 'Username is required.';
        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $errors['username'] = 'Username must be between 3 and 50 characters.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors['username'] = 'Username can only contain letters, numbers, and underscores.';
        } elseif ($this->usernameExists($username)) {
            $errors['username'] = 'This username is already taken.';
        }

        // Full Name validation (required, 2-100 chars)
        if ($fullName === '') {
            $errors['full_name'] = 'Full name is required.';
        } elseif (strlen($fullName) < 2 || strlen($fullName) > 100) {
            $errors['full_name'] = 'Full name must be between 2 and 100 characters.';
        } elseif (!preg_match("/^[a-zA-Z\s.'-]+$/", $fullName)) {
            $errors['full_name'] = 'Full name can only contain letters, spaces, dots, hyphens, or apostrophes.';
        }

        // Email validation (required, valid format, max 100 chars, unique)
        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format.';
        } elseif (strlen($email) > 100) {
            $errors['email'] = 'Email must not exceed 100 characters.';
        } elseif ($this->emailExists($email)) {
            $errors['email'] = 'This email is already registered.';
        }

        // Phone Number validation (optional, max 20 chars)
        if ($phone !== '' && !preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) {
            $errors['phone_number'] = 'Phone number must be 7–20 characters (digits, spaces, +, or -).';
        }

        // Role & Admin Secret Code validation (matches ENUM('member', 'admin'))
        if (!in_array($role, ['member', 'admin'], true)) {
            $errors['role'] = 'Invalid account role selected.';
        } elseif ($role === 'admin') {
            if ($adminCode === '') {
                $errors['admin_code'] = 'Admin registration code is required to register as Admin.';
            } elseif (!hash_equals(self::ADMIN_SECRET_CODE, $adminCode)) {
                $errors['admin_code'] = 'Invalid Admin registration code.';
            }
        }

        // Password validation (required, min 8 chars, uppercase + lowercase + number)
        if ($password === '') {
            $errors['password'] = 'Password is required.';
        } elseif (
            strlen($password) < 8 ||
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[0-9]/', $password)
        ) {
            $errors['password'] = 'Password must be at least 8 characters and include uppercase, lowercase, and a number.';
        }

        // Confirm Password validation
        if ($confirmPassword === '') {
            $errors['confirm_password'] = 'Please confirm your password.';
        } elseif ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }

        return $errors;
    }

    private function usernameExists($username)
    {
        $stmt = $this->pdo->prepare('SELECT user_id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return $stmt->fetch() !== false;
    }

    private function emailExists($email)
    {
        $stmt = $this->pdo->prepare('SELECT user_id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }

    // 2. Insert Validated Member or Admin into Database
    public function register($data)
    {
        $username     = trim($data['username']);
        $fullName     = trim($data['full_name']);
        $email        = trim($data['email']);
        $phone        = trim($data['phone_number'] ?? '');
        $phoneValue   = ($phone !== '') ? $phone : null;
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $role         = strtolower(trim($data['role'] ?? 'member'));

        if (!in_array($role, ['member', 'admin'], true)) {
            $role = 'member';
        }

        $sql = 'INSERT INTO users (username, password_hash, full_name, email, phone_number, role)
                VALUES (?, ?, ?, ?, ?, ?)';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $username,
            $passwordHash,
            $fullName,
            $email,
            $phoneValue,
            $role
        ]);
    }

    // 3. Admin Listing & Search
    public function searchMembers($search = '', $roleFilter = '')
    {
        $search     = trim($search);
        $roleFilter = strtolower(trim($roleFilter));

        if (!in_array($roleFilter, ['', 'member', 'admin'], true)) {
            $roleFilter = '';
        }

        $sql    = 'SELECT user_id, username, full_name, email, phone_number, role FROM users WHERE 1=1';
        $params = [];

        if ($search !== '') {
            $escaped = '%' . addcslashes($search, '%_\\') . '%';
            $sql .= ' AND (username LIKE ? OR full_name LIKE ? OR email LIKE ? OR phone_number LIKE ? OR CAST(user_id AS CHAR) = ?)';
            $params[] = $escaped;
            $params[] = $escaped;
            $params[] = $escaped;
            $params[] = $escaped;
            $params[] = $search;
        }

        if ($roleFilter !== '') {
            $sql .= ' AND role = ?';
            $params[] = $roleFilter;
        }

        $sql .= ' ORDER BY user_id ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // 4. Admin Member Detail View
    public function getMemberById($userId)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE user_id = ?');
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    // 5. Admin Role Update (with self-demotion protection)
    public function updateMemberRole($targetUserId, $newRole, $currentAdminId)
    {
        $newRole = strtolower(trim($newRole));

        if (!in_array($newRole, ['member', 'admin'], true)) {
            return 'Invalid role selected.';
        }

        if ((int)$targetUserId === (int)$currentAdminId && $newRole !== 'admin') {
            return 'Security check: You cannot remove your own Admin privileges.';
        }

        $stmt = $this->pdo->prepare('UPDATE users SET role = ? WHERE user_id = ?');
        $stmt->execute([$newRole, $targetUserId]);
        return true;
    }

    // 6. Session Security & Role Checks
    public static function isLoggedIn()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']) && isset($_SESSION['role']);
    }

    public static function isAdmin()
    {
        return self::isLoggedIn() && strtolower($_SESSION['role']) === 'admin';
    }

    public static function requireLogin()
    {
        if (!self::isLoggedIn()) {
            header('Location: login.php');
            exit();
        }

        // Enforce 30-minute idle session timeout
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > 1800) {
            session_unset();
            session_destroy();
            header('Location: login.php?timeout=1');
            exit();
        }
        $_SESSION['last_activity'] = time();
    }

    public static function requireRole($allowedRole)
    {
        self::requireLogin();

        if (strtolower($_SESSION['role']) !== strtolower($allowedRole)) {
            header('Location: index.php?error=unauthorized');
            exit();
        }
    }

    // Extra security check for restricted Admin pages (re-verifies role against DB)
    public static function requireAdmin($pdo)
    {
        self::requireLogin();

        $stmt = $pdo->prepare('SELECT role FROM users WHERE user_id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        if (!$user || strtolower($user['role']) !== 'admin') {
            $_SESSION['role'] = $user['role'] ?? 'member';
            http_response_code(403);
            header('Location: index.php?error=unauthorized');
            exit();
        }

        $_SESSION['role'] = $user['role'];
    }
}