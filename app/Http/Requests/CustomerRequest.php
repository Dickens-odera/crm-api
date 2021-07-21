<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
            'name' => 'required|unique:customers|string|min:3|max:10',
            'surname' => 'required|unique:customers|string|min:3|max:10',
            'photo_url' => 'nullable|image|mimes:jpeg,svg,png,jpg|max:2048'
        ];
    }

    /**
     * Custom validation messages
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please provide the customer\'s name',
            'name.unique'   => 'The name has already been taken',
            'name.string'   => 'Only valid string characters are allowed for the name',
            'name.min'      => 'The name cannot be less than 3 characters long',
            'name.max'      => 'The name cannot be more than 10 characters long',
            'surname.string'   => 'Only valid string characters are allowed for the surname',
            'surname.min'      => 'The surname cannot be less than 3 characters long',
            'surname.max'      => 'The surname cannot be more than 10 characters long',
            'photo_url.mimes' => 'Only JPG, PNG, SVG file types are allowed for the photo',
            'photo_url.image' => 'Only images are allowed for the photo',
            'photo_url.max'   => 'Uploaded image cannot exceed 2BM'
        ];
    }
}
