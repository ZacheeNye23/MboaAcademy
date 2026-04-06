<?php
namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreLessonRequest extends FormRequest
{
    public function authorize(): bool { return Auth::user()->isTeacher(); }

    public function rules(): array
    {
        return [
            'title'     => ['required', 'string', 'max:255'],
            'content'   => ['nullable', 'string'],
            'type'      => ['required', 'in:video,text,mixed'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'video'     => [
                'nullable',
                'file',
                'mimetypes:video/mp4,video/mpeg,video/quicktime,video/webm',
                'max:512000', // 500 Mo max
            ],
            'duration'  => ['nullable', 'integer', 'min:1'],
            'is_free'   => ['boolean'],
            'order'     => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'      => 'Le titre de la leçon est obligatoire.',
            'type.required'       => 'Le type de leçon est obligatoire.',
            'type.in'             => 'Le type doit être vidéo, texte ou mixte.',
            'video.mimetypes'     => 'La vidéo doit être au format MP4, MPEG, MOV ou WebM.',
            'video.max'           => 'La vidéo ne peut pas dépasser 500 Mo.',
            'video_url.url'       => 'L\'URL de la vidéo n\'est pas valide.',
        ];
    }
}