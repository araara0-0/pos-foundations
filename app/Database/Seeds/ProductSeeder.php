<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            ['name' => 'C2 Green Tea Lemon 500ml', 'price' => '39.00', 'stock_quantity' => 48],
            ['name' => 'C2 Green Tea Apple 500ml', 'price' => '39.00', 'stock_quantity' => 48],
            ['name' => 'C2 Peach Black Tea 500ml', 'price' => '39.00', 'stock_quantity' => 36],
            ['name' => 'Great Taste Vanilla Latte 200ml', 'price' => '25.00', 'stock_quantity' => 36],
            ['name' => 'Great Taste Dark Latte 200ml', 'price' => '25.00', 'stock_quantity' => 36],
            ['name' => 'Vita Coco Pure Coconut Water 330ml', 'price' => '55.00', 'stock_quantity' => 24],
            ['name' => 'Goodday Friz Original 240ml', 'price' => '44.00', 'stock_quantity' => 24],
            ['name' => 'Gatorade Blue Bolt 500ml', 'price' => '51.00', 'stock_quantity' => 36],
            ['name' => 'Del Monte Mango Juice 220ml', 'price' => '49.00', 'stock_quantity' => 30],
            ['name' => 'Cloud 9 Chocolate Milk Drink 180ml', 'price' => '28.00', 'stock_quantity' => 30],
            ['name' => 'Cream-O Cookies and Cream Milk Drink 180ml', 'price' => '28.00', 'stock_quantity' => 30],
            ['name' => 'Tube Ice 2kg', 'price' => '30.00', 'stock_quantity' => 20],
            ['name' => 'Nissin Souper Meal Beef Brisket 90g', 'price' => '51.00', 'stock_quantity' => 30],
            ['name' => 'Nissin Souper Meal Hot and Spicy 85g', 'price' => '51.00', 'stock_quantity' => 30],
            ['name' => 'Payless Pancit Canton Xtra Big 128g', 'price' => '23.00', 'stock_quantity' => 60],
            ['name' => 'Otoki Cheesy Ramen Spicy Bowl 90g', 'price' => '110.00', 'stock_quantity' => 18],
            ['name' => 'Magic Flakes Cheese 28g', 'price' => '11.00', 'stock_quantity' => 72],
            ['name' => 'Ding Dong Snack Mix 95g', 'price' => '31.00', 'stock_quantity' => 48],
            ['name' => 'Regent Cheese Ring 60g', 'price' => '25.00', 'stock_quantity' => 48],
            ['name' => 'Lays Sour Cream and Onion 100g', 'price' => '120.00', 'stock_quantity' => 24],
            ['name' => 'Kimnori Teriyaki Seaweed Snack 4g', 'price' => '40.00', 'stock_quantity' => 30],
            ['name' => 'Pretzel Sticks Cheddar and Sour Cream 28g', 'price' => '11.00', 'stock_quantity' => 60],
            ['name' => 'Wookie Chocolate Chip Cookies 108g', 'price' => '64.00', 'stock_quantity' => 24],
            ['name' => 'Snack Pack Chocolate Pudding 92g', 'price' => '42.00', 'stock_quantity' => 24],
            ['name' => 'Gardenia Classic White Bread 400g', 'price' => '72.00', 'stock_quantity' => 20],
        ];

        $builder = $this->db->table('products');
        $existingNames = array_column($builder->select('name')->get()->getResultArray(), 'name');
        $createdAt = date('Y-m-d H:i:s');
        $newProducts = [];

        foreach ($products as $product) {
            if (in_array($product['name'], $existingNames, true)) {
                continue;
            }

            $newProducts[] = $product + [
                'image' => null,
                'created_at' => $createdAt,
            ];
        }

        if ($newProducts !== []) {
            $builder->insertBatch($newProducts);
        }
    }
}
