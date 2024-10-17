<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->unsignedBigInteger('team_id')->nullable(false);
            $table->boolean('is_new')->nullable(true)->default(true);
            $table->text('message')->nullable(true);
            $table->text('reply')->nullable(true);
            $table->timestampsTz();

            $table->unique(['user_id', 'is_new']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_applications');
    }
};
