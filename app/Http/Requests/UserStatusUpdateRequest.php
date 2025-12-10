<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStatusUpdateRequest extends FormRequest
{
    public function authorize()
    {
        // Gunakan policy untuk authorization
        $user = $this->route('user');
        return $user && $this->user()->can('approve', $user);
    }

    public function rules()
    {
        return [
            'is_approved' => 'required|boolean',
        ];
    }
}