<?= view('partials/header', ['title' => 'Home']) ?>

<section class="hero">
    <p class="eyebrow">LumenMart POS</p>
    <h1>Manage your store in one place</h1>
    <p>Track products and stock, manage customers and staff, and record sales.</p>
    <div class="actions">
        <a class="button primary" href="/sales/new">Record a sale</a>
        <a class="button secondary" href="/products">View products</a>
    </div>
</section>

<section class="feature-grid" aria-label="Application features">
    <article class="card">
        <h2>Products and Stock</h2>
        <p>Manage product prices, images, and available quantities.</p>
    </article>
    <article class="card">
        <h2>Customers and Staff</h2>
        <p>Keep contact details and staff accounts up to date.</p>
    </article>
    <article class="card">
        <h2>Sales History</h2>
        <p>Review past transactions and see who sold each product.</p>
    </article>
</section>

<?= view('partials/footer') ?>
