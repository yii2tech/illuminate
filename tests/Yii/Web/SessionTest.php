<?php

namespace Yii2tech\Illuminate\Test\Yii\Web;

use Illuminate\Session\Store;
use Yii2tech\Illuminate\Test\TestCase;
use Illuminate\Session\NullSessionHandler;
use Yii2tech\Illuminate\Yii\Web\Session;

class SessionTest extends TestCase
{
    public function testOperateData()
    {
        $sessionStore = new Store('test', new NullSessionHandler());
        $sessionStore->replace([
            'key' => 'initial',
        ]);

        $session = (new Session())->setIlluminateSession($sessionStore);

        $this->assertSame('initial', $session->get('key'));
        $this->assertSame(null, $session->get('undefined'));
        $this->assertSame('default', $session->get('undefined', 'default'));

        $this->assertTrue($session->has('key'));
        $this->assertFalse($session->has('undefined'));

        $session->set('new', 'new');
        $this->assertSame('new', $session->get('new'));
        $session->set('new', 'update');
        $this->assertSame('update', $session->get('new'));

        $this->assertSame('initial', $session->remove('key'));
        $this->assertFalse($session->has('key'));
        $this->assertSame(null, $session->remove('undefined'));

        $session->removeAll();
        $this->assertFalse($session->has('new'));
        $this->assertSame(0, $session->getCount());
    }
}
