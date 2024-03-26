<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */


    /**
     * go to index login
     */
    public function index() {
        return view('auth.login');
    }

    /** its for login user 
     * 
     * @param \Illuminate\Http\JsonResponse
     */

     public function login (Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'password' => 'required|string|min:6',
            ]);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            //check if the user exists 
            $user = User::where('name', $request->name)->first();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            
            // check if the password is correct
            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['error' => 'Password is incorrect'], 401);
            }

            try {
                //attemp verify to get token 
               $credential = $request->only('name','password');
               if (!$token = JWTAuth::attempt($credential)) {
                // If the login attempt fails
                Log::info('Login failed: Invalid credentials', ['name' => $request->input('name')]);
                return response()->json(['error' => 'Invalid credentials'], 401);

                //get user auth 

                $user = JWTAuth::user();

                //cehck if the user is active
                if (!$user->active) {
                    return response()->json(['error' => 'User is not active'], 401);
                } else {
                    // If the login attempt is successful
                    Log::info('User logged in', ['name' => $request->input('name')]);
                    return response()->json(['message' => 'Login Successfully', 'token' => $token, 'name' => $user->name], 200);
                }
            }
            } catch (JWTException $e) {
                return response()->json(['error' => 'Could not create token'], 500);
            }
            return response()->view('home');
        } catch (\Throwable $th) {
           Log::info('User Login Failed', ['error' => $th->getMessage()]);
           return response()->json(['error' => 'User Login Failed'], 500);
        }
}
}