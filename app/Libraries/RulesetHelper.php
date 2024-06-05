<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace App\Libraries;

final class RulesetHelper
{
    const NAME_TO_IDS = [
        'osu' => 0,
        'taiko' => 1,
        'fruits' => 2,
        'mania' => 3,
    ];

    const VARIANTS = [
        'mania' => [
            '4k' => '4k',
            '7k' => '7k',
        ],
    ];

    public static function idToNames(): array
    {
        static $names = array_flip(self::NAME_TO_IDS);

        return $names;
    }

    public static function toId(?string $rulesetName): ?int
    {
        if ($rulesetName === 'catch') {
            return self::NAME_TO_IDS['fruits'];
        }

        return self::NAME_TO_IDS[$rulesetName] ?? null;
    }

    public static function toName(?int $rulesetId): ?string
    {
        return self::idToNames()[$rulesetId] ?? null;
    }

    public static function validIdOrNull(?int $rulesetId): ?int
    {
        return self::isValidId($rulesetId) ? $rulesetId : null;
    }

    public static function validNameOrNull(?string $rulesetName): ?string
    {
        return self::isValidName($rulesetName) ? $rulesetName : null;
    }

    public static function isValidId(?int $rulesetId): bool
    {
        return array_key_exists($rulesetId, self::idToNames());
    }

    public static function isValidName(?string $rulesetName): bool
    {
        return array_key_exists($rulesetName, self::NAME_TO_IDS);
    }

    public static function isValidVariant(?string $rulesetName, ?string $variant): bool
    {
        return $variant === null || isset(self::VARIANTS[$rulesetName][$variant]);
    }
}
