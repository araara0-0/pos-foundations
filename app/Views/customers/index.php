<?php /** @var array $customers */ ?>
<?= view('partials/header', ['title' => 'Customer Accounts']) ?>

<section class="page-heading">
    <p class="eyebrow">Account directory</p>
    <h1>Customer Accounts</h1>
    <p>Customer records retrieved from the LumenMart database.</p>
    <div class="actions"><a class="button primary" href="/customers/new">Add Customer</a></div>
</section>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customer): ?>
                <tr>
                    <td><?= esc($customer['full_name']) ?></td>
                    <td><?= esc($customer['email']) ?></td>
                    <td><?= esc($customer['phone']) ?></td>
                    <td><a href="/customers/<?= esc($customer['id']) ?>/edit">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= view('partials/footer') ?>
