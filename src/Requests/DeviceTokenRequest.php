<?php

namespace MarcioElias\LaravelNotifications\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeviceTokenRequest extends FormRequest
{
    public function rules()
    {
        return [
            'device_token' => 'nullable',
            'custom_user_data' => 'sometimes|nullable|array',
        ];
    }
}
