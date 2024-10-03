<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */

    public function register(RegisterRequest $registerRequest) : UserResource {

        $dataValidated = $registerRequest->validated();

        try {
            $user = User::create([
                'username' => $dataValidated['username'],
                'email' => $dataValidated['email'],
                'password' => Hash::make($dataValidated['password']),
            ]);


        } catch (\Throwable $th) {

            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }

        return new UserResource($user, 'Successfully Registered User');

    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $loginRequest) : UserResource
    {
        $dataValidated = $loginRequest->validated();


        if (! $token = Auth::attempt($dataValidated)) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "username / password invalid"
                    ],
                ]
                ],400));
        }

        return new UserResource(Auth::user(), "Successfully Login", $token);


    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() :JsonResponse

    {
        Auth::logout();

        return response()->json(['Success' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() :UserResource
    {
        $token = Auth::refresh();

        return new UserResource(Auth::user(), "Successfully Refresh Token", $token);


    }

    public function getcurrrentuser(){


        try {

            return new UserResource(Auth::user(), "Successfully Get Data Current User");

        } catch (\Throwable $th) {

            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
    }


}
