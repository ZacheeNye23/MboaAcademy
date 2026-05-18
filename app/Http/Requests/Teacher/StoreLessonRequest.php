<?php
namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreLessonRequest extends FormRequest
{
    public function authorize(): bool { return Auth::user()->isTeacher(); }

   public function rules()
{
    return [
        'title' => ['required', 'string', 'max:255'],
        'type'  => ['required', 'in:video,text,mixed'],

        'content'   => ['nullable', 'string'],
        'video_url' => ['nullable', 'url'],
         'video'     => ['nullable', 'file', 'mimes:mp4,mov,avi,webm', 'max:512000'], // 512 Mo

        'duration' => ['nullable', 'integer'],
        'is_free'  => ['nullable', 'boolean'],
    ];
}

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if ($this->type === 'video') {
                if (!$this->video && !$this->video_url) {
                    $validator->errors()->add('video', 'Vidéo ou URL requise.');
                }
            }

            if ($this->type === 'text') {
                if (!$this->content) {
                    $validator->errors()->add('content', 'Contenu requis.');
                }
            }

            if ($this->type === 'mixed') {
                if (!$this->content && !$this->video && !$this->video_url) {
                    $validator->errors()->add('type', 'Ajoute au moins du contenu ou une vidéo.');
                }
            }

        });
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