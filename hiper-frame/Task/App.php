<?php
/**
 * @author: Xiao Nian
 * @contact: xiaonian030@163.com
 * @datetime: 2019-12-01 14:00
 */
namespace HP\Task;
use HP\Core;
use Workerman\Worker;
class App extends Core {

    protected $consumeCallback;

    public function run(?callable $consumeCallback = null){
        $this->consumeCallback=$consumeCallback;
        //实例化
        $worker = new Worker();

        // 启动1个进程对外提供服务
        $worker->count = 1;

        // 接收到浏览器发送的数据时回复hello world给浏览器
        $worker->onWorkerStart = function($worker){
            $consumeCallback = $this->consumeCallback;
            try{
                $consumeCallback();
            }catch (\Throwable $e){}
        };

        // 运行worker
        Worker::runAll();

    }
}
