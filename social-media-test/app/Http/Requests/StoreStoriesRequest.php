<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreStoriesRequest extends FormRequest
{
    public static array $extensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp',
        'mp4'
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'media' => [
                'required',
                'file',
                'max:51200',// 10 MB
                File::types(self::$extensions),
            ],
            'caption' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'media.required' => 'Debes subir una imagen o video.',
            'media.file' => 'El archivo debe ser válido.',
            'media.max' => 'El archivo no puede pesar más de 50 MB.',
            'media.mimes' => 'Tipo de archivo no permitido.',
        ];
    }
}
