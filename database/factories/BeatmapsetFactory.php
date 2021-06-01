<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories;

use App\Models\Beatmap;
use App\Models\BeatmapDiscussion;
use App\Models\Beatmapset;
use App\Models\Genre;
use App\Models\Language;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

class BeatmapsetFactory extends Factory
{
    protected $model = Beatmapset::class;

    public function definition()
    {
        $artist = $this->faker->name();
        $title = $this->faker->sentence(rand(0, 5));

        return [
            'creator' => fn() => $this->faker->userName(),
            'artist' => $artist,
            'title' => $title,
            'discussion_enabled' => true,
            'displaytitle' => "{$artist}|{$title}",
            'source' => fn() => $this->faker->domainWord(),
            'tags' => fn() => $this->faker->domainWord(),
            'bpm' => rand(100, 200),
            'approved' => fn() => rand(0, 1),
            'approved_date' => fn(array $attributes) => $attributes['approved'] > 0 ? now() : null,
            'play_count' => rand(0, 50000),
            'favourite_count' => rand(0, 500),
            'genre_id' => Genre::factory(),
            'language_id' => Language::factory(),
            'submit_date' => fn() => $this->faker->dateTime(),
            'thread_id' => 0,
        ];
    }

    public function deleted()
    {
        return $this->state(['deleted_at' => fn() => now()]);
    }

    public function inactive()
    {
        return $this->state(['active' => 0]);
    }

    public function noDiscussion()
    {
        return $this->state(['discussion_enabled' => false]);
    }

    public function qualified()
    {
        $approvedAt = now();

        return $this->state([
            'approved' => Beatmapset::STATES['qualified'],
            'approved_date' => $approvedAt,
            'queued_at' => $approvedAt,
        ]);
    }

    public function withDiscussion()
    {
        return $this->afterCreating(function (Beatmapset $beatmapset) {
            $beatmap = Beatmap::factory()->make();
            if (!$beatmapset->beatmaps()->save($beatmap)) {
                throw new Exception('Failed creating beatmap');
            }

            $discussion = BeatmapDiscussion::factory()->general()->make(['user_id' => $beatmapset->user_id]);
            if (!$beatmapset->beatmapDiscussions()->save($discussion)) {
                throw new Exception('Failed creating beatmap discussion');
            }
        });
    }
}
