<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\SaleModel;
use App\Services\SaleService;

class Sales extends BaseController
{
    public function index()
    {
        return view('sales/index', ['sales' => (new SaleModel())->history()]);
    }

    public function new()
    {
        return view('sales/form', $this->formData([
            'product_id' => '',
            'customer_id' => '',
            'quantity' => 1,
        ]));
    }

    public function create()
    {
        $sale = [
            'product_id' => trim((string) $this->request->getPost('product_id')),
            'customer_id' => trim((string) $this->request->getPost('customer_id')),
            'quantity' => trim((string) $this->request->getPost('quantity')),
        ];

        if (! $this->validateData($sale, [
            'product_id' => 'required|is_natural_no_zero',
            'customer_id' => 'permit_empty|is_natural_no_zero',
            'quantity' => 'required|is_natural_no_zero',
        ])) {
            return view('sales/form', $this->formData($sale));
        }

        $result = (new SaleService())->record(
            (int) $sale['product_id'],
            $sale['customer_id'] === '' ? null : (int) $sale['customer_id'],
            (int) session()->get('user_id'),
            (int) $sale['quantity'],
        );

        if (! $result['success']) {
            return redirect()->to('/sales/new')->withInput()->with('error', $result['message']);
        }

        return redirect()->to('/sales')->with('success', $result['message']);
    }

    private function formData(array $sale): array
    {
        return [
            'sale' => $sale,
            'products' => (new ProductModel())->orderBy('name')->findAll(),
            'customers' => (new CustomerModel())->orderBy('full_name')->findAll(),
        ];
    }
}
