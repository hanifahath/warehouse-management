<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestockUpdateStatusRequest extends FormRequest
{
    public function authorize()
    {
        $user = $this->user();
        return $user && in_array($user->role, ['Manager', 'Admin', 'Staff']);
    }

    public function rules()
    {
        return [
            'status' => ['required', 'in:In Transit,Received'],
        ];
    }
}
