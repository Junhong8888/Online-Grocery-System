<?php
require '_base.php';
require_login();

$id = (int)$_SESSION['user_id'];
$msg = '';
$err = '';
$errors = [];

// Fetch current user record
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    session_unset();
    session_destroy();
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    // ------------------------------------------------------------------------
    // Action 1: Update Profile Details (Full Name & Phone Number)
    // ------------------------------------------------------------------------
    if ($action === 'update_profile') {
        $full_name    = trim($_POST['full_name'] ?? '');
        $phone_number = trim($_POST['phone_number'] ?? '');

        if ($full_name === '') {
            $errors['full_name'] = 'Full name is required.';
        } elseif (strlen($full_name) < 2 || strlen($full_name) > 100) {
            $errors['full_name'] = 'Full name must be between 2 and 100 characters.';
        } elseif (!preg_match("/^[a-zA-Z\s.'-]+$/", $full_name)) {
            $errors['full_name'] = 'Full name can only contain letters, spaces, dots, hyphens, or apostrophes.';
        }

        if ($phone_number !== '' && !preg_match('/^[0-9+\-\s]{7,20}$/', $phone_number)) {
            $errors['phone_number'] = 'Phone number must be 7–20 characters (digits, spaces, +, or -).';
        }

        if (empty($errors)) {
            $phoneValue = ($phone_number !== '') ? $phone_number : null;
            $upd = $pdo->prepare("UPDATE users SET full_name = ?, phone_number = ? WHERE user_id = ?");
            $upd->execute([$full_name, $phoneValue, $id]);

            $_SESSION['full_name'] = $full_name;
            $user['full_name']     = $full_name;
            $user['phone_number']  = $phoneValue;
            $msg = 'Profile details updated successfully.';
        }
    }

    // ------------------------------------------------------------------------
    // Action 2: Secure Profile Photo Upload
    // ------------------------------------------------------------------------
    if ($action === 'upload_photo') {
        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
            $err = 'Please choose an image file to upload.';
        } elseif ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $err = 'File upload failed. Please try again.';
        } elseif ($_FILES['photo']['size'] > 2097152) {
            $err = 'File size must be less than 2MB.';
        } else {
            $file_tmp = $_FILES['photo']['tmp_name'];

            // Inspect actual file bytes on server (never trust $_FILES['type'])
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $real_mime = finfo_file($finfo, $file_tmp);
            finfo_close($finfo);

            $allowed_mimes = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif',
            ];

            if (!isset($allowed_mimes[$real_mime]) || getimagesize($file_tmp) === false) {
                $err = 'Invalid image file. Only genuine JPG, PNG, and GIF images are allowed.';
            } else {
                if (!is_dir('uploads')) {
                    mkdir('uploads', 0755, true);
                }

                // Generate safe randomized filename with whitelisted extension
                $new_name = bin2hex(random_bytes(16)) . '.' . $allowed_mimes[$real_mime];
                $target   = 'uploads/' . $new_name;

                if (move_uploaded_file($file_tmp, $target)) {
                    // Safely delete previous profile photo if stored in uploads/
                    $old_photo = $user['profile_photo'] ?? '';
                    if ($old_photo !== '' && strpos($old_photo, 'uploads/') === 0 && file_exists($old_photo)) {
                        unlink($old_photo);
                    }

                    $upd = $pdo->prepare("UPDATE users SET profile_photo = ? WHERE user_id = ?");
                    $upd->execute([$target, $id]);
                    $user['profile_photo'] = $target;
                    $msg = 'Profile photo updated successfully.';
                } else {
                    $err = 'Unable to save uploaded photo. Please try again.';
                }
            }
        }
    }
}
?>
<?php require '_head.php'; ?>

<div class="auth-wrapper">
    <div class="auth-card" style="max-width: 540px;">
        <h2>My Profile</h2>
        <p class="auth-subtitle">Manage your Willem Mart account information and photo.</p>

        <?php if ($err !== ''): ?>
            <div class="alert-error"><?= encode($err) ?></div>
        <?php endif; ?>

        <?php if ($msg !== ''): ?>
            <div class="alert-success"><?= encode($msg) ?></div>
        <?php endif; ?>

        <div style="display: flex; align-items: center; gap: 18px; margin-bottom: 24px;">
            <?php if (!empty($user['profile_photo']) && file_exists($user['profile_photo'])): ?>
                <img src="<?= encode($user['profile_photo']) ?>" alt="Profile Photo"
                     style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 2px solid #00665c;">
            <?php else: ?>
                <div style="width: 90px; height: 90px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; color: #475569; font-weight: 600; font-size: 13px;">
                    No Photo
                </div>
            <?php endif; ?>

            <div>
                <div style="font-weight: 700; font-size: 17px; color: #0f172a;"><?= encode($user['full_name']) ?></div>
                <div style="font-size: 13px; color: #64748b;">@<?= encode($user['username']) ?> &bull; <?= encode(ucfirst($user['role'])) ?></div>
                <div style="font-size: 13px; color: #64748b;"><?= encode($user['email']) ?></div>
            </div>
        </div>

        <!-- Photo Upload Form -->
        <form method="post" enctype="multipart/form-data" style="margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #e2e8f0;">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="upload_photo">

            <div class="form-group">
                <label for="photo">Change Profile Photo (JPG, PNG, GIF &bull; Max 2MB)</label>
                <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/gif" required>
            </div>

            <button type="submit" class="btn-submit">Upload Photo</button>
        </form>

        <!-- Profile Details Update Form -->
        <form method="post" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="update_profile">

            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name"
                       class="<?= isset($errors['full_name']) ? 'input-error' : '' ?>"
                       value="<?= encode($user['full_name']) ?>" required>
                <?php if (isset($errors['full_name'])): ?>
                    <p class="field-error"><?= encode($errors['full_name']) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="phone_number">Phone Number (Optional)</label>
                <input type="text" id="phone_number" name="phone_number" placeholder="012-3456789"
                       class="<?= isset($errors['phone_number']) ? 'input-error' : '' ?>"
                       value="<?= encode($user['phone_number'] ?? '') ?>">
                <?php if (isset($errors['phone_number'])): ?>
                    <p class="field-error"><?= encode($errors['phone_number']) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-submit">Save Profile Changes</button>
        </form>

        <div class="auth-footer">
            <?php if (is_admin()): ?>
                <a href="admin_members.php">Manage Members</a> &bull;
            <?php endif; ?>
            <a href="index.php">Back to Home</a> &bull;
            <a href="logout.php">Log Out</a>
        </div>
    </div>
</div>

<?php require '_foot.php'; ?>