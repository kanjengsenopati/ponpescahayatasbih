<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SchoolRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys(\App\Models\School::getListType())),
            'address' => 'nullable|string|max:500',
            'description' => 'required|string|max:1000',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'icon_name' => 'nullable|string|max:255',
        ];
    }
}
