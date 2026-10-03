<?php
/** @var array<string, mixed> $customer */
/** @var bool $isEdit */
$errors = validation_list_errors();
$heading = $isEdit ? 'Edit Customer' : 'New Customer';
?>
<?= view('partials/header', ['title' => $heading]) ?>

<section class="page-heading">
    <p class="eyebrow">Customer accounts</p>
    <h1><?= esc($heading) ?></h1>
</section>

<?php if ($errors): ?><div class="alert error"><?= $errors ?></div><?php endif; ?>
<form class="form-card" method="post" action="<?= $isEdit ? '/customers/' . esc($customer['id']) : '/customers' ?>">
    <?= csrf_field() ?>
    <label>Full name <span>*</span><input type="text" name="full_name" value="<?= esc($customer['full_name'] ?? '') ?>" required maxlength="100"></label>
    <label>Email <span>*</span><input type="email" name="email" value="<?= esc($customer['email'] ?? '') ?>" required maxlength="100"></label>
    <label>Phone <input type="text" name="phone" value="<?= esc($customer['phone'] ?? '') ?>" maxlength="20"></label>
    <div class="actions"><button class="button primary" type="submit">Save Customer</button><a class="button secondary" href="/customers">Cancel</a></div>
</form>

<?= view('partials/footer') ?>
