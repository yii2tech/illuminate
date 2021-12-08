<?php

namespace Yii2tech\Illuminate\Test\Yii\Log;

use Illuminate\Log\Logger as IlluminateLogger;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Yii2tech\Illuminate\Test\TestCase;
use Yii2tech\Illuminate\Yii\Log\Logger;

class LoggerTest extends TestCase
{
    public function testLog()
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

        $logger = (new Logger())->setIlluminateLogger(new IlluminateLogger($testLogger));

        $logger->log('log message', Logger::LEVEL_TRACE, 'test');

        $expectedRecord = [
            'level' => LogLevel::DEBUG,
            'message' => 'log message',
            'context' => [
                'category' => 'test',
            ],
        ];
        $this->assertEquals($expectedRecord, $logRecords[0]);
    }
}
