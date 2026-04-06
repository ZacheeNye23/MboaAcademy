<?php
namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->isTeacher();
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['required', 'string', 'min:50'],
            'what_you_learn' => ['nullable', 'string'],
            'category'       => ['required', 'string', 'max:100'],
            'level'          => ['required', 'in:beginner,intermediate,advanced'],
            'language'       => ['required', 'string', 'max:10'],
            'price'          => ['required', 'numeric', 'min:0'],
            'is_free'        => ['boolean'],
            'thumbnail'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'preview_video'  => ['nullable', 'url', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'Le titre du cours est obligatoire.',
            'title.max'            => 'Le titre ne peut pas dépasser 255 caractères.',
            'description.required' => 'La description est obligatoire.',
            'description.min'      => 'La description doit contenir au moins 50 caractères.',
            'category.required'    => 'La catégorie est obligatoire.',
            'level.required'       => 'Le niveau est obligatoire.',
            'level.in'             => 'Le niveau doit être débutant, intermédiaire ou avancé.',
            'price.required'       => 'Le prix est obligatoire.',
            'price.min'            => 'Le prix ne peut pas être négatif.',
            'thumbnail.image'      => 'La miniature doit être une image.',
            'thumbnail.max'        => 'La miniature ne peut pas dépasser 2 Mo.',
            'preview_video.url'    => 'L\'URL de la vidéo de présentation n\'est pas valide.',
        ];
    }
}