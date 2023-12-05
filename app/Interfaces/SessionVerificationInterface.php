<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace App\Interfaces;

interface SessionVerificationInterface
{
    public static function findForVerification(string $id): ?static;

    public function getKey();
    public function isVerified(): bool;
    public function markVerified(): void;
}
