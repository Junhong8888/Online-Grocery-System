<?php

class Member
{
    private $pdo;

    // Pass the $pdo database connection from _base.php into this class
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // 1. Server-side validation for registration inputs
    public function validateRegistration($data)
    {
        $errors = [];

        $username        = trim($data['username'] ?? '');
        $fullName        = trim($data['full_name'] ?? '');
        $email           = trim($data['email'] ?? '');
        $phone           = trim($data['phone_number'] ?? '');
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

        // Full Name validation (required, max 100 chars)
        if ($fullName === '') {
            $errors['full_name'] = 'Full name is required.';
        } elseif (strlen($fullName) > 100) {
            $errors['full_name'] = 'Full name must not exceed 100 characters.';
        }

        // Email validation (required, valid format, unique)
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
            $errors['phone_number'] = 'Invalid phone number format.';
        }

        // Password validation (required, minimum 6 characters)
        if ($password === '') {
            $errors['password'] = 'Password is required.';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters.';
        }

        // Confirm Password validation
        if ($confirmPassword === '') {
            $errors['confirm_password'] = 'Please confirm your password.';
        } elseif ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }

        return $errors;
    }

    // Check if username already exists in users table
    private function usernameExists($username)
    {
        $stmt = $this->pdo->prepare('SELECT user_id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return $stmt->fetch() !== false;
    }

    // Check if email already exists in users table
    private function emailExists($email)
    {
        $stmt = $this->pdo->prepare('SELECT user_id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }

    // 2. Insert validated member into the database
    public function register($data)
    {
        $username     = trim($data['username']);
        $fullName     = trim($data['full_name']);
        $email        = trim($data['email']);
        $phone        = trim($data['phone_number'] ?? '');
        $phoneValue   = ($phone !== '') ? $phone : null; // Matches DEFAULT NULL in SQL
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $defaultRole  = 'member'; // Matches ENUM('member', 'admin')

        $sql = 'INSERT INTO users (username, password_hash, full_name, email, phone_number, role)
                VALUES (?, ?, ?, ?, ?, ?)';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $username,
            $passwordHash,
            $fullName,
            $email,
            $phoneValue,
            $defaultRole
        ]);
    }

    // 3. Base Session Security & Role Checks (Member vs. Admin)
    public static function isLoggedIn()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']) && isset($_SESSION['role']);
    }

    public static function requireLogin()
    {
        if (!self::isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
    }

    public static function requireRole($allowedRole)
    {
        // First ensure the user is logged in
        self::requireLogin();

        // Compare roles case-insensitively ('member' or 'admin')
        $currentRole = strtolower($_SESSION['role']);
        $requiredRole = strtolower($allowedRole);

        if ($currentRole !== $requiredRole) {
            // Redirect unauthorized users back to home page
            header('Location: index.php');
            exit();
        }
    }
}