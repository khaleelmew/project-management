<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    use ApiResponse;
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255', 
            'last_name'  => 'required|string|max:255', 
            'email'      => 'required|email|unique:users,email', 
            'password'   => 'required|string|min:8|confirmed', 
        ]);
        if ($validator->fails()) {
           
            return $this->error_response('Register Error',$validator->errors());
        }
        // dd([
        //     'first_name' => $request->first_name,
        //     'last_name'  => $request->last_name,
        //     'email'      => $request->email,
        //     'password'   => Hash::make($request->password), 
        // ]);
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password), 
        ]);
        $token = $user->createToken('Access Token')->accessToken;
        return $this->success_response('Register Success',['token'=>$token,'user'=>$user]);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
          
            'email'      => 'required|email', 
            'password'   => 'required', 
        ]);
        if ($validator->fails()) {
           
            return $this->error_response('Register Error',$validator->errors());
        }
        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error_response('Invalid email or password');
        }
        $user = Auth::user();
        $token = $user->createToken('Access Token')->accessToken;

        return $this->success_response('Login Success',['token'=>$token,'user'=>$user]);
    }
    public function logout(Request $request){
       
        $user=Auth::user();
        $user->token()->revoke();
        return $this->success_response('Logout Success');
    }
    
}
