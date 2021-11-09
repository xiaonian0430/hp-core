<?php
/**
 * @author: Xiao Nian
 * @contact: xiaonian030@163.com
 * @datetime: 2019-12-01 14:00
 */
namespace HP\Http;
use HP\Core;
use Workerman\Worker;
use Workerman\Protocols\Http\Request;
use Workerman\Connection\TcpConnection;
class App extends Core {

    public function run($run_able=true, ?callable $callback = null){
        //实例化
        $address='http://'.CONFIG['HTTP_SERVER']['LISTEN_ADDRESS'].':'.CONFIG['HTTP_SERVER']['PORT'];
        $http_server = new Worker($address);

        //进程名称
        $http_server->name= CONFIG['HTTP_SERVER']['SERVER_NAME'];

        // 进程数量
        $http_server->count = CONFIG['HTTP_SERVER']['PROCESS_COUNT'];

        // 接收请求
        $http_server->onMessage = function ($connection, $request) {
            $this->onMessage($connection, $request);
        };

        if($run_able){
            Worker::runAll();
        }
    }

    private function onMessage(TcpConnection $connection, Request $request) {
        //路由分发: 模块=module 类=class 方法=function
        $path=trim($request->path(),'/');
        $dot=strpos($path, '.');
        if($dot===false){
            $paths=explode('/',$path);
            if(isset($paths[0])){
                $module=ucwords($paths[0]);
            }else{
                $module='Index';
            }
            if(isset($paths[1])){
                $class_name=ucwords($paths[1]);
            }else{
                $class_name='Index';
            }
            $function = $paths[2] ?? 'index';
            $class='App\HttpController\\'.$module.'\\'.$class_name;
            if(class_exists($class)){
                $instance=new $class($connection, $request);
                if(method_exists($instance, $function)){
                    $instance->$function();
                }else{
                    $instance=new Controller($connection, $request);
                    $instance->writeJsonNoFound();
                }
            }else{
                $instance=new Controller($connection, $request);
                $instance->writeJsonNoFound();
            }
        }else{
            $instance=new Controller($connection, $request);
            $instance->writeFile($path);
        }
    }
}
