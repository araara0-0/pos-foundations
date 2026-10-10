<?php
/** @var array<string, mixed> $sale */
/** @var array $products */
/** @var array $customers */
$errors = validation_list_errors();
$selectedProduct = (string) old('product_id', $sale['product_id'] ?? '');
$selectedCustomer = (string) old('customer_id', $sale['customer_id'] ?? '');
$quantity = old('quantity', $sale['quantity'] ?? 1);
?>
<?= view('partials/header', ['title' => 'Record Sale']) ?>

<section class="page-heading">
    <p class="eyebrow">Transactions</p>
    <h1>Record Sale</h1>
    <p>Select a product, optionally identify the customer, and enter the quantity sold.</p>
</section>

<?php if ($errors): ?><div class="alert error"><?= $errors ?></div><?php endif; ?>
<?php if ($products === []): ?>
    <div class="alert error">Add a product before recording a sale.</div>
<?php else: ?>
    <form class="form-card" method="post" action="/sales">
        <?= csrf_field() ?>
        <label>Product <span>*</span>
            <select name="product_id" required>
                <option value="">Select a product</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= esc($product['id']) ?>" <?= $selectedProduct === (string) $product['id'] ? 'selected' : '' ?> <?= (int) $product['stock_quantity'] < 1 ? 'disabled' : '' ?>>
                        <?= esc($product['name']) ?> — ₱<?= number_format((float) $product['price'], 2) ?> (<?= esc($product['stock_quantity']) ?> in stock)
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Customer
            <select name="customer_id">
                <option value="">Walk-in customer</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?= esc($customer['id']) ?>" <?= $selectedCustomer === (string) $customer['id'] ? 'selected' : '' ?>><?= esc($customer['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Quantity <span>*</span><input type="number" name="quantity" value="<?= esc($quantity) ?>" required min="1" step="1"></label>
        <div class="actions"><button class="button primary" type="submit">Record Sale</button><a class="button secondary" href="/products">View Products</a></div>
    </form>
<?php endif; ?>

<?= view('partials/footer') ?>
