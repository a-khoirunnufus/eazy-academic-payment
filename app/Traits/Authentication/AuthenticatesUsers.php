<?php

namespace App\Traits\Authentication;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers as AuthenticatesUsersMaster;
use App\Traits\HasHomepage;

trait AuthenticatesUsers
{
    use AuthenticatesUsersMaster, HasHomepage;

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        if($request->wantsJson()){
            return response()->json([
                'redirectURL' => $this->getHompagePath()
            ]);
        } else {
            return redirect($this->getHompagePath());
        }
    }
}
