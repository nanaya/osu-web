<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace Tests\Libraries;

use App\Interfaces\SessionVerificationInterface;
use App\Libraries\Session\Store as SessionStore;
use App\Libraries\UserVerification;
use App\Libraries\UserVerificationState;
use App\Models\LoginAttempt;
use App\Models\User;
use Tests\TestCase;

class UserVerificationTest extends TestCase
{
    private static function getVerificationState(SessionVerificationInterface $session, User $user): UserVerificationState
    {
        return UserVerificationState::load([
            'sessionClass' => $session::class,
            'sessionId' => $session->getKey(),
            'userId' => $user->getKey(),
        ]);
    }

    public function testIssue()
    {
        $user = User::factory()->create();

        $this
            ->be($user)
            ->get(route('account.edit'))
            ->assertStatus(401)
            ->assertViewIs('users.verify');

        $record = LoginAttempt::find('127.0.0.1');

        $this->assertTrue($record->containsUser($user, 'verify'));
        $this->assertFalse(UserVerification::fromCurrentRequest()->isDone());
    }

    public function testVerify()
    {
        $user = User::factory()->create();
        $session = \Session::instance();
        $sessionId = $session->getId();

        $this
            ->be($user)
            ->withPersistentSession($session)
            ->get(route('account.edit'))
            ->assertStatus(401)
            ->assertViewIs('users.verify');

        $key = static::getVerificationState($session, $user)->data()->key;

        $this
            ->withPersistentSession($session)
            ->post(route('account.verify'), ['verification_key' => $key])
            ->assertSuccessful();

        $record = LoginAttempt::find('127.0.0.1');

        $this->assertFalse($record->containsUser($user, 'verify-mismatch:'));
        $this->assertTrue(UserVerification::fromCurrentRequest()->isDone());
    }

    public function testVerifyLink(): void
    {
        $user = User::factory()->create();
        $session = \Session::instance();
        $sessionId = $session->getId();

        $this
            ->be($user)
            ->withPersistentSession($session)
            ->get(route('account.edit'))
            ->assertStatus(401)
            ->assertViewIs('users.verify');

        $linkKey = static::getVerificationState($session, $user)->data()->linkKey;

        $guestSession = SessionStore::findOrCreate();
        $this
            ->withPersistentSession($guestSession)
            ->get(route('account.verify', ['key' => $linkKey]))
            ->assertSuccessful();

        $record = LoginAttempt::find('127.0.0.1');

        $this->assertFalse($record->containsUser($user, 'verify-mismatch:'));
        $this->assertTrue(UserVerificationState::load(['userId' => $user->getKey(), 'sessionId' => $sessionId])->isDone());
    }

    public function testVerifyLinkMismatch(): void
    {
        $user = User::factory()->create();
        $session = \Session::instance();
        $sessionId = $session->getId();

        $this
            ->be($user)
            ->withPersistentSession($session)
            ->get(route('account.edit'))
            ->assertStatus(401)
            ->assertViewIs('users.verify');

        $guestSession = SessionStore::findOrCreate();
        $this
            ->withPersistentSession($guestSession)
            ->get(route('account.verify', ['key' => 'invalid']))
            ->assertStatus(404);

        $this->assertFalse(UserVerificationState::load(['userId' => $user->getKey(), 'sessionId' => $sessionId])->isDone());
    }

    public function testVerifyMismatch()
    {
        $user = User::factory()->create();
        $session = \Session::instance();

        $this
            ->be($user)
            ->withPersistentSession($session)
            ->get(route('account.edit'))
            ->assertStatus(401)
            ->assertViewIs('users.verify');

        $record = LoginAttempt::find('127.0.0.1');
        $this->assertFalse($record->containsUser($user, 'verify-mismatch:'));

        $this
            ->withPersistentSession($session)
            ->post(route('account.verify'), ['verification_key' => 'invalid'])
            ->assertStatus(422);

        $record = LoginAttempt::find('127.0.0.1');

        $this->assertTrue($record->containsUser($user, 'verify-mismatch:'));
        $this->assertFalse(UserVerification::fromCurrentRequest()->isDone());
    }
}
