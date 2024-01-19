<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|string|unique:users,email',// must be unique in the 'users' table's 'email' column
            'password' => [
                'required',
                'confirmed',
                Password::min(8),//Password rule object ->mixedCase()->numbers()->symbols()
            ]
        ]);

        $user = User::create([
            'name' => $data["name"],
            'email' => $data["email"],
            'password' => bcrypt($data["password"]),
        ]);
        //creates a personal access token for the user
        //'main' is a name for the token
        //access the plain-text value of the token using the plainTextToken property of the NewAccessToken instance which wsa returned by the create Token method
        $token = $user->createToken('main')->plainTextToken;
        return response([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|string|exists:users,email',
            'password' => [
                'required',
            ],
            'remember' => 'boolean'
        ]);

        $remember = $credentials['remember'] ?? false;
        //removes the 'remember' key from the $credentials array to avoid passing it to the Auth::attempt method 
        unset($credentials['remember']);
        //the second $remember parameter is true, User wont have to login for quite a long time
        //otherwise, it will extend the session duration
        if(!Auth::attempt($credentials, $remember))
        {
            return response([
                'error' => 'The Provided credentials are not correct'
            ], 422);
        }

        $user = Auth::user();
        $token = $user->createToken('main')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        return response([
            'success' => true
        ]);
    }
}
