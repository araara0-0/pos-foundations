<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'product_id',
        'customer_id',
        'sold_by',
        'quantity',
        'total_price',
        'created_at',
    ];

    public function history(): array
    {
        return $this->db->table('sales s')
            ->select('s.id, s.quantity, s.total_price, s.created_at, p.name AS product_name, c.full_name AS customer_name, u.full_name AS staff_name')
            ->join('products p', 'p.id = s.product_id')
            ->join('customers c', 'c.id = s.customer_id', 'left')
            ->join('users u', 'u.id = s.sold_by')
            ->orderBy('s.created_at', 'DESC')
            ->orderBy('s.id', 'DESC')
            ->get()
            ->getResultArray();
    }
}
