<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories\Store;

use App\Models\Store\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function childBanners()
    {
        return $this->state([
            'cost' => 5.00,
            'weight' => null,
            'stock' => null,
            'base_shipping' => 0.00,
            'next_shipping' => 0.00,
            'max_quantity' => 1,
            'display_order' => -10,
            'custom_class' => 'mwc7-supporter',
        ]);
    }

    public function childTshirt()
    {
        return $this->state([
            'name' => fn() => "osu! t-shirt (triangles) / {$this->faker->colorName}",
            'cost' => 16.00,
            'weight' => 100,
            'base_shipping' => 5.00,
            'next_shipping' => 4.00,
            'stock' => rand(1, 100),
            'max_quantity' => 5,
        ]);
    }

    public function definition()
    {
        return [
            'name' => fn() => "Imagination / {$this->faker->colorName}",
            'cost' => 16.00,
            'weight' => 100,
            'base_shipping' => 5.00,
            'next_shipping' => 4.00,
            'stock' => rand(1, 100),
            'max_quantity' => 1,
        ];
    }

    public function disabled()
    {
        return $this->state(['enabled' => false]);
    }

    public function masterTshirt()
    {
        return $this->state([
            'name' => fn() => "osu! t-shirt (triangles) / {$this->faker->colorName}",
            'cost' => 16.00,
            'weight' => 100,
            'base_shipping' => 5.00,
            'next_shipping' => 4.00,
            'stock' => rand(1, 100),
            'max_quantity' => 5,
            'header_description' => '# osu! t-shirt swag',
            'promoted' => 1,
            'description' => <<<EOF
Brand new osu! t-shirts have arrived! Featuring a tasty triangle design by osu! designer flyte, it's a welcome addition to any avid osu! player’s wardrobe.

* 100% cotton
* Medium weight, pre-shrunk
* Sizes: S, M, L, XL

```
Size             S    M    L    XL
Garment Length  66cm 70cm 74cm 78cm
Body width      49cm 52cm 55cm 58cm
Shoulder width  44cm 47cm 50cm 53cm
Sleeve length   19cm 20cm 22cm 24cm
```

NOTE: These are Japanese sizes. Overseas customers are advised to check the size chart above!
EOF,
            'header_image' => 'https://puu.sh/hzgoB/1142f14e8b.jpg',
            'images_json' => json_encode([
                ['https://puu.sh/hxpsp/d0b8704769.jpg', 'https://puu.sh/hxpsp/d0b8704769.jpg'],
                ['https://puu.sh/hxptO/71121e05e7.jpg', 'https://puu.sh/hxptO/71121e05e7.jpg'],
                ['https://puu.sh/hzfUF/1b9af4dbd1.jpg', 'https://puu.sh/hzfUF/1b9af4dbd1.jpg'],
            ]),
        ]);
    }
}
