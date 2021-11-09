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

    protected $callback;

    public function run($run_able=true, ?callable $callback = null){
        $this->callback=$callback;
        //实例化
        $worker = new Worker();

        //进程名称
        $worker->name= CONFIG['TASK']['SERVER_NAME'];

        // 启动1个进程对外提供服务
        $worker->count = CONFIG['TASK']['PROCESS_COUNT'];;

        // 接收到浏览器发送的数据时回复hello world给浏览器
        $worker->onWorkerStart = function($worker){
            $callback = $this->callback;
            try{
                $callback(1);
            }catch (\Throwable $e){}
        };

        if($run_able){
            Worker::runAll();
        }
    }
}
