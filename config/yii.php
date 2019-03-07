<?php
/**
 * Configuration for Yii2 to Laravel Migration Package.
 */

return [
    /**
     * Configuration for Yii application middleware.
     *
     * @see \Yii2tech\Illuminate\Http\YiiApplicationMiddleware
     * @see \Illuminatech\ArrayFactory\FactoryContract
     */
    'middleware' => [
        'defaultEntryScript' => 'legacy/web/index.php',
        'cleanup' => true,
        //'bootstrap' => 'config/bootstrap.php',
        /*'container' => [
            '__class' => Yii2tech\Illuminate\Yii\Di\Container::class,
        ],*/
        /*'logger' => [
            '__class' => Yii2tech\Illuminate\Yii\Log\Logger::class,
        ],*/
    ],
];
