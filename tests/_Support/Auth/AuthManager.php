<?php

namespace Yii2tech\Illuminate\Test\_Support\Auth;

/**
 * Test AuthManager, which uses mock auth guard.
 *
 * @see \Yii2tech\Illuminate\Test\_Support\Auth\Guard
 */
class AuthManager extends \Illuminate\Auth\AuthManager
{
    /**
     * {@inheritdoc}
     */
    protected function resolve($name)
    {
        return new Guard();
    }
}
