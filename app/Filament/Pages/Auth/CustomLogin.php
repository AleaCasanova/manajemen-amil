<?php

namespace App\Filament\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Timebox;

class CustomLogin extends BaseLogin
{
    /**
     * Authenticate with strict rate limiting: max 5 attempts per 15 minutes (900 seconds)
     */
    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5, 900);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            Log::warning('Rate limit login admin terlampaui dari IP: '.request()->ip().' (Terkunci '.ceil($exception->secondsUntilAvailable / 60).' menit)');

            return null;
        }

        $data = $this->form->getState();

        /** @var SessionGuard $authGuard */
        $authGuard = Filament::auth();

        $authProvider = $authGuard->getProvider();
        $credentials = $this->getCredentialsFromFormData($data);
        $remember = $data['remember'] ?? false;
        $timeboxDuration = (int) config('auth.timebox_duration', 200_000);

        $user = app(Timebox::class)->call(function (Timebox $timebox) use ($authProvider, $authGuard, $credentials, $remember): Authenticatable {
            $this->fireAttemptingEvent($authGuard, $credentials, $remember);

            $user = $authProvider->retrieveByCredentials($credentials);

            if ((! $user) || (! $authProvider->validateCredentials($user, $credentials))) {
                $this->userUndertakingMultiFactorAuthentication = null;

                $this->fireFailedEvent($authGuard, $user, $credentials);
                Log::warning('Percobaan login admin gagal untuk akun ['.($credentials['email'] ?? 'unknown').'] dari IP: '.request()->ip());
                $this->throwFailureValidationException();
            }

            if (! $this->isUserAllowedToAccessPanel($user)) {
                $this->userUndertakingMultiFactorAuthentication = null;

                $this->fireFailedEvent($authGuard, $user, $credentials);
                Log::warning('User bukan admin mencoba masuk ke panel admin: ['.($credentials['email'] ?? 'unknown').'] dari IP: '.request()->ip());
                $this->throwFailureValidationException();
            }

            $timebox->returnEarly();

            return $user;
        }, $timeboxDuration);

        $needsMultiFactorChallenge = app(Timebox::class)->call(function (Timebox $timebox) use ($user): bool {
            $userUndertakingMultiFactorAuthenticationData = $this->getUserUndertakingMultiFactorAuthenticationData();

            if (
                $userUndertakingMultiFactorAuthenticationData &&
                hash_equals($userUndertakingMultiFactorAuthenticationData['userKey'], Filament::getUserScopedAuthIdentifier($user)) &&
                $this->getUserUndertakingMultiFactorAuthentication()
            ) {
                if ($this->isMultiFactorChallengeRateLimited($user)) {
                    return true;
                }

                $this->cacheSchema('multiFactorChallengeForm', null);
                $this->multiFactorChallengeForm->validate();

                return false;
            }

            $this->userUndertakingMultiFactorAuthentication = null;

            $multiFactorChallenge = $this->getMultiFactorChallenge();

            if ($multiFactorAuthenticationProvider = $multiFactorChallenge->getFirstEnabledProvider($user)) {
                $this->userUndertakingMultiFactorAuthentication = encrypt([
                    'identifier' => $user->getAuthIdentifier(),
                    'userKey' => Filament::getUserScopedAuthIdentifier($user),
                ]);

                $multiFactorChallenge->beforeChallenge($user, $multiFactorAuthenticationProvider);
            }

            if (filled($this->userUndertakingMultiFactorAuthentication)) {
                $this->cacheSchema('multiFactorChallengeForm', null);
                $this->multiFactorChallengeForm->fill();

                return true;
            }

            if (Filament::getMultiFactorAuthenticationProviders() === []) {
                $timebox->returnEarly();
            }

            return false;
        }, $timeboxDuration);

        if ($needsMultiFactorChallenge) {
            return null;
        }

        $userScopedAuthIdentifier = Filament::getUserScopedAuthIdentifier($user);

        if (! $authGuard->attemptWhen(
            $credentials,
            fn (Authenticatable $user): bool => hash_equals($userScopedAuthIdentifier, Filament::getUserScopedAuthIdentifier($user)) && $this->isUserAllowedToAccessPanel($user),
            $remember,
        )) {
            $this->throwFailureValidationException();
        }

        session()->regenerate();

        Log::info('Admin berhasil login: '.$user->name.' ('.$user->email.') dari IP: '.request()->ip());

        return app(LoginResponse::class);
    }

    protected function getRateLimitedNotification(TooManyRequestsException $exception): ?Notification
    {
        $minutes = max(1, (int) ceil($exception->secondsUntilAvailable / 60));

        return Notification::make()
            ->title('Akses Dibatasi Sementara')
            ->body("Terlalu banyak percobaan login gagal dari perangkat ini. Demi keamanan data internal, silakan coba lagi dalam {$minutes} menit.")
            ->danger()
            ->persistent();
    }
}
