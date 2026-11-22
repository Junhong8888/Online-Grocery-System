<?php
require '_base.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $pass1 = $_POST['new_password'];
    $pass2 = $_POST['confirm_password'];

    if (empty($email) || empty($pass1) || empty($pass2)) {
        $error = "Please fill in all fields.";
    } else if ($pass1 != $pass2) {
        $error = "Passwords do not match.";
    } else if (strlen($pass1) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();

        if ($row) {
            $new_hash = password_hash($pass1, PASSWORD_DEFAULT);
            $upd = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
            $upd->execute([$new_hash, $email]);
            $success = "Password reset successful. You can login now.";
        } else {
            $error = "Email does not exist.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>

    <?php if ($error != ''): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($success != ''): ?>
        <p style="color:green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Email:</label><br>
        <input type="email" name="email"><br><br>

        <label>New Password:</label><br>
        <input type="password" name="new_password"><br><br>

        <label>Confirm Password:</label><br>
        <input type="password" name="confirm_password"><br><br>

        <button type="submit">Reset</button>
    </form>

    <p><a href="login.php">Back to Login</a></p>
</body>
</html>