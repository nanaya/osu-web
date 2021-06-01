<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories;

use App\Models\User;
use App\Models\UserDonation;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserDonationFactory extends Factory
{
    protected $model = UserDonation::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'target_user_id' => fn(array $attributes) => $attributes['user_id'],
            'transaction_id' => fn() => $this->transactionId(),
            'length' => 1,
            'amount' => 4,
            'cancel' => false,
        ];
    }

    public function cancelled()
    {
        return $this->state([
            'transaction_id' => fn() => "{$this->transactionId()}-cancel",
            'cancel' => true,
        ]);
    }

    private function transactionId()
    {
        return 'faked-'.time().'-'.$this->faker->randomNumber();
    }
}
