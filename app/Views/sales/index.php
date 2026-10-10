<?= view('partials/header', ['title' => 'Sales History']) ?>

<section class="page-heading">
    <p class="eyebrow">Transactions</p>
    <h1>Sales History</h1>
    <p>Review completed transactions and the staff members who recorded them.</p>
    <div class="actions"><a class="button primary" href="/sales/new">Record Sale</a></div>
</section>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Customer</th>
                <th>Staff</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($sales === []): ?>
                <tr><td colspan="6">No sales have been recorded yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($sales as $sale): ?>
                <tr>
                    <td><?= esc($sale['product_name']) ?></td>
                    <td><?= esc($sale['customer_name'] ?? 'Walk-in customer') ?></td>
                    <td><?= esc($sale['staff_name']) ?></td>
                    <td><?= esc($sale['quantity']) ?></td>
                    <td>₱<?= number_format((float) $sale['total_price'], 2) ?></td>
                    <td><time datetime="<?= esc($sale['created_at']) ?>"><?= esc(date('M j, Y g:i A', strtotime($sale['created_at']))) ?></time></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= view('partials/footer') ?>
