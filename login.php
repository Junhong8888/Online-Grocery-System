<?php
require '_base.php';

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Server-side validation
    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    if ($password === '') {
        $errors['password'] = 'Password is required.';
    }

    // Verify user credentials
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $u = $stmt->fetch();

        if ($u && password_verify($password, $u['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $u['user_id'];
            $_SESSION['username']  = $u['username'];
            $_SESSION['full_name'] = $u['full_name'];
            $_SESSION['role']      = $u['role'];

            redirect('index.php');
        } else {
            $errors['login'] = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Online Grocery System</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 60px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"] { width: 100%; padding: 8px; box-sizing: border-box; }
        .error { color: red; font-size: 0.9em; margin-top: 4px; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; cursor: pointer; font-size: 16px; }
    </style>
</head>
<body>
    <h2>Login</h2>

    <?php if (isset($errors['login'])): ?>
        <p class="error"><?= encode($errors['login']) ?></p>
    <?php endif; ?>

    <form method="post" novalidate>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" value="<?= encode($email) ?>">
            <?php if (isset($errors['email'])): ?>
                <div class="error"><?= encode($errors['email']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password">
            <?php if (isset($errors['password'])): ?>
                <div class="error"><?= encode($errors['password']) ?></div>
            <?php endif; ?>
        </div>

        <button type="submit">Log In</button>
    </form>
</body>
</html>