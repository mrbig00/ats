<?php

use App\Http\Controllers\Api\V1\MeetingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (v1)
|--------------------------------------------------------------------------
|
| Versioned REST API under /api/v1.
|
| Authentication (choose one):
| 1) Bearer token: send header "Authorization: Bearer {token}".
|    Obtain token: POST /api/v1/tokens with JSON body:
|    { "email": "...", "password": "...", "device_name": "..." }
|    Returns { "token": "..." }.
| 2) Cookie (SPA): same-origin requests with Accept: application/json
|    and session cookie (e.g. after POST /login via Fortify).
|
*/

Route::prefix('v1')->middleware('throttle:api')->group(function (): void {
    Route::post('/tokens', function (Request $request) {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['required', 'string', 'max:255'],
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json(['token' => $token]);
    })->name('api.v1.tokens.store');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::apiResource('meetings', MeetingController::class);
    });
});
