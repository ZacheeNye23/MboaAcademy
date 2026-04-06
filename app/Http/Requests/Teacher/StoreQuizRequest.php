<?php
namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreQuizRequest extends FormRequest
{
    public function authorize(): bool { return Auth::user()->isTeacher(); }

    public function rules(): array
    {
        return [
            'course_id'        => ['required', 'exists:courses,id'],
            'lesson_id'        => ['nullable', 'exists:lessons,id'],
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string', 'max:1000'],
            'passing_score'    => ['required', 'integer', 'min:0', 'max:100'],
            'max_attempts'     => ['required', 'integer', 'min:1', 'max:10'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:360'],
            'show_answers'     => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'course_id.required'     => 'Veuillez sélectionner un cours.',
            'course_id.exists'       => 'Ce cours est introuvable.',
            'title.required'         => 'Le titre du quiz est obligatoire.',
            'passing_score.required' => 'Le score de passage est obligatoire.',
            'passing_score.min'      => 'Le score doit être entre 0 et 100.',
            'passing_score.max'      => 'Le score doit être entre 0 et 100.',
            'max_attempts.required'  => 'Le nombre de tentatives est obligatoire.',
            'max_attempts.min'       => 'Au moins 1 tentative est requise.',
        ];
    }
}