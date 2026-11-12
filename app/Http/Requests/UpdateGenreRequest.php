<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGenreRequest extends FormRequest
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
        $genre = $this->route('genre');
        $genreId = is_object($genre) ? $genre->id : $genre;

        return [
            'name' => [
                'required',
                'string',
                'max:45',
                Rule::unique('genres', 'name')->ignore($genreId),
            ],
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama genre wajib diisi.',
            'name.max' => 'Nama genre maksimal 45 karakter.',
            'name.unique' => 'Nama genre sudah ada.',
        ];
    }
}

