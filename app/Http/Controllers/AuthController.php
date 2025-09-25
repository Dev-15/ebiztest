<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function register(Request $r)
    {
        $r->validate([ 'name' => 'required', 'email' => 'required|email|unique:users', 'password' => 'required|min:8' ]);
        $pepper = config('app.password_pepper', env('APP_PASSWORD_PEPPER'));
        $hashed = password_hash($r->password . $pepper, PASSWORD_ARGON2ID);
        $user = User::create(['name'=>$r->name,'email'=>$r->email,'password'=>$hashed]);
        $token = $user->createToken('api')->plainTextToken;
        return response()->json(['token'=>$token,'user'=>$user],201);
    }

    public function login(Request $r)
    {
        $r->validate(['email'=>'required|email','password'=>'required']);
        $key = 'login:'.$r->ip();
        $max = (int) env('AUTH_MAX_ATTEMPTS',5);
        $decay = (int) env('AUTH_LOCKOUT_MINUTES',15);
        if (RateLimiter::tooManyAttempts($key, $max)) {
            return response()->json(['message'=>'Too many attempts'],429);
        }
        $user = User::where('email',$r->email)->first();
        $pepper = config('app.password_pepper', env('APP_PASSWORD_PEPPER'));
        if (!$user || !password_verify($r->password . $pepper, $user->password)) {
            RateLimiter::hit($key, $decay * 60);
            return response()->json(['message'=>'Invalid credentials'],401);
        }
        RateLimiter::clear($key);
        $token = $user->createToken('api')->plainTextToken;
        return response()->json(['token'=>$token,'user'=>$user]);
    }
}
