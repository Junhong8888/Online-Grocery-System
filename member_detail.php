<?php
require '_base.php';
require 'member.php';

Member::requireAdmin($pdo);

$userId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$userId || $userId <= 0) {
    redirect('admin_members.php');
}

$memberModel = new Member($pdo);
$message     = '';
$error       = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $result = $memberModel->updateMemberRole($userId, $_POST['role'] ?? '', $_SESSION['user_id']);

    if ($result === true) {
        $message = 'Member role updated successfully.';
    } else {
        $error = $result;
    }
}

$member = $memberModel->getMemberById($userId);
if (!$member) {
    redirect('admin_members.php');
}
?>
<?php require '_head.php'; ?>

<h2>Member Details — #<?= encode($member['user_id']) ?></h2>
<p><a href="admin_members.php">&larr; Back to Member List</a></p>

<?php if ($message !== ''): ?>
    <p style="color:green;"><?= encode($message) ?></p>
<?php endif; ?>

<?php if ($error !== ''): ?>
    <p style="color:red;"><?= encode($error) ?></p>
<?php endif; ?>

<table border="1" cellpadding="10" cellspacing="0" style="width: 100%; max-width: 600px; border-collapse: collapse;">
    <?php foreach ($member as $column => $value): ?>
        <?php
        if (in_array($column, ['password_hash', 'password', 'reset_token'], true)) {
            continue;
        }
        $label = ucwords(str_replace('_', ' ', $column));
        ?>
        <tr>
            <th style="background-color: #f4f4f4; text-align: left; width: 35%;"><?= encode($label) ?></th>
            <td><?= encode($value ?? '—') ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<hr style="margin: 24px 0;">

<h3>Manage Account Role</h3>
<form method="post">
    <?= csrf_field() ?>
    <label for="role">Role:</label>
    <select id="role" name="role">
        <option value="member" <?= strtolower($member['role']) === 'member' ? 'selected' : '' ?>>Member</option>
        <option value="admin" <?= strtolower($member['role']) === 'admin' ? 'selected' : '' ?>>Admin</option>
    </select>
    <button type="submit">Update Role</button>
</form>

<?php require '_foot.php'; ?>