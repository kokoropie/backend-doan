<?php
namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ResponseHelper
{
    public static function responseJson(string $type = 'success', mixed $data = [], string $message = 'Success', int $code = 200): mixed
    {
        if ($data instanceof ResourceCollection) {
            $data = $data->response()->getData(true);
        }

        $return = [
            'status' => $type,
            'message' => $message,
            'data' => $data,
        ];
        if (!request()->is('api/auth/login')) {
            /** @var \App\Models\User $user */
            $user = request()->user();
            if ($user) {
                $currentToken = $user->currentAccessToken();
                if (Carbon::parse($currentToken->created_at)->addMinutes(floatval(config('sanctum.token_expiration')))->isPast()) {
                    $currentToken->delete();
                    if (request()->header('X-Refresh-Token') === $user->getRememberToken()) {
                        $return['token'] = $user->createToken('auth_token', ['role:' . $user->role->value])->plainTextToken;
                        $return['expires_at'] = time() + config('sanctum.token_expiration') * 60;
                    }
                }
            }
        }

        return response()->json($return, $code);
    }
}