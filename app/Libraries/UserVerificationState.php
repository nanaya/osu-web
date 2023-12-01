<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace App\Libraries;

use App\Events\UserSessionEvent;
use App\Exceptions\UserVerificationException;
use App\Interfaces\SessionVerificationInterface;
use App\Models\User;

class UserVerificationState
{
    private const KEY_VALID_DURATION = 5 * 3600;

    private false|null|\stdClass $data = false;
    private string $dataKey;

    private function __construct(private ?User $user, private SessionVerificationInterface $session)
    {
        $this->dataKey = 'user_verification:'.$this->session::class.':'.$this->session->getKey();
    }

    public static function fromCurrentRequest(): static
    {
        return new static(\Auth::user(), \Session::instance());
    }

    public static function fromVerifyLink($linkKey): ?static
    {
        if (!SignedRandomString::isValid($linkKey)) {
            return null;
        }

        $params = \Cache::get("verification:{$linkKey}");

        if ($params === null) {
            return null;
        }

        $state = static::load($params);

        // As it's from verify link, make sure the state is waiting for verification.
        return $state->issued() ? $state : null;
    }

    public static function load($params): static
    {
        $params['sessionClass'] ??= Session\Store::class;
        $session = $params['sessionClass']::findForVerification($params['sessionId']);

        return new static(User::find($params['userId']), $session);
    }

    public function data(): ?\stdClass
    {
        if ($this->data === false) {
            $this->data = \Cache::get($this->dataKey);
        }

        return $this->data;
    }

    public function issue()
    {
        $this->reset();
        $data = $this->setDefaultData();
        $this->save();

        return [
            'link' => $data->linkKey,
            'main' => $data->key,
        ];
    }

    public function issued(): bool
    {
        return $this->data() !== null;
    }

    public function isDone(): bool
    {
        return $this->user === null
            ? true
            : $this->session->isVerified();
    }

    public function markVerified(): void
    {
        $this->reset();
        $this->session->markVerified();

        UserSessionEvent::newVerified($this->user->getKey(), $this->session->getKey())->broadcast();
    }

    public function verify($inputKey): void
    {
        if ($this->isDone()) {
            return;
        }

        $data = $this->data();

        if ($data === null) {
            throw new UserVerificationException('expired', true);
        }

        if ($data->tries > config('osu.user.verification_key_tries_limit')) {
            throw new UserVerificationException('retries_exceeded', true);
        }

        if (!hash_equals($data->key, $inputKey)) {
            $data->tries++;
            $this->save();

            throw new UserVerificationException('incorrect_key', false);
        }
    }

    private function reset(): void
    {
        if (($data = $this->data()) !== null) {
            \Cache::forget("verification:{$data->linkKey}");
        }
        \Cache::forget($this->dataKey);
        $this->data = null;
    }

    private function save(): void
    {
        $data = $this->data();

        $duration = max(0, $data->expiresAt - time());

        \Cache::put("verification:{$data->linkKey}", [
            'userId' => $this->user->getKey(),
            'sessionId' => $this->session->getKey(),
            'sessionClass' => $this->session::class,
        ], $duration);

        \Cache::put($this->dataKey, $data, $duration);
    }

    private function setDefaultData(): \stdClass
    {
        return $this->data = (object) [
            'expiresAt' => time() + static::KEY_VALID_DURATION,
            // 1 byte = 2^8 bits = 16^2 bits = 2 hex characters
            'key' => bin2hex(random_bytes(config('osu.user.verification_key_length_hex') / 2)),
            'linkKey' => SignedRandomString::create(32),
            'tries' => 0,
        ];
    }
}
