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
    private static $instance;
    static function getInstance() {
        if(!isset(static::$instance)){
            static::$instance = new Logger('name');
            static::$instance->pushHandler(new StreamHandler(CONFIG['LOG_PATH'], MonologLogger::WARNING));
        }
        return static::$instance;
    }
}