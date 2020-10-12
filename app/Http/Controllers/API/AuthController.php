<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\LoginRequest;
use App\Http\Requests\API\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth.api', [
            'except' => [
                'register',
                'login',
            ]
        ]);
    }


    public function register(RegisterRequest $request)
    {
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
            mkdir("/opt/myproject/{$user->email}", 0777, true);
            $token = auth()->login($user);
            $cookie = $this->getCookieDetails($token);
            $response = rest(true, $this->payloadToken($token, $user));
            $response->cookie(
                $cookie['name'],
                $cookie['value'],
                $cookie['minutes'],
                $cookie['path'],
                $cookie['domain'],
                $cookie['secure'],
                $cookie['httponly'],
                $cookie['samesite']
            );
            $header = $request->header('Accept');
            return $response;
        } catch (\Exception $e) {
            return rest(false, [], $e);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->email)
                ->first();
            if (!$user) {
                throw new \Exception("Unauthorized", 401);
            }
            $password = $request->post('password', null);
            if (!$password || !Hash::check($password, $user->password)) {
                throw new \Exception("Unauthorized", 401);
            }
            $token = auth()->login($user);
            $cookie = $this->getCookieDetails($token);
            $response = rest(true, $this->payloadToken($token, $user));
            $response->cookie(
                $cookie['name'],
                $cookie['value'],
                $cookie['minutes'],
                $cookie['path'],
                $cookie['domain'],
                $cookie['secure'],
                $cookie['httponly'],
                $cookie['samesite']
            );
            $header = $request->header('Accept');
            return $response;
        } catch (\Exception $e) {
            return rest(false, [], $e);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return rest(true, auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        $cookie = Cookie::forget('_token');
        return rest(true)->withCookie($cookie);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return rest(true, $this->payloadToken(auth()->refresh()));
    }

    private function getCookieDetails($token)
    {
        return [
            'name' => '_token',
            'value' => $token,
            'minutes' => auth()->factory()->getTTL() * 60,
            'path' => null,
            'domain' => null,
            // 'secure' => true, // for production
            'secure' => null, // for localhost
            'httponly' => true,
            'samesite' => true,
        ];
    }

    protected function payloadToken($token, $user)
    {
        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }
}
