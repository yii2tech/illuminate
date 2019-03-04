<?php

namespace Yii2tech\Illuminate\Test\Yii\Log;

use Psr\Log\LogLevel;
use Psr\Log\Test\TestLogger;
use Yii2tech\Illuminate\Test\TestCase;
use Yii2tech\Illuminate\Yii\Log\Logger;
use Illuminate\Log\Logger as IlluminateLogger;

class LoggerTest extends TestCase
{
    public function testLog()
    {
        $testLogger = new TestLogger();

        $logger = (new Logger())->setIlluminateLogger(new IlluminateLogger($testLogger));

        $logger->log('log message', Logger::LEVEL_TRACE, 'test');

        $expectedRecord = [
            'message' => 'log message',
            'context' => [
                'category' => 'test',
            ],
        ];
        $this->assertTrue($testLogger->hasRecord($expectedRecord, LogLevel::DEBUG));
    }
}
