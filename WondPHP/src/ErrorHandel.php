<?php
namespace WondPHP;

use ErrorException;
use Exception;
use WondPHP\Exceptions\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use WondPHP\Exceptions\FatalError;
use Throwable;

class ErrorHandel
{
    public static $reservedMemory;

    public function __construct()
    {

        self::$reservedMemory = str_repeat('x', 10240);


        error_reporting(-1);

        set_error_handler([$this, 'handleError']);

        set_exception_handler([$this, 'handleException']);

        register_shutdown_function([$this, 'handleShutdown']);

        // if (! $app->environment('testing')) {
        //     ini_set('display_errors', 'Off');
        // }







//todo json请求
        // if (envs('APP_DEBUG')) {
        //     $whoops = new \Whoops\Run;
        //     $whoops->allowQuit(false);
        //     $whoops->writeToOutput(false);
        //     $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        //     $html = $whoops->handleException($e);
        //     $statusCode =http_response_code();
        //     if(substr($statusCode,0,1) ==4  || substr($statusCode,0,1) ==5 ) {
        //         http_response_code($statusCode);
        //     }else{
        //         http_response_code(500);
        //     }

        //     echo $html;
        // } else {
        // set_error_handler(array($this, 'error_handler'));
        // set_exception_handler(array($this, 'exception_handler'));
        // register_shutdown_function(array($this, 'shutdown_function'));
        // }



        

    }

    public function shutdown_function()
    {
        $e = error_get_last();
        print_r($e);
    }

    public function exception_handler($exception)
    {
        // echo " exception: " , $exception->getMessage() .$exception->getLine().$exception->getFile(), "\n";
        error(500, 'ERROR:500,内部服务器错误');
    }

    public function error_handler($severity, $message, $file, $line)
    {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }





    /**
     * Convert PHP errors to ErrorException instances.
     *
     * @param  int  $level
     * @param  string  $message
     * @param  string  $file
     * @param  int  $line
     * @param  array  $context
     * @return void
     *
     * @throws \ErrorException
     */
    public function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Handle an uncaught exception from the application.
     *
     * Note: Most exceptions can be handled via the try / catch block in
     * the HTTP and Console kernels. But, fatal error exceptions must
     * be handled differently since they are not normal exceptions.
     *
     * @param  \Throwable  $e
     * @return void
     */
    public function handleException(Throwable $e)
    {
        try {
            self::$reservedMemory = null;
            $this->getExceptionHandler($e)->report($e);
        } catch (Exception $e) {
            //
        }
        if (0) { //$this->app->runningInConsole()
            $this->renderForConsole($e);
        } else {
            $this->renderHttpResponse($e);
        }
    }

    /**
     * Render an exception to the console.
     *
     * @param  \Throwable  $e
     * @return void
     */
    protected function renderForConsole(Throwable $e)
    {
        $this->getExceptionHandler($e)->renderForConsole(new ConsoleOutput, $e);
    }

    /**
     * Render an exception as an HTTP response and send it.
     *
     * @param  \Throwable  $e
     * @return void
     */
    protected function renderHttpResponse(Throwable $e)
    {
        $this->getExceptionHandler($e)->render(Request::getInstance(), $e)->send();
    }

    /**
     * Handle the PHP shutdown event.
     *
     * @return void
     */
    public function handleShutdown()
    {
        if (! is_null($error = error_get_last()) && $this->isFatal($error['type'])) {
            $this->handleException($this->fatalErrorFromPhpError($error, 0));
        }
    }

    /**
     * Create a new fatal error instance from an error array.
     *
     * @param  array  $error
     * @param  int|null  $traceOffset
     * @return \Symfony\Component\ErrorHandler\Error\FatalError
     */
    protected function fatalErrorFromPhpError(array $error, $traceOffset = null)
    {
        return new FatalError($error['message'], 0, $error, $traceOffset);
    }

    /**
     * Determine if the error type is fatal.
     *
     * @param  int  $type
     * @return bool
     */
    protected function isFatal($type)
    {
        return in_array($type, [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE]);
    }

    /**
     * Get an instance of the exception handler.
     *
     * @return \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected function getExceptionHandler($e)
    {
        return app()->make(ExceptionHandler::class,[$e]);
    }



    public function __destruct()
    {
        restore_error_handler();
        restore_exception_handler();
    }
}
