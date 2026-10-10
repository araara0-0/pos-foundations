<?php

namespace App\Controllers;

use App\Models\ProductModel;
use CodeIgniter\HTTP\Files\UploadedFile;

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

        $image = $this->prepareImage(null);
        if ($image === false) {
            return view('products/form', ['product' => $data, 'isEdit' => false]);
        }
        if ($image !== null) {
            $data['image'] = $image;
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        (new ProductModel())->insert($data);

        return redirect()->to('/products')->with('success', 'Product created.');
    }

    public function edit(int $id)
    {
        $product = (new ProductModel())->find($id);
        if ($product === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

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
        if (! $this->validateProduct($data)) {
            $data['id'] = $id;
            $data['image'] = $product['image'] ?? null;
            return view('products/form', ['product' => $data, 'isEdit' => true]);
        }

        $image = $this->prepareImage($product['image'] ?? null);
        if ($image === false) {
            $data['id'] = $id;
            $data['image'] = $product['image'] ?? null;
            return view('products/form', ['product' => $data, 'isEdit' => true]);
        }
        if ($image !== null) {
            $data['image'] = $image;
        }

        $model->update($id, $data);
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

        $model->delete($id);
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

    /** @return string|null|false The filename, null when no image was supplied, or false on failure. */
    private function prepareImage(?string $oldImage): string|null|false
    {
        /** @var UploadedFile|null $upload */
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
            $this->validator->setError('image', 'The product image could not be prepared.');
            return false;
        }

        $this->deleteImage($oldImage);
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
