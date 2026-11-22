<?php
require '_base.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['user_id'];
$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $file_name = $_FILES['photo']['name'];
        $file_size = $_FILES['photo']['size'];
        $file_tmp  = $_FILES['photo']['tmp_name'];
        $file_type = $_FILES['photo']['type'];

        $allowed = array('image/jpeg', 'image/png', 'image/gif');

        if (!in_array($file_type, $allowed)) {
            $err = "Only JPG, PNG and GIF files are allowed.";
        } else if ($file_size > 2097152) {
            $err = "File size must be less than 2MB.";
        } else {
            $new_name = uniqid() . "_" . $file_name;
            $target = "uploads/" . $new_name;

            if (move_uploaded_file($file_tmp, $target)) {
                $stmt = $pdo->prepare("UPDATE users SET profile_photo = ? WHERE user_id = ?");
                $stmt->execute([$target, $id]);
                $msg = "Photo uploaded successfully.";
            } else {
                $err = "Failed to upload file.";
            }
        }
    } else {
        $err = "Please choose a photo.";
    }
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
</head>
<body>
    <h2>My Profile</h2>

    <?php if ($err != ''): ?>
        <p style="color:red;"><?php echo $err; ?></p>
    <?php endif; ?>

    <?php if ($msg != ''): ?>
        <p style="color:green;"><?php echo $msg; ?></p>
    <?php endif; ?>

    <?php if (!empty($user['profile_photo'])): ?>
        <img src="<?php echo $user['profile_photo']; ?>" width="120" height="120"><br><br>
    <?php else: ?>
        <p>No profile photo</p>
    <?php endif; ?>

    <p>Username: <?php echo htmlspecialchars($user['username']); ?></p>
    <p>Full Name: <?php echo htmlspecialchars($user['full_name']); ?></p>
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>

    <form method="post" enctype="multipart/form-data">
        <label>Change Profile Photo:</label><br>
        <input type="file" name="photo"><br><br>
        <button type="submit">Upload</button>
    </form>

    <br>
    <a href="index.php">Home</a> | <a href="logout.php">Logout</a>
</body>
</html>