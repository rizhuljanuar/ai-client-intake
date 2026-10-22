<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScopeDecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $version = $this->route('version');

        return $version instanceof \App\Models\ScopeVersion
            && $this->user()?->can('decide', $version) === true;
    }

    public function rules(): array
    {
        return [
            'note' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }
}
