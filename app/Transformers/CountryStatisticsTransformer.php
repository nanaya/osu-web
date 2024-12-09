<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace App\Transformers;

use App\Models\CountryStatistics;

class CountryStatisticsTransformer extends TransformerAbstract
{
    protected array $availableIncludes = ['country'];

    public function transform(CountryStatistics $stat)
    {
        return [
            'code' => $stat->country_code,
            'active_users' => $stat->user_count,
            'play_count' => $stat->play_count,
            'ranked_score' => $stat->ranked_score,
            'performance' => $stat->performance,
        ];
    }

    public function includeCountry(CountryStatistics $stat)
    {
        $country = app('countries')->byCode($stat->country_code);

        return $country === null
            ? $this->primitive(null)
            : $this->item($country, new CountryTransformer());
    }
}
