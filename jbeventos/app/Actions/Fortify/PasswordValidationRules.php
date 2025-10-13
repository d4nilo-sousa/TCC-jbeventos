<?php

namespace App\Actions\Fortify;

trait PasswordValidationRules
{
    /**
     * Retorna as regras de validação da senha.
     *
     * @param string $userType Tipo do usuário ('user', 'coordinator' ou 'admin')
     * @return array
     */
    protected function passwordRules(string $userType = 'user'): array
    {
        if ($userType === 'coordinator' || $userType === 'admin') {
            return [
                'required',
                'string',
                'regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%&*])[A-Za-z0-9!@#$%&*]+$/',
                'min:8',
                'confirmed',
            ];
        }

        return [
            'required',
            'string',
            'min:8',
            'confirmed',
        ];
    }
}
