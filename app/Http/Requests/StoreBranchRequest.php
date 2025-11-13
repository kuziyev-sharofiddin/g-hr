<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
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
            'state_id' => ['nullable', 'integer', 'exists:states,id'],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'address' => ['nullable', 'string'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'target' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:100'],
            'responsible_worker' => ['nullable', 'string', 'max:255'],
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
            'name' => 'Filial nomi',
            'state_id' => 'Viloyat',
            'region_id' => 'Tuman',
            'address' => 'Manzil',
            'phone_number' => 'Telefon raqam',
            'target' => 'Maqsad',
            'location' => 'Joylashuv',
            'responsible_worker' => 'Mas\'ul xodim',
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
            'phone_number.max' => ':attribute :max belgidan oshmasligi kerak.',
        ];
    }
}
