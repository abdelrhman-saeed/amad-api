<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\TokenID;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function auth_with_amadeus(string $uuid): string
    {
        return Http::withHeaders(['ama-client-ref' => $uuid])
                    ->asForm()
                    ->post(env('AMADEUS_AUTH'), [
                        'grant_type'    => env('AMADEUS_GRANT_TYPE'),
                        'client_id'     => env('AMADEUS_CLIENT_ID'),
                        'client_secret' => env('AMADEUS_CLIENT_SECRET'),
                    ])->json('access_token');
    }
 
    public function register(RegisterUserRequest $request)
    {
        User::create($request->validated());

        return response('registered');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'     => 'required|email',
            'password'  => 'required|string'
        ]);

        $user = User::where('email', $credentials['email'])
                        ->firstOrFail();

        if (!Hash::check($credentials['password'], $user->password)) {
            return response('invalid email or password', 422);
        }

        // if ($user->tokens()->exists()) {
        //     return response('already have a valid token', 401);
        // }

        $sanctumAccessToken = $user->createToken('default_token')->toArray();

        auth()->login($user);

        TokenID::create([
            'access_token'  => $this->auth_with_amadeus($uuid = Str::uuid()->__tostring()),
            'token_id'      => $sanctumAccessToken['accessToken']['id'],
            'uuid'          => $uuid,
        ]);

        return ['token' => $sanctumAccessToken['plainTextToken']];
    }

    public function logout(Request $request): void
    {
        auth()->user()->tokens()->delete();
    }
}
