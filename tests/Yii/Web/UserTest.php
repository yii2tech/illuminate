<?php

namespace Yii2tech\Illuminate\Test\Yii\Web;

use Illuminate\Auth\GenericUser;
use Illuminate\Database\Schema\Blueprint;
use Yii2tech\Illuminate\Yii\Web\User;
use Yii2tech\Illuminate\Test\_Support\Auth\AuthManager;
use Yii2tech\Illuminate\Test\_Support\Yii\Models\User as UserModel;
use Yii2tech\Illuminate\Test\TestCase;

class UserTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->createSchema();
        $this->seedData();
    }

    /**
     * Setup the database schema.
     *
     * @return void
     */
    protected function createSchema()
    {
        $this->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
        });
    }

    /**
     * Seeds the database.
     *
     * @return void
     */
    protected function seedData()
    {
        $this->getDbConnection()->table('users')->insert([
            'name' => 'user1',
            'email' => 'user1@example.com',
        ]);
        $this->getDbConnection()->table('users')->insert([
            'name' => 'user2',
            'email' => 'user2@example.com',
        ]);
    }

    public function testGetIdentity()
    {
        $mockUser = new GenericUser(['id' => 2]);
        $authManager = new AuthManager($this->app);
        $authManager->guard()->setUser($mockUser);

        $webUser = (new User(['identityClass' => UserModel::class]))->setIlluminateAuthManager($authManager);

        $identity = $webUser->getIdentity();
        $this->assertEquals($mockUser->getAuthIdentifier(), $identity->id);
    }

    public function testSwitchIdentity()
    {
        $mockUser = new GenericUser(['id' => 2]);
        $authManager = new AuthManager($this->app);
        $authManager->guard()->setUser($mockUser);

        $webUser = (new User(['identityClass' => UserModel::class]))->setIlluminateAuthManager($authManager);

        $identity = UserModel::findOne(1);
        $webUser->switchIdentity($identity);

        $this->assertEquals($identity->id, $authManager->guard()->user()->id);
    }

    public function testUnsetIdentiry()
    {
        $mockUser = new GenericUser(['id' => 2]);
        $authManager = new AuthManager($this->app);
        $authManager->guard()->setUser($mockUser);

        $webUser = (new User(['identityClass' => UserModel::class]))->setIlluminateAuthManager($authManager);

        $webUser->switchIdentity(null);

        $this->assertNull($authManager->guard()->user());
    }
}
