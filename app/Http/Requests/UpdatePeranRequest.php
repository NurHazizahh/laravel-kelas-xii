<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePeranRequest extends FormRequest
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
            'film_id' => ['required', 'exists:films,id'],
            'cast_id' => ['required', 'exists:casts,id'],
            'actor' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'film_id.required' => 'Film wajib dipilih.',
            'film_id.exists' => 'Film yang dipilih tidak valid.',
            'cast_id.required' => 'Cast wajib dipilih.',
            'cast_id.exists' => 'Cast yang dipilih tidak valid.',
            'actor.required' => 'Nama peran / karakter wajib diisi.',
            'actor.max' => 'Nama peran maksimal 255 karakter.',
        ];
    }
}
