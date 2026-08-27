<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            // Validasi WhatsApp Number
            'whatsapp_number' => [
                'required',
                'string',
                'regex:/^08[0-9]{8,13}$/',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'whatsapp_number.regex' => 'Format nomor WhatsApp harus diawali dengan 08.',
            'whatsapp_number.unique' => 'Nomor WhatsApp ini sudah digunakan oleh akun lain.',
        ];
    }
}
