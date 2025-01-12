<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'file' => [
                'required',
                'file',
                'max:8192',
            ],
            'name' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $file = $this->file('file');

        if ($file) {
            $this->merge([
                'name' => $this->input('name') ?? $file->getClientOriginalName(),
                'fileExtension' => $file->getClientOriginalExtension(),
                'fileSize' => round($file->getSize() / 1024 / 1024, 2), // Размер в MB
            ]);
        }
    }

    public function messages()
    {
        return [
            'file.required' => 'Файл обязателен для загрузки.',
            'file.max' => 'Размер файла не должен превышать 8 MB.',
        ];
    }
}
