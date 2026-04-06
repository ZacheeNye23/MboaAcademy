<?php
namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool { return Auth::user()->isTeacher(); }

    public function rules(): array
    {
        return [
            'question'           => ['required', 'string', 'min:3', 'max:1000'],
            'type'               => ['required', 'in:single,multiple,true_false'],
            'explanation'        => ['nullable', 'string', 'max:1000'],
            'points'             => ['required', 'integer', 'min:1', 'max:100'],
            'answers'            => ['required', 'array', 'min:2', 'max:6'],
            'answers.*.text'     => ['required', 'string', 'max:500'],
            'answers.*.correct'  => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'question.required'        => 'La question est obligatoire.',
            'question.min'             => 'La question doit contenir au moins 3 caractères.',
            'type.required'            => 'Le type de question est obligatoire.',
            'type.in'                  => 'Type invalide (single, multiple ou true_false).',
            'points.required'          => 'Les points sont obligatoires.',
            'answers.required'         => 'Au moins 2 réponses sont requises.',
            'answers.min'              => 'Ajoutez au moins 2 réponses.',
            'answers.*.text.required'  => 'Chaque réponse doit avoir un texte.',
        ];
    }

    // Validation personnalisée : au moins une bonne réponse
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $answers = $this->input('answers', []);
            $hasCorrect = collect($answers)->some(fn($a) => !empty($a['correct']));

            if (!$hasCorrect) {
                $validator->errors()->add('answers', 'Cochez au moins une réponse correcte.');
            }

            // Pour type "single" : une seule bonne réponse
            if ($this->type === 'single') {
                $correctCount = collect($answers)->filter(fn($a) => !empty($a['correct']))->count();
                if ($correctCount > 1) {
                    $validator->errors()->add('answers', 'Un QCM à réponse unique ne peut avoir qu\'une seule bonne réponse.');
                }
            }
        });
    }
}