<?php

namespace Yii2tech\Illuminate\Test\Yii\Log;

use Illuminate\Log\Logger as IlluminateLogger;
use Psr\Log\LogLevel;
use Psr\Log\Test\TestLogger;
use yii\log\Logger;
use Yii2tech\Illuminate\Test\TestCase;
use Yii2tech\Illuminate\Yii\Log\Target;

class TargetTest extends TestCase
{
    public function testExport()
    {
        $testLogger = new TestLogger();

        $target = (new Target())->setIlluminateLogger(new IlluminateLogger($testLogger));

        $target->messages = [
            ['log message', Logger::LEVEL_TRACE, 'test', 123456789],
        ];

        $target->export();

        $expectedRecord = [
            'message' => 'log message',
            'context' => [
                'time' => 123456789,
                'category' => 'test',
            ],
        ];
        $this->assertTrue($testLogger->hasRecord($expectedRecord, LogLevel::DEBUG));
    }
}
