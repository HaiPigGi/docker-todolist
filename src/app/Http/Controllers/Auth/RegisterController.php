<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Exceptions\JWTException;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

   
     /**
      * go to index register
      */

      public function index() {
          return view('auth.register');
      }

       /**
     * its for Registration user
     *
     * @param \Illuminate\Http\JsonResponse
     */
    /**
     * its for Registration user
     *
     * @param \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            // Validasi menggunakan Validator
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'min:3'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            // Log data registrasi
            Log::info('Data registrasi:', $request->all());

            // Jika validasi gagal
            if ($validator->fails()) {
                Log::warning('Registration validation failed', ['errors' => $validator->errors()]);
                return response()->json(['error' => $validator->errors()], 400);
            }

            // Check if the password meets the minimum length requirement
            if (strlen($request->name) < 3) {
                Log::warning('Registration failed: Name must be at least 3 characters long');
                return response()->json(['error' => 'Name must be at least 3 characters long'], 400);
            }

            // Check if the username already exists
            $cekName = User::where('name', $request->name)->first();

            if ($cekName) {
                Log::warning('Registration failed: Username already exists', ['username' => $request->name]);
                return response()->json(['error' => 'Username already exists'], 400);
            }

            // Check if the password meets the minimum length requirement
            if (strlen($request->password) < 8) {
                Log::warning('Registration failed: Password must be at least 8 characters long');
                return response()->json(['error' => 'Password must be at least 8 characters long'], 400);
            }

            // Check if the password and password confirmation match
            if ($request->password !== $request->password_confirmation) {
                Log::warning('Registration failed: Password and password confirmation do not match');
                return response()->json(['error' => 'Password and password confirmation do not match'], 400);
            }

            DB::beginTransaction();

            // Create a new user instance
            $user = User::create([
                'name' =>  $request->name,
                'password' => Hash::make($request->password),
            ]);

            // Optionally generate a JWT token for the newly registered user
            $token = JWTAuth::fromUser($user);
            Log::info('Registration successful', ['user_id' => $user->id, 'username' => $user->name, 'token' => $token]);

            // Commit the transaction
            DB::commit();

            // Return a JSON response
            return response()->json([
                'message' => 'Successfully created account',
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            // An error occurred, rollback the transaction
            DB::rollBack();

            // Log the error
            Log::error('Registration failed', ['error' => $e->getMessage()]);

            // Return an error response in JSON format
            return response()->json(['error' => 'Registration failed: ' . $e->getMessage()], 500);
        }
    }

}