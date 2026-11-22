<?php
require '_base.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home - Online Grocery System</title>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h2>
    <p>Username: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
    <p>Role: <?php echo htmlspecialchars($_SESSION['role']); ?></p>

    <p>
        <a href="profile.php">My Profile</a> | 
        <a href="logout.php">Log Out</a>
    </p>
</body>
</html>