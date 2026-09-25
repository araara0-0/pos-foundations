<?php /** @var array $users */ ?>
<?= view('partials/header', ['title' => 'User Accounts']) ?>

<section class="page-heading">
    <p class="eyebrow">Staff directory</p>
    <h1>User Accounts</h1>
    <p>Staff records retrieved from the LumenMart database.</p>
</section>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Full Name</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= esc($user['username']) ?></td>
                    <td><?= esc($user['full_name']) ?></td>
                    <td><?= esc($user['role']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= view('partials/footer') ?>
