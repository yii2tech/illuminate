<?php

namespace Yii2tech\Illuminate\Test\Yii\Web;

use Yii;
use yii\web\Request;
use Yii2tech\Illuminate\Test\TestCase;
use Yii2tech\Illuminate\Yii\Web\Response;

class ResponseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Yii::$app->set('request', [
            'class' => Request::class,
            'enableCookieValidation' => false,
        ]);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSend()
    {
        $response = new Response();

        $response->setStatusCode(422, 'Form Validation Error');
        $response->content = 'Validation Error Content';
        $response->getHeaders()->add('some-header', 'some-value');

        $response->send();

        $illuminateResponse = $response->getIlluminateResponse();

        $this->assertSame(422, $illuminateResponse->getStatusCode());
        $this->assertSame($response->content, $illuminateResponse->getContent());
        $this->assertSame('some-value', $illuminateResponse->headers->get('some-header'));
    }
}
