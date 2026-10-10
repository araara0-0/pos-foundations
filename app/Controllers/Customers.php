<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\CustomerModel;

class Customers extends BaseController
{
    public function index()
    {
        $customerModel = new CustomerModel();
        $customers = $customerModel->findAll();
        return view('customers/index', ['customers' => $customers]);
    }

    public function new()
    {
        return view('customers/form', [
            'customer' => ['full_name' => '', 'email' => '', 'phone' => ''],
            'isEdit' => false,
        ]);
    }

    public function create()
    {
        $model = new CustomerModel();
        $data = $this->customerData();

        if (! $this->validateData($data, [
            'full_name' => 'required|max_length[100]',
            'email' => 'required|valid_email|max_length[100]',
            'phone' => 'permit_empty|max_length[20]',
        ])) {
            return view('customers/form', ['customer' => $data, 'isEdit' => false]);
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $model->insert($data);

        return redirect()->to('/customers')->with('success', 'Customer account created.');
    }

    public function edit(int $id)
    {
        $customer = (new CustomerModel())->find($id);
        if ($customer === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('customers/form', ['customer' => $customer, 'isEdit' => true]);
    }

    public function update(int $id)
    {
        $model = new CustomerModel();
        $customer = $model->find($id);
        if ($customer === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = $this->customerData();
        if (! $this->validateData($data, [
            'full_name' => 'required|max_length[100]',
            'email' => 'required|valid_email|max_length[100]',
            'phone' => 'permit_empty|max_length[20]',
        ])) {
            $data['id'] = $id;
            return view('customers/form', ['customer' => $data, 'isEdit' => true]);
        }

        $model->update($id, $data);
        return redirect()->to('/customers')->with('success', 'Customer account updated.');
    }

    public function delete(int $id)
    {
        $model = new CustomerModel();
        if ($model->find($id) === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $hasSales = db_connect()->table('sales')->where('customer_id', $id)->countAllResults() > 0;
        if ($hasSales) {
            return redirect()->to('/customers')->with('error', 'This customer cannot be deleted because they appear in sales history.');
        }

        try {
            $deleted = $model->delete($id);
        } catch (\Throwable $exception) {
            log_message('error', 'Customer deletion failed: {message}', ['message' => $exception->getMessage()]);
            $deleted = false;
        }
        if (! $deleted) {
            return redirect()->to('/customers')->with('error', 'The customer could not be deleted. Please try again.');
        }
        return redirect()->to('/customers')->with('success', 'Customer account deleted.');
    }

    private function customerData(): array
    {
        return [
            'full_name' => trim((string) $this->request->getPost('full_name')),
            'email' => trim((string) $this->request->getPost('email')),
            'phone' => trim((string) $this->request->getPost('phone')),
        ];
    }
}
