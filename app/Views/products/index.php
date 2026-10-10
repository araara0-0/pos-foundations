<?php /** @var array $products */ ?>
<?= view('partials/header', ['title' => 'Products']) ?>

<section class="page-heading">
    <p class="eyebrow">Inventory</p>
    <h1>Products</h1>
    <p>Manage product prices, stock quantities, and display images.</p>
    <div class="actions"><a class="button primary" href="/products/new">Add Product</a></div>
</section>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($products === []): ?>
                <tr><td colspan="5">No products have been added yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><img class="product-thumb" src="/uploads/products/<?= esc(($product['image'] ?? null) ?: 'placeholder-product.svg') ?>" alt="<?= esc($product['name']) ?>"></td>
                    <td><?= esc($product['name']) ?></td>
                    <td>₱<?= number_format((float) $product['price'], 2) ?></td>
                    <td><?= esc($product['stock_quantity']) ?></td>
                    <td>
                        <div class="table-actions">
                            <a href="/products/<?= esc($product['id']) ?>/edit">Edit</a>
                            <form method="post" action="/products/<?= esc($product['id']) ?>/delete" onsubmit="return confirm('Delete this product?')">
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
