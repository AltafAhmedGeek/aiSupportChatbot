<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiLoginRequest;
use App\Http\Resources\ApiLoginResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiLoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ApiLoginRequest $request)
    {
        try {
            $validated = $request->validated();

            if (! Auth::attempt($validated)) {

                return (new ApiLoginResource((object) [

                    'message' => 'Invalid Credential.',

                ]))->response()->setStatusCode(Response::HTTP_UNAUTHORIZED);

            }

            $user = User::where('email', $validated['email'])->first();

            // Generate an API token for the authenticated user
            $token = Auth::user()->createToken('api-token', ['*'], now()->addDay())->plainTextToken;

            // Return the token in the response
            return new ApiLoginResource((object) [
                'message' => 'Auth successful',
                'token'   => $token,
                'user'    => $user,
            ]);

        } catch (Throwable $th) {

            report($th);

            Log::error('API Login Error: '.$th->getMessage());

            return (new ApiLoginResource((object) [
                'message' => 'API Login Error',
            ]))->response()->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
