<?php
$errors = validation_list_errors();
$heading = $isEdit ? 'Edit User' : 'New User';
?>
<?= view('partials/header', ['title' => $heading]) ?>

<section class="page-heading">
    <p class="eyebrow">Staff accounts</p>
    <h1><?= esc($heading) ?></h1>
</section>

<?php if ($errors): ?><div class="alert error"><?= $errors ?></div><?php endif; ?>
<form class="form-card" method="post" enctype="multipart/form-data" action="<?= $isEdit ? '/users/' . esc($user['id']) : '/users' ?>">
    <?= csrf_field() ?>
    <label>Username <span>*</span><input type="text" name="username" value="<?= esc($user['username'] ?? '') ?>" required maxlength="50"></label>
    <label>Full name <span>*</span><input type="text" name="full_name" value="<?= esc($user['full_name'] ?? '') ?>" required maxlength="100"></label>
    <label>Role <input type="text" name="role" value="<?= esc($user['role'] ?? 'Staff') ?>" maxlength="50"></label>
    <label>Password <?= $isEdit ? '' : '<span>*</span>' ?>
        <input type="password" name="password" <?= $isEdit ? '' : 'required' ?> minlength="8" maxlength="72" autocomplete="new-password">
        <small><?= $isEdit ? 'Leave blank to keep the current password.' : 'Use 8 to 72 characters.' ?></small>
    </label>
    <label>Profile picture
        <?php if ($isEdit): ?>
            <img class="avatar" src="/uploads/<?= esc(($user['avatar'] ?? null) ?: 'placeholder-avatar.svg') ?>" alt="Current avatar">
        <?php endif; ?>
        <input type="file" name="avatar" accept="image/jpeg,image/png">
        <small>JPG or PNG, up to 2MB.</small>
    </label>
    <div class="actions"><button class="button primary" type="submit">Save User</button><a class="button secondary" href="/users">Cancel</a></div>
</form>

<?= view('partials/footer') ?>
