<?php

namespace App\Http\Requests;

use Exception;
use Illuminate\Validation\ValidationException;
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
        $rules = [
            'name' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];

        if ($this->input("_method") && $this->input("_method") == "PUT") {
            $rules['file'] = [
                'nullable',
                'file',
                'max:8192',
            ];
        } else {
            $rules['file'] = [
                'required', 
                'file',
                'max:8192',
            ];
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        $file = $this->file('file');

        if ($file) {
            $this->merge([
                'name' => $this->input('name') ?? $file->getClientOriginalName(),
                'originalName' => $file->getClientOriginalName(),
                'fileExtension' => $file->getClientOriginalExtension(),
                'fileSize' => round($file->getSize() / 1024 / 1024, 2), // Размер в MB
            ]);
        } else {
            $this->merge([
                'name' => $this->input('name'),
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
