<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePositionRequest extends FormRequest
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
            'section_id' => ['required', 'integer', 'exists:sections,id'],
            'does_it_belong_to_the_curator' => ['nullable', 'boolean'],
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
            'name' => 'Lavozim nomi',
            'responsible_worker' => 'Mas\'ul xodim',
            'section_id' => 'Bo\'lim',
            'does_it_belong_to_the_curator' => 'Mudir yo\'qotadi',
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
            'section_id.required' => ':attribute majburiy maydon.',
            'section_id.exists' => 'Tanlagan :attribute mavjud emas.',
        ];
    }
}
