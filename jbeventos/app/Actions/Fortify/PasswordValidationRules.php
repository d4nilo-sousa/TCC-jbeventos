<?php

namespace App\Actions\Fortify;

trait PasswordValidationRules
{
    /**
     * Retorna as regras de validação da senha.
     *
     * @param string $userType Tipo do usuário ('user' ou 'coordinator')
     * @return array
     */
    protected function passwordRules(string $userType = 'user'): array
    {
        if ($userType === 'coordinator') {
            // Coordenador: requisitos fortes
            return [
                'required',
                'string',
                'regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%&*])[A-Za-z0-9!@#$%&*]+$/',
                'min:8',
                'confirmed',
            ];
        }

        // Usuário comum: só precisa de 8 caracteres
        return [
            'required',
            'string',
            'min:8',
            'confirmed',
        ];
    }
}
