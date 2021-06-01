<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories\Store;

use App\Models\Store\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function checkout()
    {
        return $this->state(['status' => 'checkout']);
    }

    public function definition()
    {
        return [
            'user_id' => User::factory(),
        ];
    }

    public function paid()
    {
        $date = now();

        return $this->state([
            'paid_at' => $date,
            'status' => 'paid',
            'transaction_id' => "test-{$date->timestamp}",
        ]);
    }

    public function incart()
    {
        return $this->state(['status' => 'incart']);
    }

    public function processing()
    {
        return $this->state(['status' => 'processing']);
    }

    public function shipped()
    {
        return $this->state(['status' => 'shipped']);
    }

    public function shopify()
    {
        return $this->state([
            // Doesn't need to be a gid for tests.
            'transaction_id' => Order::PROVIDER_SHOPIFY.'-'.time(),
        ]);
    }
}
