<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace App\Libraries;

use App\Events\UserSessionEvent;
use App\Exceptions\UserVerificationException;
use App\Libraries\Session\Store;
use App\Models\User;

class UserVerificationState
{
    private const KEY_VALID_DURATION = 5 * 3600;
    private const LINK_KEY_SIZE = 32;

    public static function fromCurrentRequest(): static
    {
        return new static(\Auth::user(), \Session::instance());
    }

    public static function fromVerifyLink($linkKey): ?static
    {
        if (!static::verifyLinkKey($linkKey)) {
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

    public static function load($params)
    {
        $session = (new SessionManager(app()))->instance();
        $session->setId($params['sessionId']);
        $session->start();

        return new static(User::find($params['userId']), $session);
    }

    private static function createLinkKey(): string
    {
        $key = random_bytes(static::LINK_KEY_SIZE);
        $hmac = static::createLinkKeyHmac($key);

        return base64url_encode($key.$hmac);
    }

    private static function createLinkKeyHmac(string $key): string
    {
        return hash_hmac('sha1', $key, \Crypt::getKey(), true);
    }

    private static function verifyLinkKey(string $link): bool
    {
        $linkBin = base64url_decode($link);
        if ($linkBin === null) {
            return false;
        }

        $key = substr($linkBin, 0, static::LINK_KEY_SIZE);
        $hmac = substr($linkBin, static::LINK_KEY_SIZE);
        $expectedHmac = static::createLinkKeyHmac($key);

        return hash_equals($expectedHmac, $hmac);
    }

    private false|null|\stdClass $data = false;
    private string $dataKey;

    private function __construct(private ?User $user, private Store $session)
    {
        $currentSession = \Session::instance();
        $sessionId = $this->session->getId();
        if ($sessionId === $currentSession->getId()) {
            // Override passed session if it's the same as current session
            // otherwise the changes here will be overriden when current
            // session is saved.
            $this->session = $currentSession;
        }

        $this->dataKey = "user_verification:web:{$sessionId}";
    }

    public function issue()
    {
        $this->reset();
        $data = $this->build();
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

    public function isDone()
    {
        if ($this->user === null) {
            return true;
        }

        if ($this->session->get('verified')) {
            return true;
        }

        return false;
    }

    public function markVerified()
    {
        $this->reset();
        $this->session->put('verified', true);
        $this->session->save();

        UserSessionEvent::newVerified($this->user->getKey(), $this->session->getKey())->broadcast();
    }

    public function verify($inputKey)
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

    private function build(): \stdClass
    {
        return $this->data = (object) [
            'expiresAt' => time() + static::KEY_VALID_DURATION,
            // 1 byte = 2^8 bits = 16^2 bits = 2 hex characters
            'key' => bin2hex(random_bytes(config('osu.user.verification_key_length_hex') / 2)),
            'linkKey' => static::createLinkKey(),
            'tries' => 0,
        ];
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
            'sessionId' => $this->session->getId(),
        ], $duration);

        \Cache::put($this->dataKey, $data, $duration);
    }

    private function data(): ?\stdClass
    {
        if ($this->data === false) {
            $this->data = \Cache::get($this->dataKey);
        }

        return $this->data;
    }
}
