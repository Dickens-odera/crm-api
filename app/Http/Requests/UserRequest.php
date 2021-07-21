<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:users|min:3|max:20',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:4|max:60|confirmed',
            'password_confirmation' => 'required'
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Please provide your name',
            'name.unique'   => 'The name has already been taken',
            'name.string'   => 'Please use valid string name format',
            'name.min'      => 'Name cannot be less than 3 characters long',
            'name.max'      => 'Name cannot be more than 20 characters long',
            'email.required' => 'Please provide your email address',
            'email.email'    => 'Please provide a valid email address',
            'email.unique'   => 'The email address has already been taken',
            'password.required' => 'Please provide your password',
            'password.min'      => 'The password cannot be less than 4 characters long',
            'password.max'      => 'The password cannot be more than 60 characters long',
            'password.confirmed'  => 'Password confirmations do not match',
            'password_confirmation.required' => 'Please confirm your password',
        ];
    }
}
