<?php
/**
 * @author: Xiao Nian
 * @contact: xiaonian030@163.com
 * @datetime: 2019-12-01 14:00
 */
namespace HP\Register;
use HP\Core;
use Workerman\Worker;
use GatewayWorker\Register;
class App extends Core {

    public function run($run_able=true, ?callable $callback = null){
        //实例化
        $address='text://'.CONFIG['REGISTER']['LISTEN_ADDRESS'].':'.CONFIG['REGISTER']['PORT'];
        $register = new Register($address);
        $register->name=CONFIG['REGISTER']['SERVER_NAME'];

        if($run_able){
            Worker::runAll();
        }
    }
}
