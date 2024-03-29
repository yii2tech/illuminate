<?php

namespace Yii2tech\Illuminate\Test\Yii\Log;

use Illuminate\Log\Logger as IlluminateLogger;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use yii\log\Logger;
use Yii2tech\Illuminate\Test\TestCase;
use Yii2tech\Illuminate\Yii\Log\Target;

class TargetTest extends TestCase
{
    public function testExport()
    {
        $testLogger = $this->getMockBuilder(AbstractLogger::class)
            ->onlyMethods(['log'])
            ->getMock();

        $logRecords = [];

        $testLogger->method('log')->willReturnCallback(function ($level, $message, $context) use (&$logRecords) {
            $logRecords[] = [
                'level' => $level,
                'message' => $message,
                'context' => $context,
            ];
        });

        $target = (new Target())->setIlluminateLogger(new IlluminateLogger($testLogger));

        $target->messages = [
            ['log message', Logger::LEVEL_TRACE, 'test', 123456789],
        ];

        $target->export();

        $expectedRecord = [
            'level' => LogLevel::DEBUG,
            'message' => 'log message',
            'context' => [
                'time' => 123456789,
                'category' => 'test',
            ],
        ];
        $this->assertEquals($expectedRecord, $logRecords[0]);
    }
}
