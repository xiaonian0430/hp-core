<?php
/**
 * @author: Xiao Nian
 * @contact: xiaonian030@163.com
 * @datetime: 2019-12-01 14:00
 */
namespace HP;
use Workerman\Worker;
class Core {

    public function __construct() {
        ini_set('display_errors', 'on');
        ini_set('date.timezone','Asia/Shanghai');
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
        //EVENT_LOOP SWOOLE 1 其他 0
        $conf_frame=[
            'LOG_PATH'=>$log_path.'/app.log',
            'EVENT_LOOP'=>1,
            'HTTP_SERVER'    => [
                'SERVER_NAME'    => 'HTTP_SERVER',
                'PROCESS_COUNT'     => 4,  //进程数
                'LISTEN_ADDRESS' => '0.0.0.0',
                'PORT'           => 5151
            ],
            'REGISTER'    => [
                'SERVER_NAME'    => 'RegisterCenter',
                'LISTEN_ADDRESS' => '0.0.0.0',
                'PORT'           => 1236,
                'LAN_IP'         => '127.0.0.1',
                'LAN_PORT'       => 1236
            ],
            'GATEWAY'    => [
                'SERVER_NAME'    => 'ChatGateway',
                'LISTEN_ADDRESS' => '0.0.0.0',
                'PORT'           => 7272,
                'PROCESS_COUNT'     => 4,  //进程数
                'PING_INTERVAL'  => 10,  //心跳间隔
                'LAN_IP'         => '127.0.0.1', //分布式部署时请设置成内网ip（非127.0.0.1）
                'LAN_START_PORT'     => 2300, //内部通讯起始端口
            ],
            'BUSINESS'    => [
                'SERVER_NAME'    => 'BusinessWorker',
                'PROCESS_COUNT'     => 4,  //进程数
                'EVENT_HANDLER'       => 'Events'
            ],
            'TASK'    => [
                'SERVER_NAME'    => 'TaskProcess',
                'PROCESS_COUNT'     => 1,  //进程数
            ]
        ];
        defined('CONFIG') or define('CONFIG', array_merge($conf_frame, $conf));

        //初始化worker
        Worker::$stdoutFile = $log_path.'/error.log';
        Worker::$logFile = $log_path.'/log.log';
        Worker::$pidFile = $temp_path.'/pid.pid';

        //添加swoole轮询事件
        if(CONFIG['EVENT_LOOP']==1){
            Worker::$eventLoopClass = 'Workerman\Events\Swoole';
        }
    }

    public function run($run_able=true, ?callable $callback = null){
        //实例化
    }
}
