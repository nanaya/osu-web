<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories;

use App\Models\UpdateStream;
use Illuminate\Database\Eloquent\Factories\Factory;

class UpdateStreamFactory extends Factory
{
    protected $model = UpdateStream::class;

    public function definition()
    {
        return [
            'name' => $this->faker->colorName(),
            'pretty_name' => fn(array $attributes) => $attributes['name'],
        ];
    }
}
