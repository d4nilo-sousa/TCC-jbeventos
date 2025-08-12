<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    // Define as regras de validação para a senha:
    protected function passwordRules(): array
    {
        // - 'regex': exige pelo menos uma letra maiúscula, um número e um caractere especial entre !@#$%&*;
        return ['required', 'string', 'regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%&*])[A-Za-z0-9!@#$%&*]+$/', 'min:8', 'confirmed'];
    }
}
