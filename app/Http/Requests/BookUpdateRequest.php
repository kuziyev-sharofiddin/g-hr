<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BookUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'short_description' => 'required|string',
            'long_description' => 'required|string',
            'book_status' => 'nullable|string|in:all,recommended,unrecommended',
            'image_path' => 'nullable|string',
            'book_author_id' => 'required|integer|exists:book_authors,id',
            'book_genre_id' => 'required|integer|exists:genres,id',
            'book_language_id' => 'required|integer|exists:book_languages,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'name.required' => 'Kitob nomi kiritilishi majburiy.',
            'name.string' => 'Kitob nomi matn formatida bo\'lishi kerak.',
            'name.max' => 'Kitob nomi 255 belgidan oshmasligi kerak.',
            'short_description.required' => 'Qisqa tavsif kiritilishi majburiy.',
            'short_description.string' => 'Qisqa tavsif matn formatida bo\'lishi kerak.',
            'long_description.required' => 'To\'liq tavsif kiritilishi majburiy.',
            'long_description.string' => 'To\'liq tavsif matn formatida bo\'lishi kerak.',
            'book_status.string' => 'Kitob holati matn bo\'lishi kerak.',
            'book_status.in' => 'Kitob holati all, recommended yoki unrecommended bo\'lishi kerak.',
            'image_path.string' => 'Rasm yo\'li matn formatida bo\'lishi kerak.',
            'book_author_id.required' => 'Muallif tanlash majburiy.',
            'book_author_id.integer' => 'Muallif ID butun son bo\'lishi kerak.',
            'book_author_id.exists' => 'Tanlangan muallif mavjud emas.',
            'book_genre_id.required' => 'Janr tanlash majburiy.',
            'book_genre_id.integer' => 'Janr ID butun son bo\'lishi kerak.',
            'book_genre_id.exists' => 'Tanlangan janr mavjud emas.',
            'book_language_id.required' => 'Til tanlash majburiy.',
            'book_language_id.integer' => 'Til ID butun son bo\'lishi kerak.',
            'book_language_id.exists' => 'Tanlangan til mavjud emas.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors()
        ]));
    }
}
