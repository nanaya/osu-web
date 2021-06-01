<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories\UserStatistics\Spotlight;

use Database\Factories\UserStatistics\ModelFactory as BaseFactory;

abstract class ModelFactory extends BaseFactory
{
    public function definition()
    {
        $definition = parent::definition();

        unset($definition['accuracy_new']);
        unset($definition['total_seconds_played']);
        unset($definition['xh_rank_count']);
        unset($definition['sh_rank_count']);
        unset($definition['rank_score']);
        unset($definition['rank_score_index']);

        return $definition;
    }
}
