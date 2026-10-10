<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreIntakeSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contact_name' => ['required', 'string', 'max:160'],
            'contact_email' => ['required', 'email', 'max:254'],
            'organization_name' => ['nullable', 'string', 'max:180'],
            'problem_statement' => ['required', 'string', 'min:20'],
            'desired_outcome' => ['required', 'string', 'min:20'],
            'constrains' => ['nullable', 'string', 'max:5000'],
            'additional_context' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'contact_name' => 'nama kontak',
            'contact_email' => 'email kontak',
            'organization_name' => 'nama organisasi',
            'problem_statement' => 'masalah yang ingin diselesaikan',
            'desired_outcome' => 'hasil yang diharapkan',
            'constrains' => 'batasan proyek',
            'additional_context' => 'konteks tambahan',
        ];
    }
}
