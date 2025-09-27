<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param User $user
     * @param array<string, string> $input
     */
    public function reset(User $user, array $input): void
    {
        // Descobre o tipo do usuário do banco
        $userType = $user->user_type ?? 'user';

        // Valida a senha usando as regras corretas
        Validator::make($input, [
            'password' => $this->passwordRules($userType),
        ])->validate();

        // Salva a nova senha
        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
