<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);
        Fortify::loginView(fn() => view('auth.login'));

        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            // Método responsável por definir para onde o usuário será redirecionado após o login
            public function toResponse($request) {
                // Pega o usuário autenticado a partir da requisição
                $user = $request->user();

                // Verifica o tipo do usuário e redireciona para a rota correspondente
                if ($user->user_type === 'admin') {
                    // Redireciona para o painel do admin
                    return redirect()->route('admin.dashboard');
                } elseif ($user->user_type === 'coordinator') {
                    // Redireciona para o painel do coordenador
                    return redirect()->route('coordinator.dashboard');
                } else if ($user->user_type === 'user') {
                    // Redireciona para o painel do usuário comum
                    return redirect()->route('user.dashboard');
                }
                // Caso o tipo do usuário não seja nenhum dos anteriores, redireciona para a home padrão
                return redirect('/home');
            }
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
