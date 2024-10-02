<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /** register new account */
    public function register(RegisterUserRequest $request)
    {
        $user = new User();
        $user->name         = $request->name ;
        $user->email        = $request->email;
        $user->password     = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success'    => true,
            'message'    => 'Success register',
        ], 201);
    }

    /**
     * Login Req
     */
    public function login(Request $request)
    {   
        try {

            $email     = $request->email;
            $password  = $request->password;

            if (Auth::attempt(['email' => $email,'password' => $password])) 
            {
                /** @var \App\Models\User */
                $user = Auth::User();
                $tokenResult = $user->createToken($user->email);
    
                return response()->json([
                    'success'    => true,
                    'message'    => 'Success login',
                    'user'       => $user,
                    'token'      => $tokenResult->accessToken,
                    'expires_at' => Carbon::parse(
                                        $tokenResult->token->expires_at
                                    )->toDateTimeString()
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Incorrect credentials'
                ], 401);
            }
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function unauthenticated()
    {
        return response()->json([
            "status" => false,
            "message" => "Unauthenticated",
        ], 401);
    }
}
