<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace App\Models;

class DeletedUser extends User
{
    public null $country_acronym = null;
    public $user_avatar = null;
    public $username = '[deleted user]';

    public function trashed()
    {
        return true;
    }
}
