<?php
namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreChapterRequest extends FormRequest
{
    public function authorize(): bool { return Auth::user()->isTeacher(); }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre du chapitre est obligatoire.',
        ];
    }
}