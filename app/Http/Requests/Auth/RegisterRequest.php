<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['nullable', 'string', 'max:100'],
            'cedula' => ['required', 'string', 'max:20', 'unique:USUARIO,CED_USU', 'unique:CLIENTE,CED_CLI'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:120', 'unique:USUARIO,EMA_USU'],
            'password' => ['required', 'string', 'min:6', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'Ingresa tus nombres.',
            'cedula.required' => 'Ingresa tu cédula.',
            'cedula.unique' => 'La cédula ya está registrada.',
            'email.required' => 'Ingresa tu correo electrónico.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.required' => 'Ingresa una contraseña.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ];
    }
}
