<?php

namespace Yii2tech\Illuminate\Test\_Support\Auth;

use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Mock Auth Guard.
 *
 * @see \Yii2tech\Illuminate\Test\_Support\Auth\AuthManager
 */
class Guard implements StatefulGuard
{
    public $user;

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function guest()
    {
        return $this->user === null;
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function id()
    {
        return $this->user->id;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $credentials = [])
    {
        $row = DB::table('users')->where($credentials)->first();

        return $row !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        $row = DB::table('users')->where($credentials)->first();
        if (! $row) {
            return false;
        }

        $this->user = new GenericUser((array) $row);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function once(array $credentials = [])
    {
        $this->attempt($credentials);
    }

    /**
     * {@inheritdoc}
     */
    public function login(Authenticatable $user, $remember = false)
    {
        $this->setUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loginUsingId($id, $remember = false)
    {
        $row = DB::table('users')->where('id', '=', $id)->first();

        if ($row === null) {
            return null;
        }

        $user = new GenericUser((array) $row);

        $this->setUser($user);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function onceUsingId($id)
    {
        $user = $this->loginUsingId($id);
        if (! $user) {
            return false;
        }

        $this->setUser($user);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function viaRemember()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function logout()
    {
        $this->user = null;
    }
}
