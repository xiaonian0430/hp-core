<?php
/**
 * @author: Xiao Nian
 * @contact: xiaonian030@163.com
 * @datetime: 2019-12-01 14:00
 */
namespace HP\Log;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;

 class Logger {
    public static $log;

    public function __construct() {
        if(!isset(static::$log)){
            static::$log = new Logger('name');
            static::$log->pushHandler(new StreamHandler(CONFIG['LOG_PATH'], MonologLogger::WARNING));
        }
    }
}