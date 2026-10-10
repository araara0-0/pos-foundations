<?php

namespace App\Controllers;

use App\Models\ProductModel;

class Products extends BaseController
{
    public function index()
    {
        return view('products/index', [
            'products' => (new ProductModel())->orderBy('name')->findAll(),
        ]);
    }

    public function new()
    {
        return view('products/form', [
            'product' => ['name' => '', 'price' => '', 'stock_quantity' => 0, 'image' => null],
            'isEdit' => false,
        ]);
    }

    public function create()
    {
        $data = $this->productData();
        if (! $this->validateProduct($data)) {
            return view('products/form', ['product' => $data, 'isEdit' => false]);
        }

        $image = $this->prepareImage();
        if ($image === false) {
            return view('products/form', ['product' => $data, 'isEdit' => false]);
        }
        if ($image !== null) {
            $data['image'] = $image;
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        try {
            $saved = (new ProductModel())->insert($data);
        } catch (\Throwable $exception) {
            log_message('error', 'Product creation failed: {message}', ['message' => $exception->getMessage()]);
            $saved = false;
        }
        if ($saved === false) {
            $this->deleteImage($image);
            return redirect()->to('/products/new')->with('error', 'The product could not be saved. Please try again.');
        }

        return redirect()->to('/products')->with('success', 'Product created.');
    }

    public function edit(int $id)
    {
        $product = (new ProductModel())->find($id);
        if ($product === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $product['original_stock_quantity'] = $product['stock_quantity'];
        return view('products/form', ['product' => $product, 'isEdit' => true]);
    }

    public function update(int $id)
    {
        $model = new ProductModel();
        $product = $model->find($id);
        if ($product === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = $this->productData();
        $originalStock = filter_var($this->request->getPost('original_stock_quantity'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        if ($originalStock === false) {
            return redirect()->to('/products/' . $id . '/edit')->with('error', 'Please reload this product before saving.');
        }
        if (! $this->validateProduct($data)) {
            $data['id'] = $id;
            $data['image'] = $product['image'] ?? null;
            $data['original_stock_quantity'] = $originalStock;
            return view('products/form', ['product' => $data, 'isEdit' => true]);
        }

        $image = $this->prepareImage();
        if ($image === false) {
            $data['id'] = $id;
            $data['image'] = $product['image'] ?? null;
            $data['original_stock_quantity'] = $originalStock;
            return view('products/form', ['product' => $data, 'isEdit' => true]);
        }
        if ($image !== null) {
            $data['image'] = $image;
        }

        try {
            $db = db_connect();
            $saved = $db->table('products')
                ->where('id', $id)
                ->where('stock_quantity', $originalStock)
                ->update($data);
            $affected = $db->affectedRows();
        } catch (\Throwable $exception) {
            log_message('error', 'Product update failed: {message}', ['message' => $exception->getMessage()]);
            $saved = false;
        }

        if (! $saved) {
            $this->deleteImage($image);
            return redirect()->to('/products/' . $id . '/edit')->with('error', 'The product could not be saved. Please try again.');
        }
        if ($affected === 0) {
            $current = $model->find($id);
            if ($current === null || (int) $current['stock_quantity'] !== $originalStock) {
                $this->deleteImage($image);
                return redirect()->to('/products/' . $id . '/edit')->with('error', 'Stock changed since you opened this product. Review the latest quantity and save again.');
            }
        }

        if ($image !== null) {
            $this->deleteImage($product['image'] ?? null);
        }
        return redirect()->to('/products')->with('success', 'Product updated.');
    }

    public function delete(int $id)
    {
        $model = new ProductModel();
        $product = $model->find($id);
        if ($product === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $hasSales = db_connect()->table('sales')->where('product_id', $id)->countAllResults() > 0;
        if ($hasSales) {
            return redirect()->to('/products')->with('error', 'This product cannot be deleted because it appears in sales history.');
        }

        try {
            $deleted = $model->delete($id);
        } catch (\Throwable $exception) {
            log_message('error', 'Product deletion failed: {message}', ['message' => $exception->getMessage()]);
            $deleted = false;
        }
        if (! $deleted) {
            return redirect()->to('/products')->with('error', 'The product could not be deleted. Please try again.');
        }
        $this->deleteImage($product['image'] ?? null);

        return redirect()->to('/products')->with('success', 'Product deleted.');
    }

    private function productData(): array
    {
        return [
            'name' => trim((string) $this->request->getPost('name')),
            'price' => trim((string) $this->request->getPost('price')),
            'stock_quantity' => trim((string) $this->request->getPost('stock_quantity')),
        ];
    }

    private function validateProduct(array $data): bool
    {
        return $this->validateData($data, [
            'name' => 'required|max_length[100]',
            'price' => 'required|decimal|greater_than[0]',
            'stock_quantity' => 'required|integer|greater_than_equal_to[0]',
        ]);
    }

    private function prepareImage(): string|null|false
    {
        $upload = $this->request->getFile('image');
        if ($upload === null || $upload->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (! $upload->isValid() || $upload->getSize() > 3 * 1024 * 1024) {
            $this->validator->setError('image', 'The product image must be a JPG or PNG no larger than 3MB.');
            return false;
        }

        $imageInfo = @getimagesize($upload->getTempName());
        $mime = $imageInfo['mime'] ?? '';
        if (! in_array($mime, ['image/jpeg', 'image/png'], true)) {
            $this->validator->setError('image', 'The product image must be a JPG or PNG no larger than 3MB.');
            return false;
        }

        $directory = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'products';
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            $this->validator->setError('image', 'The product image could not be saved.');
            return false;
        }

        $filename = bin2hex(random_bytes(16)) . ($mime === 'image/png' ? '.png' : '.jpg');
        $destination = $directory . DIRECTORY_SEPARATOR . $filename;
        try {
            if (extension_loaded('gd')) {
                service('image')->withFile($upload->getTempName())->fit(640, 480, 'center')->save($destination, 85);
            } else {
                $upload->move($directory, $filename);
            }
        } catch (\Throwable) {
            if (is_file($destination)) {
                @unlink($destination);
            }
            $this->validator->setError('image', 'The product image could not be prepared.');
            return false;
        }

        return $filename;
    }

    private function deleteImage(?string $filename): void
    {
        if (! $filename) {
            return;
        }

        $path = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . basename($filename);
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
