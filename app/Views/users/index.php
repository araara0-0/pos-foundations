<?php /** @var array $users */ ?>
<?= view('partials/header', ['title' => 'User Accounts']) ?>

<section class="page-heading">
    <p class="eyebrow">Staff directory</p>
    <h1>User Accounts</h1>
    <p>Staff records retrieved from the LumenMart database.</p>
    <div class="actions"><a class="button primary" href="/users/new">Add User</a></div>
</section>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Avatar</th>
                <th>Username</th>
                <th>Full Name</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($users === []): ?>
                <tr><td colspan="5">No staff accounts have been added yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><img class="avatar" src="/uploads/<?= esc(($user['avatar'] ?? null) ?: 'placeholder-avatar.svg') ?>" alt="<?= esc($user['full_name']) ?> avatar"></td>
                    <td><?= esc($user['username']) ?></td>
                    <td><?= esc($user['full_name']) ?></td>
                    <td><?= esc($user['role']) ?></td>
                    <td>
                        <div class="table-actions">
                            <a href="/users/<?= esc($user['id']) ?>/edit">Edit</a>
                            <?php if ((int) session()->get('user_id') !== (int) $user['id']): ?>
                                <form method="post" action="/users/<?= esc($user['id']) ?>/delete" onsubmit="return confirm('Delete this staff account?')">
                                    <?= csrf_field() ?>
                                    <button class="link-button danger" type="submit">Delete</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= view('partials/footer') ?>
