<?php
/** @var array<string, mixed> $product */
/** @var bool $isEdit */
$errors = validation_list_errors();
$heading = $isEdit ? 'Edit Product' : 'New Product';
?>
<?= view('partials/header', ['title' => $heading]) ?>

<section class="page-heading">
    <p class="eyebrow">Inventory</p>
    <h1><?= esc($heading) ?></h1>
</section>

<?php if ($errors): ?><div class="alert error"><?= $errors ?></div><?php endif; ?>
<form class="form-card" method="post" enctype="multipart/form-data" action="<?= $isEdit ? '/products/' . esc($product['id']) : '/products' ?>">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="original_stock_quantity" value="<?= esc($product['original_stock_quantity'] ?? $product['stock_quantity']) ?>"><?php endif; ?>
    <label>Product name <span>*</span><input type="text" name="name" value="<?= esc($product['name'] ?? '') ?>" required maxlength="100"></label>
    <label>Price <span>*</span><input type="number" name="price" value="<?= esc($product['price'] ?? '') ?>" required min="0.01" step="0.01"></label>
    <label>Stock quantity <span>*</span><input type="number" name="stock_quantity" value="<?= esc($product['stock_quantity'] ?? 0) ?>" required min="0" step="1"></label>
    <label>Product image
        <?php if ($isEdit): ?>
            <img class="product-preview" src="/uploads/products/<?= esc(($product['image'] ?? null) ?: 'placeholder-product.svg') ?>" alt="Current product image">
        <?php endif; ?>
        <input type="file" name="image" accept="image/jpeg,image/png">
        <small>JPG or PNG, up to 3MB. Images are prepared at 640 × 480 when image processing is available.</small>
    </label>
    <div class="actions"><button class="button primary" type="submit">Save Product</button><a class="button secondary" href="/products">Cancel</a></div>
</form>

<?= view('partials/footer') ?>
