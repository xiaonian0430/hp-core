<?php
/**
 * @author: Xiao Nian
 * @contact: xiaonian030@163.com
 * @datetime: 2019-12-01 14:00
 */
namespace HP\Task;

use HP\Component\Singleton;
use Workerman\Lib\Timer;
use Workerman\Worker;

class Task {

    public function business($task){
        // 每2.5秒执行一次
        $time_interval = 2.5;
        Timer::add($time_interval, function() {
            echo "task run\n";
        });
    }

    public function run(){
        $task = new Worker();

        // 开启多少个进程运行定时任务，注意业务是否在多进程有并发问题
        $task->count = 1;
        $task->onWorkerStart = function($task) {
            $this->business($task);
        };
    }
}