<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Support\Facades\Hash;

class LegacyMd5UserProvider extends EloquentUserProvider
{
    /**
     * Validate a user against the given credentials, upgrading legacy MD5
     * passwords to bcrypt transparently on first successful login.
     */
    public function validateCredentials(UserContract $user, array $credentials): bool
    {
        $plain = $credentials['password'];
        $hashed = $user->getAuthPassword();

        if ($this->isLegacyMd5Hash($hashed)) {
            $salt = config('auth.legacy_password_salt', '');

            if (md5($salt.$plain) !== $hashed) {
                return false;
            }

            $user->forceFill(['password' => Hash::make($plain)])->save();

            return true;
        }

        return parent::validateCredentials($user, $credentials);
    }

    private function isLegacyMd5Hash(string $hash): bool
    {
        return strlen($hash) === 32 && ctype_xdigit($hash);
    }
}
