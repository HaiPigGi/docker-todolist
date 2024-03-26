<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
class LogoutController extends Controller
{
    /**
     * its for logout user
     */
    public function logout ()
    {
        try {
            //get auth user
            $token = JWTAuth::getToken();

            if (!$token) {
                return response()->json(['error' => 'Token not provided'], 400);
            }

            // check token to user

            $user = JWTAuth::toUser($token);

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
    
            }

            //logout user
            JWTAuth::invalidate($token);

            //return response
            return response()->json(['message' => 'User has been logged out'], 200);
        } catch (JWTException $e) {
            Log::error('Failed to logout, please try again.', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to logout, please try again.'], 500);
        }
    }
     
}