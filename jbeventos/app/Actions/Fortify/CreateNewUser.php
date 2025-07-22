<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['nullable', 'string', 'regex:/^\(\d{2}\) \d{5}-\d{4}$/', 'unique:users'], // telefone opcional, formato específico, único na tabela users
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();
        
        // Ajusta o número de telefone para null se for string vazia
        $phoneNumber = $input['phone_number'];

        if ($phoneNumber === '') {
            $phoneNumber = null;
        }

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone_number'  => $phoneNumber,
            'password' => Hash::make($input['password']),
        ]);
    }
}
