Error Handling
==============

`\Yii2tech\Illuminate\Http\YiiApplicationMiddleware` disables Yii error handler, allowing all PHP errors and exceptions
to be handled by Laravel. It automatically converts `\yii\web\HttpException` exceptions into `\Symfony\Component\HttpKernel\Exception\HttpException`,
allowing triggering of 404, 503 and so on HTTP errors in the same way. It relies on receiving `\yii\web\HttpException`
with 404 code as an indicator, that request handling should be passed forward to Laravel.

Make sure you do not enable Yii error handler manually and do not register any other error handler except the one, provided
by Laravel application.

In case you used custom views for HTTP error rendering in Yii, you should convert them to [Laravel ones](https://laravel.com/docs/10.x/errors#custom-http-error-pages).

In case you used your own error handler with some custom logic around particular exception processing, you should transfer
this logic to the error handler, provided by Laravel.
