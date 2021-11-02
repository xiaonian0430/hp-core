<?php
/**
 * @author: Xiao Nian
 * @contact: xiaonian030@163.com
 * @datetime: 2019-12-01 14:00
 */
namespace HP;
use Workerman\Worker;
use Workerman\Protocols\Http\Request;
use Workerman\Connection\TcpConnection;
class Core {

    public function __construct() {
        defined('IN_PHAR') or define('IN_PHAR', boolval(\Phar::running(false)));
        defined('SERVER_ROOT') or define('SERVER_ROOT', IN_PHAR ? \Phar::running() : realpath(getcwd()));
        defined('PUBLIC_ROOT') or define('PUBLIC_ROOT', SERVER_ROOT.'/public'); //不能加后缀

        //创建临时目录
        $temp_path=SERVER_ROOT.'/temp';
        $log_path=SERVER_ROOT.'/temp/log';
        if(!is_dir($log_path)){
            mkdir($log_path, 0777, true);
        }

        // 检查扩展或环境
        if(strpos(strtolower(PHP_OS), 'win') === 0) {
            exit("not support windows.\n");
        }
        if(!extension_loaded('pcntl')) {
            exit("Please install pcntl extension.\n");
        }
        if(!extension_loaded('posix')) {
            exit("Please install posix extension.\n");
        }

        //导入配置文件
        global $argv;
        $mode='produce';
        foreach ($argv as $item){
            $item_val=explode('=', $item);
            if(count($item_val)==2 && $item_val[0]=='-mode'){
                $mode=$item_val[1];
            }
        }
        $config_path=SERVER_ROOT . '/config/'.$mode.'.php';
        if (file_exists($config_path)) {
            $conf = require_once $config_path;
        }else{
            exit($config_path." is not exist\n");
        }
        defined('CONFIG') or define('CONFIG', $conf);

        //初始化worker
        Worker::$stdoutFile = $log_path.'/error.log';
        Worker::$logFile = $log_path.'/log.log';
        Worker::$pidFile = $temp_path.'/pid.pid';
    }
}
