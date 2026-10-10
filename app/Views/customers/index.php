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
            <?php if ($customers === []): ?>
                <tr><td colspan="4">No customers have been added yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($customers as $customer): ?>
                <tr>
                    <td><?= esc($customer['full_name']) ?></td>
                    <td><?= esc($customer['email']) ?></td>
                    <td><?= esc($customer['phone']) ?></td>
                    <td>
                        <div class="table-actions">
                            <a href="/customers/<?= esc($customer['id']) ?>/edit">Edit</a>
                            <form method="post" action="/customers/<?= esc($customer['id']) ?>/delete" onsubmit="return confirm('Delete this customer?')">
                                <?= csrf_field() ?>
                                <button class="link-button danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= view('partials/footer') ?>
