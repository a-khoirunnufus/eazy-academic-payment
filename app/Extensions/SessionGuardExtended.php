<?php

namespace App\Extensions;

use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;

class SessionGuardExtended extends SessionGuard
{
    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if ($this->loggedOut) {
            return;
        }

        if($this->session->get("userData"))
            return $this->session->get("userData");

        if (! is_null($this->user)) {
            return $this->user;
        }

        $id = $this->session->get($this->getName());

        if (! is_null($id) && $this->user = $this->provider->retrieveById($id)) {
            $this->fireAuthenticatedEvent($this->user);
        }

        if (is_null($this->user) && ! is_null($recaller = $this->recaller())) {
            $this->user = $this->userFromRecaller($recaller);
            if ($this->user) {
                $this->updateSession($this->user->getAuthIdentifier());
                $this->fireLoginEvent($this->user, true);
            }
        }

        $this->session->put("userData", $this->user);

        return $this->user;
    }

    public function login(Authenticatable $user, $remember = false)
    {
        $this->updateSession($user->getAuthIdentifier());

        if ($remember) {
            $this->ensureRememberTokenIsSet($user);

            $this->queueRecallerCookie($user);
        }

        $this->fireLoginEvent($user, $remember);

        $this->setUser($user);
    }

    protected function clearUserDataFromStorage()
    {
        $this->session->remove($this->getName());

        if (! isset($this->cookie)) {
            return;
        }

        $this->getCookieJar()->unqueue($this->getRecallerName());

        if (! is_null($this->recaller())) {
            $this->getCookieJar()->queue(
                $this->getCookieJar()->forget($this->getRecallerName())
            );
        }
    }
}