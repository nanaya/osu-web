<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace App\Models;

/**
 * @property int $count
 * @property string $name
 */
class Count extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    protected $keyType = 'string';
    protected $primaryKey = 'name';
    protected $table = 'osu_counts';

    public static function currentRankStartName(string $ruleset): string
    {
        return "pp_rank_column_{$ruleset}";
    }

    public static function totalUsers(): static
    {
        return static::firstOrCreate(['name' => 'usercount'], ['count' => 0]);
    }

    public static function lastMailUserNotificationIdSent(): static
    {
        return static::firstOrCreate(['name' => 'last_mail_user_notification_id_sent'], ['count' => 0]);
    }
}
