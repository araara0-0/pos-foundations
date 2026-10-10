<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;

class SaleService
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
    }

    /** @return array{success: bool, message: string} */
    public function record(int $productId, ?int $customerId, int $staffId, int $quantity): array
    {
        $this->db->transBegin();

        try {
            $product = $this->db->table('products')->where('id', $productId)->get()->getRowArray();
            if ($product === null) {
                $this->db->transRollback();
                return $this->failure('The selected product no longer exists.');
            }

            if ($customerId !== null) {
                $customerExists = $this->db->table('customers')->where('id', $customerId)->countAllResults() > 0;
                if (! $customerExists) {
                    $this->db->transRollback();
                    return $this->failure('The selected customer no longer exists.');
                }
            }

            $staffExists = $this->db->table('users')->where('id', $staffId)->countAllResults() > 0;
            if (! $staffExists) {
                $this->db->transRollback();
                return $this->failure('Your staff account is no longer available. Please log in again.');
            }

            $updated = $this->db->table('products')
                ->where('id', $productId)
                ->where('stock_quantity >=', $quantity)
                ->set('stock_quantity', 'stock_quantity - ' . $quantity, false)
                ->update();

            if (! $updated || $this->db->affectedRows() !== 1) {
                $this->db->transRollback();
                return $this->failure('Not enough stock is available for this sale.');
            }

            $inserted = $this->db->table('sales')->insert([
                'product_id' => $productId,
                'customer_id' => $customerId,
                'sold_by' => $staffId,
                'quantity' => $quantity,
                'total_price' => $this->calculateTotal((string) $product['price'], $quantity),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            if (! $inserted || ! $this->db->transCommit()) {
                $this->db->transRollback();
                return $this->failure('The sale could not be saved. No stock was changed.');
            }
        } catch (\Throwable $exception) {
            $this->db->transRollback();
            log_message('error', 'Sale recording failed: {message}', ['message' => $exception->getMessage()]);
            return $this->failure('The sale could not be saved. No stock was changed.');
        }

        return ['success' => true, 'message' => 'Sale recorded successfully.'];
    }

    private function calculateTotal(string $unitPrice, int $quantity): string
    {
        $normalized = number_format((float) $unitPrice, 2, '.', '');
        [$whole, $fraction] = explode('.', $normalized);
        $totalCents = (((int) $whole * 100) + (int) $fraction) * $quantity;

        return intdiv($totalCents, 100) . '.' . str_pad((string) ($totalCents % 100), 2, '0', STR_PAD_LEFT);
    }

    /** @return array{success: false, message: string} */
    private function failure(string $message): array
    {
        return ['success' => false, 'message' => $message];
    }
}
