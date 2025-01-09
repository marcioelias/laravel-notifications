<?php

namespace MarcioElias\LaravelNotifications\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use MarcioElias\LaravelNotifications\Facades\LaravelNotifications;
use MarcioElias\LaravelNotifications\Requests\DeviceTokenRequest;

class DeviceTokenController
{
    public function __invoke(DeviceTokenRequest $request)
    {
        try {
            $user = Auth::user();

            if ($request->device_token === $user->device_token) {
                return response()->json([
                    'message' => 'Device token already exists',
                ], 200);
            }

            $endpointArn = match (config('notifications.push_service_provider')) {
                'aws_sns' => LaravelNotifications::createEndpointArn($request->device_token, $request->custom_user_data),
                default => null,
            };

            $user->device_token = $request->device_token;
            $user->endpoint_arn = $endpointArn;
            $user->save();

            return response()->json([
                'message' => 'Device token updated successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
