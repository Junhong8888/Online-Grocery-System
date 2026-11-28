<?php
require '_base.php';

if (is_logged_in()) {
    redirect(is_admin() ? 'admin_members.php' : 'index.php');
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);

            $_SESSION['user_id']       = $user['user_id'];
            $_SESSION['username']      = $user['username'];
            $_SESSION['full_name']     = $user['full_name'];
            $_SESSION['role']          = $user['role'];
            $_SESSION['last_activity'] = time();

            redirect(strcasecmp($user['role'], 'Admin') === 0 ? 'admin_members.php' : 'index.php');
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<?php require '_head.php'; ?>

<div class="auth-wrapper">
    <div class="auth-card" style="max-width: 420px;">
        <h2>Welcome Back</h2>
        <p class="auth-subtitle">Log in to your Willem Mart account.</p>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert-success">Registration successful! Please log in.</div>
        <?php endif; ?>

        <?php if (isset($_GET['timeout'])): ?>
            <div class="alert-error">Session expired due to inactivity. Please log in again.</div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="alert-error"><?= encode($error) ?></div>
        <?php endif; ?>

        <form method="post" novalidate>
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="you@example.com"
                       value="<?= encode($email) ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn-submit">Log In</button>
        </form>

        <div class="auth-footer">
            <a href="reset_password.php">Forgot Password?</a> &bull;
            <a href="register.php">Create an Account</a>
        </div>
    </div>
</div>

<?php require '_foot.php'; ?>