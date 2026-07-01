<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountabilityFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('upload accountability files');
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:10240', // 10 MB
                'mimes:pdf,doc,docx,jpg,jpeg,png',
            ],
        ];
    }
}
