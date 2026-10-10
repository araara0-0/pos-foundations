<?= view('partials/header', ['title' => 'About LumenMart']) ?>

<section class="page-heading">
    <p class="eyebrow">About the company</p>
    <h1>About LumenMart</h1>
    <p>LumenMart is a fictional neighborhood retail company focused on making everyday shopping organized, convenient, and dependable.</p>
</section>

<section class="content-card">
    <h2>About this application</h2>
    <p>LumenMart POS manages products, customers, staff accounts, and sales in a CodeIgniter 4 application.</p>
    <p>A route matches each URL to a controller method. The controller prepares the page data and passes it to a view that renders the HTML.</p>
    <p>Product, customer, staff, and sales records are stored in the LumenMart database. Recording a sale saves the transaction and reduces the product's stock.</p>
</section>

<?= view('partials/footer') ?>
