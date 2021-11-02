<?php
/**
 * @author: Xiao Nian
 * @contact: xiaonian030@163.com
 * @datetime: 2019-12-01 14:00
 */
namespace HP\BusinessWorker;
use HP\Core;
use Workerman\Worker;
use GatewayWorker\BusinessWorker;
class App extends Core {

    public function run(){
        //实例化
        $business = new BusinessWorker();

        // worker名称
        $business->name = CONFIG['BUSINESS']['SERVER_NAME'];

        // businessWorker进程数量
        $business->count = CONFIG['BUSINESS']['PROCESS_COUNT'];

        // 服务注册地址
        $registerAddress=CONFIG['REGISTER']['LAN_IP'].':'.CONFIG['REGISTER']['LAN_PORT'];
        $business->registerAddress = $registerAddress;

        $business->eventHandler=CONFIG['BUSINESS']['EVENT_HANDLER'];

        if(CONFIG['HTTP_SERVER']['EVENT_LOOP']==1){
            Worker::$eventLoopClass = 'Workerman\Events\Swoole';
        }

        Worker::runAll();
    }
}
