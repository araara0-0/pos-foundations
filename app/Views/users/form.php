<?php
/** @var array<string, mixed> $user */
/** @var bool $isEdit */
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
    <?php if ($isEdit): ?>
        <label>Profile picture
            <img class="avatar" src="/uploads/<?= esc(($user['avatar'] ?? null) ?: 'placeholder-avatar.svg') ?>" alt="Current avatar">
            <input type="file" name="avatar" accept="image/jpeg,image/png">
            <small>JPG or PNG, up to 2MB. It will be prepared as a 256×256 thumbnail.</small>
        </label>
    <?php endif; ?>
    <div class="actions"><button class="button primary" type="submit">Save User</button><a class="button secondary" href="/users">Cancel</a></div>
</form>

<?= view('partials/footer') ?>
