<?php

namespace MarcioElias\LaravelNotifications\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeviceTokenRequest extends FormRequest
{
    public function rules()
    {
        return [
            'device_token' => 'required|string',
            'custom_user_data' => 'nullable|array',
        ];
    }
}
