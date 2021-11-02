<?php
/**
 * @author: Xiao Nian
 * @contact: xiaonian030@163.com
 * @datetime: 2019-12-01 14:00
 */
namespace HP\Gateway;
use HP\Core;
use Workerman\Worker;
use GatewayWorker\Gateway;
class App extends Core {

    public function run(){
        //实例化
        $address='Websocket://'.CONFIG['GATEWAY']['LISTEN_ADDRESS'].':'.CONFIG['GATEWAY']['PORT'];
        $gateway = new Gateway($address);

        // 设置名称，方便status时查看
        $gateway->name = CONFIG['GATEWAY']['SERVER_NAME'];

        // 设置进程数，gateway进程数建议与cpu核数相同
        $gateway->count = CONFIG['GATEWAY']['PROCESS_COUNT'];

        // 分布式部署时请设置成内网ip（非127.0.0.1）
        $gateway->lanIp = CONFIG['GATEWAY']['LAN_IP'];

        // 内部通讯起始端口。假如$gateway->count=4，起始端口为2300
        // 则一般会使用2300 2301 2302 2303 4个端口作为内部通讯端口
        $gateway->startPort = CONFIG['GATEWAY']['LAN_START_PORT'];

        // 心跳间隔
        $gateway->pingInterval = CONFIG['GATEWAY']['PING_INTERVAL'];

        $gateway->pingNotResponseLimit = 1;

        // 心跳数据
        $gateway->pingData = '';

        // 服务注册地址
        $registerAddress=CONFIG['REGISTER']['LAN_IP'].':'.CONFIG['REGISTER']['LAN_PORT'];
        $gateway->registerAddress = $registerAddress;

        Worker::runAll();
    }
}
