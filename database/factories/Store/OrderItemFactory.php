<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories\Store;

use App\Models\Store\Order;
use App\Models\Store\OrderItem;
use App\Models\Store\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition()
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'quantity' => 1,
            'cost' => 12.0,
        ];
    }

    public function supporterTag()
    {
        return $this->state([
            'product_id' => Product::customClass('supporter-tag')->first(),
            'cost' => 4,
            'extra_data' => function (array $attributes) {
                $user = Order::find($attributes['order_id'])->user;

                return [
                    'target_id' => (string) $user->getKey(),
                    'username' => $user->username,
                    'duration' => 1,
                ];
            },
        ]);
    }

    public function usernameChange()
    {
        return $this->state([
            'product_id' => Product::customClass('username-change')->first(),
            'cost' => 0,
            'extra_info' => 'new_username',
        ]);
    }
}
