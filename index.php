<?php
require '_base.php';
?>
<?php require '_head.php'; ?>

<div class="auth-wrapper">
    <div class="auth-card" style="max-width: 680px; text-align: center;">
        <?php if (is_logged_in()): ?>
            <h2>Welcome back, <?= encode($_SESSION['full_name'] ?? $_SESSION['username']) ?>!</h2>
            <p class="auth-subtitle">
                Signed in as <strong>@<?= encode($_SESSION['username']) ?></strong>
                (<?= encode(ucfirst($_SESSION['role'])) ?>)
            </p>

            <div style="display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; margin-top: 20px;">
                <?php if (is_admin()): ?>
                    <a href="admin_members.php" class="btn-submit" style="width: auto; padding: 10px 22px; text-decoration: none;">
                        Manage Members
                    </a>
                <?php endif; ?>
                <a href="profile.php" class="btn-submit" style="width: auto; padding: 10px 22px; text-decoration: none; background-color: #0f172a;">
                    My Profile
                </a>
            </div>
        <?php else: ?>
            <h2>Welcome to Willem Mart</h2>
            <p class="auth-subtitle">Fresh groceries delivered to your doorstep.</p>

            <div style="display: flex; justify-content: center; gap: 12px; margin-top: 20px;">
                <a href="login.php" class="btn-submit" style="width: auto; padding: 10px 24px; text-decoration: none;">
                    Log In
                </a>
                <a href="register.php" class="btn-submit" style="width: auto; padding: 10px 24px; text-decoration: none; background-color: #0f172a;">
                    Register
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require '_foot.php'; ?>