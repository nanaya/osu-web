<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories;

use App\Models\Country;
use App\Models\User;
use App\Models\UserAccountHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    private static function defaultPassword()
    {
        static $password;

        return $password ??= password_hash(md5('password'), PASSWORD_BCRYPT);
    }

    protected $model = User::class;

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            $user->addToGroup(app('groups')->byId($user->group_id));
        });
    }

    public function definition()
    {
        // Get or create a random country
        $countryAcronym = fn() => Country::inRandomOrder()->first() ?? Country::factory()->create();

        return [
            'username' => fn() => substr(str_replace('.', ' ', $this->faker->userName()), 0, 15),
            'user_password' => static::defaultPassword(),
            'user_email' => fn() => $this->faker->safeEmail(),
            'group_id' => fn() => app('groups')->byIdentifier('default'),
            'user_lastvisit' => time(),
            'user_posts' => rand(1, 500),
            'user_warnings' => 0,
            'user_type' => 0,
            'osu_kudosavailable' => rand(1, 500),
            'osu_kudosdenied' => rand(1, 500),
            'osu_kudostotal' => rand(1, 500),
            'country_acronym' => $countryAcronym,
            'osu_playstyle' => [array_rand(User::PLAYSTYLES)],
            'user_website' => 'http://www.google.com/',
            'user_twitter' => 'ppy',
            'user_permissions' => '',
            'user_interests' => fn() => substr($this->faker->bs(), 30),
            'user_occ' => fn() => substr($this->faker->catchPhrase(), 30),
            'user_sig' => fn() => $this->faker->realText(155),
            'user_from' => fn() => substr($this->faker->country(), 30),
            'user_regdate' => fn() => $this->faker->dateTimeBetween('-6 years'),
        ];
    }

    public function restricted()
    {
        return $this->state([
            'user_warnings' => 1,
        ])->afterCreating(function (User $user) {
            $history = UserAccountHistory::factory()->restriction()->make();
            $user->accountHistories()->save($history);
        });
    }

    public function silenced()
    {
        return $this->afterCreating(function (User $user) {
            $history = UserAccountHistory::factory()->silence()->make();
            $user->accountHistories()->save($history);
        });
    }

    public function withGroup(?string $groupIdentifier)
    {
        if ($groupIdentifier === null) {
            return $this;
        }

        $group = app('groups')->byIdentifier($groupIdentifier);

        return $this->state(['group_id' => $group])->afterCreating(function (User $user) use ($group) {
            $user->addToGroup($group);
        });
    }

    public function withNote()
    {
        return $this->afterCreating(function (User $user) {
            $history = UserAccountHistory::factory()->note()->make();
            $user->accountHistories()->save($history);
        });
    }
}
