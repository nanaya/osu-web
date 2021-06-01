<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories;

use App\Models\Spotlight;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpotlightFactory extends Factory
{
    protected $model = Spotlight::class;

    public function configure()
    {
        return $this->afterMaking(function (Spotlight $spotlight) {
            if ($spotlight->start_date !== null) {
                $spotlight->end_date ??= $spotlight->start_date->addMonths(1)->addDays(rand(0, 27));
            }
        });
    }

    public function definition()
    {
        return [
            'acronym' => 'T'.strtoupper(substr(uniqid(), 8)),
            'name' => fn() => $this->faker->realText(40),
            'start_date' => fn() => $this->faker->dateTimeBetween('-6 years'),
            'mode_specific' => true,
            'type' => 'test',
            'active' => true,
        ];
    }

    public function monthly()
    {
        $chartMonth = Carbon::instance($this->faker->dateTimeBetween('-6 years'))->startOfMonth();

        return $this->state([
            'acronym' => fn(array $attributes) => "MONTH{$attributes['chart_month']->format('ym')}",
            'name' => fn(array $attributes) => "Spotlight {$attributes['chart_month']->format('F Y')}",
            'start_date' => fn(array $attributes) => $attributes['chart_month']->copy()->addMonths(1)->addDays(rand(0, 27)),
            'mode_specific' => true,
            'type' => 'monthly',
            'active' => true,
            'chart_month' => $chartMonth,
        ]);
    }

    public function bestof()
    {
        $chartMonth = Carbon::instance($this->faker->dateTimeBetween('-6 years'))->endOfYear();

        return [
            'acronym' => fn(array $attributes) => "BEST{$attributes['chart_month']->format('Y')}",
            'name' => fn(array $attributes) => "Best of {$attributes['chart_month']->format('Y')}",
            'start_date' => fn(array $attributes) => $attributes['chart_month']->copy()->addMonths(1)->addDays(rand(0, 27)),
            'mode_specific' => true,
            'type' => 'bestof',
            'active' => true,
            'chart_month' => $chartMonth,
        ];
    }
}
