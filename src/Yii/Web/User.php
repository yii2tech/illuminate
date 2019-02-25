<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Yii2tech\Illuminate\Yii\Web;

use RuntimeException;
use yii\db\BaseActiveRecord;
use yii\web\IdentityInterface;
use Illuminate\Auth\AuthManager;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;

/**
 * User allows usage of the Laravel guard for authenticated user tracking.
 *
 * @see \Illuminate\Auth\AuthManager
 *
 * @property \Illuminate\Auth\AuthManager $laravelAuthManager related Laravel auth manager.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class User extends \yii\web\User
{
    /**
     * @var string|null guard to be used while retrieving identity from Laravel auth manager.
     */
    public $guard;

    /**
     * @var \yii\web\IdentityInterface|bool user identity.
     */
    private $_identity = false;

    /**
     * @var \Illuminate\Auth\AuthManager related Laravel auth manager.
     */
    private $_laravelAuthManager;

    /**
     * {@inheritdoc}
     */
    public function getIdentity($autoRenew = true)
    {
        if ($this->_identity === false) {
            $identity = $this->getLaravelAuthManager()->guard($this->guard)->user();
            if ($identity !== null) {
                $identity = $this->convertLaravelIdentity($identity);
            }

            $this->_identity = $identity;
        }

        return $this->_identity;
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentity($identity): void
    {
        parent::setIdentity($identity);

        $this->_identity = $identity;
    }

    /**
     * @return \Illuminate\Auth\AuthManager
     */
    public function getLaravelAuthManager(): AuthManager
    {
        if ($this->_laravelAuthManager === null) {
            $this->_laravelAuthManager = $this->defaultLaravelRequest();
        }

        return $this->_laravelAuthManager;
    }

    /**
     * @param  \Illuminate\Auth\AuthManager  $authManager
     * @return static self reference.
     */
    public function setLaravelAuthManager(AuthManager $authManager): self
    {
        $this->_laravelAuthManager = $authManager;

        return $this;
    }

    /**
     * @return \Illuminate\Auth\AuthManager default Laravel auth manager.
     */
    protected function defaultLaravelRequest(): AuthManager
    {
        return Container::getInstance()->make('auth');
    }

    /**
     * {@inheritdoc}
     */
    public function switchIdentity($identity, $duration = 0): void
    {
        $this->setIdentity($identity);

        if ($identity === null) {
            $this->getLaravelAuthManager()->guard($this->guard)->logout();

            return;
        }

        if ($identity instanceof BaseActiveRecord) {
            $id = $identity->getPrimaryKey();
        } else {
            $id = $identity->id;
        }

        $this->getLaravelAuthManager()->guard($this->guard)->loginUsingId($id);
    }

    /**
     * Converts Laravel identity into Yii one.
     *
     * @param  mixed  $identity Laravel identity.
     * @return IdentityInterface Yii compatible identity instance.
     */
    protected function convertLaravelIdentity($identity): IdentityInterface
    {
        if ($identity instanceof Model) {
            $id = $identity->getKey();
            $attributes = $identity->getAttributes();
        } elseif (is_array($identity) && isset($identity['id'])) {
            $id = $identity['id'];
            $attributes = $identity;
        } else {
            throw new RuntimeException('Unable to convert identity from "'.gettype($identity).'"');
        }

        $identityClass = $this->identityClass;
        if (is_subclass_of($identityClass, BaseActiveRecord::class)) {
            $record = new $identityClass;
            call_user_func([$identityClass, 'populateRecord'], $record, $attributes);

            return $record;
        }

        return call_user_func([$identityClass, 'findIdentity'], $id);
    }
}
