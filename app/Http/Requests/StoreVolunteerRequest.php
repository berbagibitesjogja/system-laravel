<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVolunteerRequest extends FormRequest
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
            // Add rules if needed, e.g. 'name' => 'required'
            // Since original code had no explicit validation, leaving standard empty or minimal
        ];
    }
    
    protected function prepareForValidation()
    {
        if ($this->has('phone')) {
             $this->merge([
                'phone' => str_replace('08', '628', $this->phone)
            ]);
        }
    }
}
