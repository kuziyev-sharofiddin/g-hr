<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookGenreRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'name_ru' => ['nullable', 'string', 'max:255'],
            'responsible_worker' => ['nullable', 'string', 'max:255'],
            'genre_category_id' => ['nullable', 'integer', 'exists:genre_categories,id'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'Janr nomi',
            'name_ru' => 'Janr nomi (rus tilida)',
            'responsible_worker' => 'Mas\'ul xodim',
            'genre_category_id' => 'Janr kategoriyasi',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => ':attribute majburiy maydon.',
            'name.max' => ':attribute :max belgidan oshmasligi kerak.',
            'name_ru.max' => ':attribute :max belgidan oshmasligi kerak.',
        ];
    }
}
