<?php
/**
 * @author: Xiao Nian
 * @contact: xiaonian030@163.com
 * @datetime: 2019-12-01 14:00
 */
namespace HP\Http;
use Workerman\Protocols\Http\Request;
use Workerman\Connection\TcpConnection;
class App {
    public function __construct(TcpConnection $connection, Request $request) {
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
