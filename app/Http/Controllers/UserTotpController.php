<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\InvariantException;
use App\Models\UserTotpKey;
use Symfony\Component\HttpFoundation\Response;

class UserTotpController extends Controller
{
    private const TIMEOUT = 600;

    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
        //$this->middleware('throttle:20,1440,user-totp:');
        $this->middleware('verify-user');

        $this->middleware(function ($request, $next) {
            $this->currentUser = \Auth::user();

            if ($this->currentUser->userTotpKey !== null) {
                $session = \Session::instance();
                $session->flash('popup', osu_trans('user_totp.index.already_setup'));
                \Cache::forget($this->cacheKey());

                return ujs_redirect(route('account.edit').'#totp');
            }

            return $next($request);
        }, ['except' => ['destroy', 'edit']]);
    }

    public function create(): Response
    {
        return ext_view('user_totp.create', [
            'uri' => \Cache::get($this->cacheKey()),
        ]);
    }

    public function destroy(): Response
    {
        $currentUser = \Auth::user();
        $currentUser->userTotpKey?->delete();

        \Session::flash('popup', osu_trans('user_totp.destroy.ok'));

        return ujs_redirect(route('account.edit').'#totp');
    }

    public function edit(): Response
    {
        $currentUser = \Auth::user();
        if ($currentUser->userTotpKey === null) {
            \Session::flash('popup', osu_trans('user_totp.destroy.missing'));

            return ujs_redirect(route('account.edit').'#totp');
        }

        return ext_view('user_totp.edit');
    }

    public function issueUri()
    {
        $currentUser = \Auth::user();
        $password = get_string(request('password')) ?? '';

        if (!$currentUser->checkPassword($password)) {
            return response(['form_error' => [
                'password' => [osu_trans('user_totp.issue_uri.invalid_password')],
            ]], 422);
        }

        \Cache::put($this->cacheKey(), UserTotpKey::generateUri($currentUser), static::TIMEOUT);

        return ujs_redirect(route('user-totp.create'));
    }

    public function store()
    {
        $key = get_string(request('key')) ?? '';

        $session = \Session::instance();
        $cacheKey = $this->cacheKey();
        $totpUri = \Cache::get($cacheKey);
        if ($totpUri === null) {
            $session->flash('popup', osu_trans('user_totp.store.restart'));

            return ujs_redirect(route('user-totp.create'));
        }

        $currentUser = \Auth::user();

        $existingTotpKey = $currentUser->userTotpKey;
        if ($existingTotpKey === null) {
            if (UserTotpKey::isValidKey($totpUri, $key)) {
                try {
                    $totpKey = $currentUser->userTotpKey()->create([
                        'uri' => $totpUri,
                    ]);
                } catch (\Throwable $e) {
                    if (!is_sql_unique_exception($e)) {
                        throw $e;
                    }
                    // uhhh
                    $existingTotpKey = $currentUser->userTotpKey()->first();
                }
                \Cache::forget($cacheKey);
            } else {
                return response(['form_error' => [
                    'key' => [osu_trans('user_verification.errors.incorrect_key')],
                ]], 422);
            }
        }

        // this also handles race condition between key existence check and creation
        if ($existingTotpKey !== null && $existingTotpKey !== $totpUri) {
            // pretend setup is complete
            throw new InvariantException('you already have a different totp key setup!');
        }

        $session->flash('popup', osu_trans('user_totp.store.ok'));

        return ujs_redirect(route('account.edit').'#totp');
    }

    private function cacheKey(): string
    {
        $id = \Session::getKey();

        return "user_totp:create:{$id}";
    }
}
