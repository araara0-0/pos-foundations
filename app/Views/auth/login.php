<?= view('partials/header', ['title' => 'Log In']) ?>

<section class="page-heading">
    <p class="eyebrow">LumenMart POS</p>
    <h1>Log In</h1>
    <p>Sign in to manage products, customers, staff, and sales.</p>
</section>

<?php if ($message = session()->getFlashdata('error')): ?>
    <div class="alert error"><?= esc($message) ?></div>
<?php endif; ?>

<form class="form-card" method="post" action="/login">
    <?= csrf_field() ?>
    <label>Username <input type="text" name="username" value="<?= esc(session()->getFlashdata('username') ?? '') ?>" required autocomplete="username"></label>
    <label>Password <input type="password" name="password" required autocomplete="current-password"></label>
    <div class="actions"><button class="button primary" type="submit">Log In</button></div>
</form>

<?= view('partials/footer') ?>
