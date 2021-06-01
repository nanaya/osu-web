<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Seeders\ModelSeeders;

use App\Models\Country;
use App\Models\Store\Product;
use App\Models\Tournament;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedProducts();
        $this->seedBanners();
    }

    public function seedProducts()
    {
        $this->product_ids = [];
        $this->count = 0;

        $masterTshirt = Product::factory()->masterTshirt()->create();
        $childShirts = Product::factory()->childTshirt()->count(7)->create([
            'master_product_id' => $masterTshirt->product_id,
        ])->each(function ($s) {
            $this->product_ids[] = $s->product_id;
            $this->count++;
        });

        // Add the child shirt IDs to the master shirt's type_mappings_json
        $typeMappingsJson = [];
        $sizes = ['S', 'M', 'L', 'XL'];

        $typeMappingsJson[$masterTshirt->product_id] = [
            'size' => 'S',
            'colour' => 'White',
        ];

        $i = 1;
        foreach ($this->product_ids as $id) {
            if ($i < 4) {
                $colour = 'White';
            } else {
                $colour = 'Charcoal';
            }
            $typeMappingsJson[$id] = [
                'size' => $sizes[$i % 4],
                'colour' => $colour,
            ];
            $i++;
        }
        $masterTshirt->type_mappings_json = json_encode($typeMappingsJson, JSON_PRETTY_PRINT);
        $masterTshirt->save();
    }

    public function seedBanners()
    {
        $tournament = Tournament::factory()->create();
        // Get some countries to use.
        $countries = Country::limit(6)->get()->toArray();
        $masterCountry = array_shift($countries);

        $master = Product::factory()->childBanners()->create([
            'name' => "{$tournament->name} Support Banner ({$masterCountry['name']})",
            'description' => ':)',
            'header_description' => "# {$tournament->name} Support Banners\nYayifications",
            'promoted' => true,
            'display_order' => 0,
        ]);

        $typeMappingsJson = [
            $master->product_id => [
                'country' => $masterCountry['name'],
                'tournament_id' => $tournament->tournament_id,
            ],
        ];

        foreach ($countries as $country) {
            $product = Product::factory()->childBanners()->create([
                'name' => "{$tournament->name} Support Banner ({$country['name']})",
                'master_product_id' => $master->product_id,
            ]);

            $typeMappingsJson[$product->product_id] = [
                'country' => $country['name'],
                'tournament_id' => $tournament->tournament_id,
            ];
        }

        $master->type_mappings_json = json_encode($typeMappingsJson, JSON_PRETTY_PRINT);
        $master->saveOrExplode();
    }
}
