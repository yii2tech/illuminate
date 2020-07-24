<?php

namespace Yii2tech\Illuminate\Test\Yii\Security;

use Illuminate\Hashing\BcryptHasher;
use yii\base\Security;
use Yii2tech\Illuminate\Test\TestCase;

class PasswordHashTest extends TestCase
{
    public function testPasswordHash()
    {
        $yiiSecurity = new Security(['passwordHashCost' => 4]);
        $illuminateHasher = new BcryptHasher(['rounds' => 4]);

        $password = 'secret';

        $yiiHash = $yiiSecurity->generatePasswordHash($password);

        $illuminateHash = $illuminateHasher->make($password);

        $this->assertTrue($illuminateHasher->check($password, $yiiHash));
        $this->assertTrue($yiiSecurity->validatePassword($password, $illuminateHash));
    }
}
