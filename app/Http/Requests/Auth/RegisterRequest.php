<?php

// app/Http/Requests/Auth/RegisterRequest.php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:2', 'max:50'],
            'last_name'  => ['required', 'string', 'min:2', 'max:50'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'role'       => ['required', 'in:student,teacher'],
            'password'   => ['required', 'confirmed', Password::defaults()],
            'terms'      => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Le prénom est obligatoire.',
            'first_name.min'      => 'Le prénom doit contenir au moins 2 caractères.',
            'last_name.required'  => 'Le nom est obligatoire.',
            'last_name.min'       => 'Le nom doit contenir au moins 2 caractères.',
            'email.required'      => 'L\'adresse email est obligatoire.',
            'email.email'         => 'L\'adresse email n\'est pas valide.',
            'email.unique'        => 'Cette adresse email est déjà utilisée.',
            'role.required'       => 'Veuillez choisir un rôle.',
            'role.in'             => 'Le rôle sélectionné est invalide.',
            'password.required'   => 'Le mot de passe est obligatoire.',
            'password.confirmed'  => 'Les mots de passe ne correspondent pas.',
            'terms.accepted'      => 'Vous devez accepter les conditions d\'utilisation.',
        ];
    }
}