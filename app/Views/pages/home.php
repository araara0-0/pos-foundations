<?= view('partials/header', ['title' => 'Home']) ?>

<section class="hero">
    <p class="eyebrow">LumenMart Point of Sale</p>
    <h1>Store account management made simple</h1>
    <p>LumenMart uses this application to organize customer and staff account information in one clear interface.</p>
    <div class="actions">
        <a class="button primary" href="/customers">View customers</a>
        <a class="button secondary" href="/users">View users</a>
    </div>
</section>

<section class="feature-grid" aria-label="Application features">
    <article class="card">
        <h2>Customer Accounts</h2>
        <p>Review customer names, email addresses, and phone numbers.</p>
    </article>
    <article class="card">
        <h2>User Accounts</h2>
        <p>Review staff usernames, full names, and assigned roles.</p>
    </article>
    <article class="card">
        <h2>CodeIgniter MVC</h2>
        <p>Routes, controllers, and views keep LumenMart's application organized.</p>
    </article>
</section>

<?= view('partials/footer') ?>
