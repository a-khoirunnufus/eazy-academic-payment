<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\Authentication\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'email';
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            $this->storeLoginHistory($request->username, 'Success');

            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    protected function attemptLogin(Request $request)
    {
        $username = $request->post($this->username());
        $password = $request->post('password');
        $pass 	  = '$EAZYBANGET$' . substr(md5('$EAZYBANGET$'. $password).md5($password), 0, 50);

        $user = User::where('email', $username)->first();

        if(!$user)
            return $this->sendFailedLoginResponse($request, [
                $this->username() => [trans('auth.failed')]
            ]);

        if($user->password != $pass)
            return $this->sendFailedLoginResponse($request, [
                'password' => [trans('auth.password')]
            ]);

        return Auth::loginUsingId($user->id);;
    }

    protected function sendFailedLoginResponse(Request $request, $messages = null)
    {
        $this->storeLoginHistory($request->username, 'Failed');

        if(is_null($messages))
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.failed')],
            ]);
        else
            throw ValidationException::withMessages($messages);
    }

    protected function storeLoginHistory($username, $status)
    {
        if(env('ENABLE_LOGIN_LOGGING'. false) === false)
            return;

        // store logging proccess here
    }
}
