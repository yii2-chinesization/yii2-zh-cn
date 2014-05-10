<?php
/**
 * 翻译日期：20140510
 */

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\base;

use Yii;
use yii\web\HttpException;

/**
 * ErrorHandler （错误处理器，又称为错误处理句柄）处理未捕获的 PHP 错误和异常
 *
 * ErrorHandler 默认在[[\yii\base\Application]]配置为一个应用组件，
 * 你可以通过`Yii::$app->errorHandler`来访问该实例。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Alexander Makarov <sam@rmcreative.ru>
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
abstract class ErrorHandler extends Component
{
    /**
     * @var boolean 是否在错误显示前丢弃任何现有的页面输出，默认为 true
     */
    public $discardExistingOutput = true;
    /**
     * @var integer 保留的内存大小，一部分内存已预先分配，这样当出现内存不足的问题时，
     * 错误处理器才能在预留内存的帮助下处理错误。如果该值设置为 0 ，将没有内存被保留。默认为 256KB
     */
    public $memoryReserveSize = 262144;
    /**
     * @var \Exception 当前正在处理的异常
     */
    public $exception;

    /**
     * @var string 用来为致命错误处理器预留内存
     */
    private $_memoryReserve;


    /**
     * 注册此错误处理器
     */
    public function register()
    {
        ini_set('display_errors', false);
        set_exception_handler([$this, 'handleException']);
        set_error_handler([$this, 'handleError']);
        if ($this->memoryReserveSize > 0) {
            $this->_memoryReserve = str_repeat('x', $this->memoryReserveSize);
        }
        register_shutdown_function([$this, 'handleFatalError']);
    }

    /**
     * 处理未捕获的 PHP 异常
     *
     * 此方法被实现为 PHP 异常处理器
     *
     * @param \Exception $exception 未捕获的异常
     */
    public function handleException($exception)
    {
        if ($exception instanceof ExitException) {
            return;
        }

        $this->exception = $exception;

        // disable error capturing to avoid recursive errors while handling exceptions
        restore_error_handler();
        restore_exception_handler();
        try {
            $this->logException($exception);
            if ($this->discardExistingOutput) {
                $this->clearOutput();
            }
            $this->renderException($exception);
            if (!YII_ENV_TEST) {
                exit(1);
            }
        } catch (\Exception $e) {
            // an other exception could be thrown while displaying the exception
            $msg = (string) $e;
            $msg .= "\nPrevious exception:\n";
            $msg .= (string) $exception;
            if (YII_DEBUG) {
                if (PHP_SAPI === 'cli') {
                    echo $msg . "\n";
                } else {
                    echo '<pre>' . htmlspecialchars($msg, ENT_QUOTES, Yii::$app->charset) . '</pre>';
                }
            }
            $msg .= "\n\$_SERVER = " . var_export($_SERVER, true);
            error_log($msg);
            exit(1);
        }

        $this->exception = null;
    }

    /**
     * 处理 PHP 执行错误，如警告和注意
     *
     * 此方法用作 PHP 错误处理器，它将简单地引发一个[[ErrorException]].
     *
     * @param integer $code 引发的错误级别
     * @param string $message 错误消息
     * @param string $file 引发错误的文件名
     * @param integer $line 引发错误的行号
     *
     * @throws ErrorException
     */
    public function handleError($code, $message, $file, $line)
    {
        if (error_reporting() & $code) {
            // load ErrorException manually here because autoloading them will not work
            // when error occurs while autoloading a class
            if (!class_exists('yii\\base\\ErrorException', false)) {
                require_once(__DIR__ . '/ErrorException.php');
            }
            $exception = new ErrorException($message, $code, $code, $file, $line);

            // in case error appeared in __toString method we can't throw any exception
            $trace = debug_backtrace(0);
            array_shift($trace);
            foreach ($trace as $frame) {
                if ($frame['function'] == '__toString') {
                    $this->handleException($exception);
                    exit(1);
                }
            }

            throw $exception;
        }
    }

    /**
     * 处理 PHP 致命错误
     */
    public function handleFatalError()
    {
        unset($this->_memoryReserve);

        // load ErrorException manually here because autoloading them will not work
        // when error occurs while autoloading a class
        if (!class_exists('yii\\base\\ErrorException', false)) {
            require_once(__DIR__ . '/ErrorException.php');
        }

        $error = error_get_last();

        if (ErrorException::isFatalError($error)) {
            $exception = new ErrorException($error['message'], $error['type'], $error['type'], $error['file'], $error['line']);
            $this->exception = $exception;
            // use error_log because it's too late to use Yii log
            // also do not log when on CLI SAPI because message will be sent to STDERR which has already been done by PHP
            PHP_SAPI === 'cli' or error_log($exception);

            if ($this->discardExistingOutput) {
                $this->clearOutput();
            }
            $this->renderException($exception);
            exit(1);
        }
    }

    /**
     * 渲染异常
     * @param \Exception $exception 要渲染的异常
     */
    abstract protected function renderException($exception);

    /**
     * 记录给定异常
     * @param \Exception $exception 要记录的异常
     */
    protected function logException($exception)
    {
        $category = get_class($exception);
        if ($exception instanceof HttpException) {
            $category = 'yii\\web\\HttpException:' . $exception->statusCode;
        } elseif ($exception instanceof \ErrorException) {
            $category .= ':' . $exception->getSeverity();
        }
        Yii::error((string) $exception, $category);
    }

    /**
     * 清除调用此方法前的所有输出
     */
    public function clearOutput()
    {
        // the following manual level counting is to deal with zlib.output_compression set to On
        for ($level = ob_get_level(); $level > 0; --$level) {
            if (!@ob_end_clean()) {
                ob_clean();
            }
        }
    }

    /**
     * 将异常转变为 PHP 错误
     *
     * 此方法用于将类似`__toString()`方法内的异常转变为 PHP 错误，因为异常不能在它们内部抛出。
     * @param \Exception $exception 要转变为 PHP 错误的异常
     */
    public static function convertExceptionToError($exception)
    {
        trigger_error(static::convertExceptionToString($exception), E_USER_ERROR);
    }

    /**
     * 把异常转变为简单的字符串
     * @param \Exception $exception 要转换的异常
     * @return string 代表异常的字符串
     */
    public static function convertExceptionToString($exception)
    {
        if ($exception instanceof Exception && ($exception instanceof UserException || !YII_DEBUG)) {
            $message = "{$exception->getName()}: {$exception->getMessage()}";
        } elseif (YII_DEBUG) {
            if ($exception instanceof Exception) {
                $message = "Exception ({$exception->getName()})";
            } elseif ($exception instanceof ErrorException) {
                $message = "{$exception->getName()}";
            } else {
                $message = 'Exception';
            }
            $message .= " '" . get_class($exception) . "' with message '{$exception->getMessage()}' \n\nin "
                . $exception->getFile() . ':' . $exception->getLine() . "\n\n"
                . "Stack trace:\n" . $exception->getTraceAsString();
        } else {
            $message = 'Error: ' . $exception->getMessage();
        }
        return $message;
    }
}
