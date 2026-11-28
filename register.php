<?php
require '_base.php';
require 'member.php';

if (Member::isLoggedIn()) {
    redirect('index.php');
}

$memberModel  = new Member($pdo);
$errors       = [];
$full_name    = '';
$username     = '';
$email        = '';
$phone_number = '';
$role         = 'member';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $full_name    = trim($_POST['full_name'] ?? '');
    $username     = trim($_POST['username'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $role         = strtolower(trim($_POST['role'] ?? 'member'));

    $errors = $memberModel->validateRegistration($_POST);

    if (empty($errors)) {
        $memberModel->register($_POST);
        redirect('login.php?registered=1');
    }
}
?>
<?php require '_head.php'; ?>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Create an Account</h2>
        <p class="auth-subtitle">Join Willem Mart to start shopping fresh groceries.</p>

        <form method="post" novalidate>
            <?= csrf_field() ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" placeholder="e.g. Ali Bin Abu"
                           class="<?= isset($errors['full_name']) ? 'input-error' : '' ?>"
                           value="<?= encode($full_name) ?>" required>
                    <?php if (isset($errors['full_name'])): ?>
                        <p class="field-error"><?= encode($errors['full_name']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="e.g. ali_abu"
                           class="<?= isset($errors['username']) ? 'input-error' : '' ?>"
                           value="<?= encode($username) ?>" required>
                    <?php if (isset($errors['username'])): ?>
                        <p class="field-error"><?= encode($errors['username']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="you@example.com"
                       class="<?= isset($errors['email']) ? 'input-error' : '' ?>"
                       value="<?= encode($email) ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <p class="field-error"><?= encode($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone_number">Phone Number (Optional)</label>
                    <input type="text" id="phone_number" name="phone_number" placeholder="012-3456789"
                           class="<?= isset($errors['phone_number']) ? 'input-error' : '' ?>"
                           value="<?= encode($phone_number) ?>">
                    <?php if (isset($errors['phone_number'])): ?>
                        <p class="field-error"><?= encode($errors['phone_number']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="role">Register As</label>
                    <select id="role" name="role" onchange="toggleAdminCode()"
                            class="<?= isset($errors['role']) ? 'input-error' : '' ?>">
                        <option value="member" <?= $role === 'member' ? 'selected' : '' ?>>Member</option>
                        <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                    <?php if (isset($errors['role'])): ?>
                        <p class="field-error"><?= encode($errors['role']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div id="adminCodeGroup" class="admin-code-box" style="display: <?= $role === 'admin' ? 'block' : 'none' ?>;">
                <div class="form-group">
                    <label for="admin_code">Admin Registration Code</label>
                    <input type="password" id="admin_code" name="admin_code" placeholder="Enter secret admin code"
                           class="<?= isset($errors['admin_code']) ? 'input-error' : '' ?>">
                    <?php if (isset($errors['admin_code'])): ?>
                        <p class="field-error"><?= encode($errors['admin_code']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Min. 8 chars"
                           class="<?= isset($errors['password']) ? 'input-error' : '' ?>" required>
                    <?php if (isset($errors['password'])): ?>
                        <p class="field-error"><?= encode($errors['password']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password"
                           class="<?= isset($errors['confirm_password']) ? 'input-error' : '' ?>" required>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <p class="field-error"><?= encode($errors['confirm_password']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn-submit">Create Account</button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="login.php">Log in here</a>
        </div>
    </div>
</div>

<script>
function toggleAdminCode() {
    const roleSelect = document.getElementById('role');
    const codeGroup  = document.getElementById('adminCodeGroup');
    codeGroup.style.display = (roleSelect.value === 'admin') ? 'block' : 'none';
}
</script>

<?php require '_foot.php'; ?>