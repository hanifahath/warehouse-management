<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStatusUpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'is_approved' => 'required|boolean',
        ];
    }

}