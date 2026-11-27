<?php
require '_base.php';
require 'member.php';

Member::requireAdmin($pdo);

$memberModel = new Member($pdo);
$search      = trim($_GET['q'] ?? '');
$roleFilter  = strtolower(trim($_GET['role'] ?? ''));

$members = $memberModel->searchMembers($search, $roleFilter);
?>
<?php require '_head.php'; ?>

<h2>Admin — Member Listing &amp; Search</h2>

<form method="get" style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
    <input
        type="text"
        name="q"
        value="<?= encode($search) ?>"
        placeholder="Search by ID, username, name, email, or phone..."
        style="padding: 8px; min-width: 280px;"
    >

    <select name="role" style="padding: 8px;">
        <option value="">All Roles</option>
        <option value="member" <?= $roleFilter === 'member' ? 'selected' : '' ?>>Member</option>
        <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Admin</option>
    </select>

    <button type="submit" style="padding: 8px 16px;">Search</button>
    <?php if ($search !== '' || $roleFilter !== ''): ?>
        <a href="admin_members.php" style="padding: 8px 12px;">Reset</a>
    <?php endif; ?>
</form>

<p>Showing <strong><?= count($members) ?></strong> user(s).</p>

<?php if (empty($members)): ?>
    <p>No members matched your search.</p>
<?php else: ?>
    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f4f4f4; text-align: left;">
                <th>ID</th>
                <th>Username</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($members as $m): ?>
                <tr>
                    <td><?= encode($m['user_id']) ?></td>
                    <td><?= encode($m['username']) ?></td>
                    <td><?= encode($m['full_name']) ?></td>
                    <td><?= encode($m['email']) ?></td>
                    <td><?= encode($m['phone_number'] ?? '—') ?></td>
                    <td><strong><?= encode(ucfirst($m['role'])) ?></strong></td>
                    <td>
                        <a href="member_detail.php?id=<?= encode($m['user_id']) ?>">View Details</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require '_foot.php'; ?>