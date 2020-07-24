<?php

namespace Yii2tech\Illuminate\Test\Yii\Web;

use Illuminate\Http\Request as IlluminateRequest;
use yii\web\HeaderCollection;
use Yii2tech\Illuminate\Test\TestCase;
use Yii2tech\Illuminate\Yii\Web\Request;

class RequestTest extends TestCase
{
    public function testGetHeaders()
    {
        $illuminateRequest = IlluminateRequest::create('http://example.test');
        $illuminateRequest->headers->add([
            'header1' => [
                'value1.1',
                'value1.2',
            ],
            'header2' => [
                'value2.1',
                'value2.2',
            ],
        ]);

        $request = (new Request())->setIlluminateRequest($illuminateRequest);

        $headers = $request->getHeaders();
        $this->assertTrue($headers instanceof HeaderCollection);

        $this->assertEquals($illuminateRequest->headers->all(), $headers->toArray());
    }

    public function testGetMethod()
    {
        $illuminateRequest = IlluminateRequest::create('http://example.test', 'PATCH');

        $request = (new Request())->setIlluminateRequest($illuminateRequest);

        $this->assertSame('PATCH', $request->getMethod());
    }

    public function testGetRawBody()
    {
        $illuminateRequest = IlluminateRequest::create('http://example.test', 'POST', [], [], [], [], 'Test content');

        $request = (new Request())->setIlluminateRequest($illuminateRequest);

        $this->assertSame('Test content', $request->getRawBody());
    }

    public function testGetBodyParamsJson()
    {
        $data = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];

        $illuminateRequest = IlluminateRequest::create('http://example.test', 'POST', [], [], [], [], json_encode($data));
        $illuminateRequest->headers->add(['content-type' => ['application/json']]);

        $request = (new Request())->setIlluminateRequest($illuminateRequest);

        $this->assertEquals($data, $request->getBodyParams());
    }

    public function testGetBodyParamsFormData()
    {
        $data = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];

        $illuminateRequest = IlluminateRequest::create('http://example.test', 'PUT', $data);
        $illuminateRequest->headers->add(['content-type' => ['application/x-www-form-urlencoded']]);

        $request = (new Request())->setIlluminateRequest($illuminateRequest);

        $this->assertEquals($data, $request->getBodyParams());
    }

    public function testGetHostInfo()
    {
        $illuminateRequest = IlluminateRequest::create('http://example.test');

        $request = (new Request())->setIlluminateRequest($illuminateRequest);

        $this->assertSame('http://example.test', $request->getHostInfo());
    }

    public function testGetScriptUrl()
    {
        $illuminateRequest = IlluminateRequest::create('http://example.test');

        $request = (new Request())->setIlluminateRequest($illuminateRequest);

        $this->assertSame('/index.php', $request->getScriptUrl());
    }

    public function testGetBaseUrl()
    {
        $illuminateRequest = IlluminateRequest::create('http://example.test');

        $request = (new Request())->setIlluminateRequest($illuminateRequest);

        $this->assertSame('', $request->getBaseUrl());
    }

    public function testGetUrl()
    {
        $illuminateRequest = IlluminateRequest::create('http://example.test/some');

        $request = (new Request())->setIlluminateRequest($illuminateRequest);

        $this->assertSame('/some', $request->getUrl());
    }

    public function testGetPathInfo()
    {
        $illuminateRequest = IlluminateRequest::create('http://example.test');

        $request = (new Request())->setIlluminateRequest($illuminateRequest);

        $this->assertSame('', $request->getPathInfo());
    }
}
