<?= view('partials/header', ['title' => 'About LumenMart']) ?>

<section class="page-heading">
    <p class="eyebrow">About the company</p>
    <h1>About LumenMart</h1>
    <p>LumenMart is a fictional neighborhood retail company focused on making everyday shopping organized, convenient, and dependable.</p>
</section>

<section class="content-card">
    <h2>About this application</h2>
    <p>The LumenMart POS foundation organizes customer and staff accounts while demonstrating the Model-View-Controller structure of CodeIgniter 4.</p>
    <p>A route matches each URL to a controller method. The controller prepares the page data and passes it to a view that renders the HTML.</p>
    <p>Customer and user records currently come from static PHP arrays. A database can replace these temporary data sources in a future version.</p>
</section>

<?= view('partials/footer') ?>
