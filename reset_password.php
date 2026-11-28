<?php
require '_base.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error    = '';
$success  = '';
$email    = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email    = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $pass1    = $_POST['new_password'] ?? '';
    $pass2    = $_POST['confirm_password'] ?? '';

    if ($email === '' || $username === '' || $pass1 === '' || $pass2 === '') {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (
        strlen($pass1) < 8 ||
        !preg_match('/[A-Z]/', $pass1) ||
        !preg_match('/[a-z]/', $pass1) ||
        !preg_match('/[0-9]/', $pass1)
    ) {
        $error = 'New password must be at least 8 characters and include uppercase, lowercase, and a number.';
    } elseif ($pass1 !== $pass2) {
        $error = 'Passwords do not match.';
    } else {
        // Verify both email and username together (prevents unverified resets & email enumeration)
        $stmt = $pdo->prepare("SELECT user_id, password_hash FROM users WHERE email = ? AND username = ?");
        $stmt->execute([$email, $username]);
        $row = $stmt->fetch();

        if (!$row) {
            $error = 'Account details could not be verified. Please check your email and username.';
        } elseif (password_verify($pass1, $row['password_hash'])) {
            $error = 'New password must be different from your current password.';
        } else {
            $new_hash = password_hash($pass1, PASSWORD_DEFAULT);
            $upd = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
            $upd->execute([$new_hash, $row['user_id']]);

            $success  = 'Password reset successful! You can now log in with your new password.';
            $email    = '';
            $username = '';
        }
    }
}
?>
<?php require '_head.php'; ?>

<div class="auth-wrapper">
    <div class="auth-card" style="max-width: 440px;">
        <h2>Reset Password</h2>
        <p class="auth-subtitle">Verify your email and username to set a new password.</p>

        <?php if ($error !== ''): ?>
            <div class="alert-error"><?= encode($error) ?></div>
        <?php endif; ?>

        <?php if ($success !== ''): ?>
            <div class="alert-success"><?= encode($success) ?></div>
        <?php endif; ?>

        <form method="post" novalidate>
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="email">Registered Email Address</label>
                <input type="email" id="email" name="email" placeholder="you@example.com"
                       value="<?= encode($email) ?>" required>
            </div>

            <div class="form-group">
                <label for="username">Registered Username</label>
                <input type="text" id="username" name="username" placeholder="Your username"
                       value="<?= encode($username) ?>" required>
            </div>

            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password"
                       placeholder="Min. 8 chars (upper, lower, number)" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password"
                       placeholder="Re-enter new password" required>
            </div>

            <button type="submit" class="btn-submit">Reset Password</button>
        </form>

        <div class="auth-footer">
            <a href="login.php">&larr; Back to Login</a>
        </div>
    </div>
</div>

<?php require '_foot.php'; ?>