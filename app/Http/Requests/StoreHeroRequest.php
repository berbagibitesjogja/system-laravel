<?php

namespace App\Http\Requests;

use App\Models\Donation\Donation;
use App\Models\Volunteer\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreHeroRequest extends FormRequest
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
            'phone' => 'regex:/^8/',
            'donation' => 'required',
            'name' => 'required',
            'faculty' => 'required',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $donation = Donation::find($this->donation);
            if (!$donation || $donation->remain <= 0) {
                $validator->errors()->add('donation', 'Gagal mendaftar');
            }

            $currentPhone = '62' . $this->phone;
            if ($donation && $donation->heroes->pluck('phone')->contains($currentPhone)) {
                 $validator->errors()->add('phone', 'Gagal mendaftar');
            }
            
            $volunteer = User::all()->pluck('phone');
            if ($volunteer->contains($currentPhone)) {
                $validator->errors()->add('phone', 'Gagal mendaftar');
            }
        });
    }

    protected function prepareForValidation()
    {
        // phone concatenation moves to logic or here? 
        // Controller did validation on regex first, then added '62'.
        // So validation rule expects '8' prefix.
    }
}
