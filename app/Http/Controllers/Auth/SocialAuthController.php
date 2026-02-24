<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        if (! $this->isSupportedProvider($provider)) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        if (! $this->isSupportedProvider($provider)) {
            abort(404);
        }

        try {
            $providerUser = Socialite::driver($provider)->user();
            $providerId = (string) $providerUser->getId();

            if ($providerId === '') {
                return redirect()->route('login')->withErrors([
                    'email' => 'Prijava ni uspela. Poskusite znova.',
                ]);
            }

            $providerColumn = $provider.'_id';

            $user = User::firstWhere($providerColumn, $providerId);

            if (! $user) {
                $email = $this->resolveEmail($provider, (string) $providerUser->getEmail(), $providerId);
                $user = User::firstWhere('email', $email);

                if ($user) {
                    if (! $user->{$providerColumn}) {
                        $user->forceFill([$providerColumn => $providerId])->save();
                    }
                } else {
                    $name = trim((string) ($providerUser->getName() ?? $providerUser->getNickname() ?? ''));

                    if ($name === '') {
                        $name = Str::before($email, '@');
                    }

                    $user = User::create([
                        'display_name' => $name,
                        'name' => $name,
                        'email' => $email,
                        'email_verified_at' => now(),
                        'password' => Str::random(32),
                        $providerColumn => $providerId,
                    ]);
                }
            }

            Auth::login($user);
            request()->session()->regenerate();

            return redirect()->intended(config('fortify.home'));
        } catch (Throwable) {
            return redirect()->route('login')->withErrors([
                'email' => 'Prijava ni uspela. Poskusite znova.',
            ]);
        }
    }

    private function isSupportedProvider(string $provider): bool
    {
        return in_array($provider, ['google', 'facebook'], true);
    }

    private function resolveEmail(string $provider, string $email, string $providerId): string
    {
        $normalizedEmail = Str::lower(trim($email));

        if ($normalizedEmail !== '') {
            return $normalizedEmail;
        }

        if ($provider !== 'facebook') {
            throw new \RuntimeException('Missing required email from provider.');
        }

        $base = 'facebook-'.$providerId;
        $candidate = $base.'@placeholder.priprave.net';
        $suffix = 2;

        while (User::query()->where('email', $candidate)->exists()) {
            $candidate = $base.'-'.$suffix.'@placeholder.priprave.net';
            $suffix++;
        }

        return $candidate;
    }
}
