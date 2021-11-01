<?php
/**
 * @author: Xiao Nian
 * @contact: xiaonian030@163.com
 * @datetime: 2019-12-01 14:00
 */
namespace HP\Http;
use Workerman\Protocols\Http\Request;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Response;
class Controller {
    protected $connection;
    protected $request;

    public function __construct(TcpConnection $connection, Request $request) {
        $this->connection=$connection;
        $this->request=$request;
    }

    public function writeJson($statusCode , $result, $msg) {
        $body=json_encode([
            "code" => $statusCode,
            "result" => !empty($result)?$result:['empty'=>1],
            "msg" => $msg
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $response=new Response(200, [
            'Server'=>'InfobirdCloud',
            'Content-type'=>'application/json;charset=utf-8'
        ], $body);
        $this->connection->send($response);
    }

    public function writeFile($file) {
        $response=new Response(200, [
            'Server'=>'InfobirdCloud',
            'Content-type'=>'application/html;charset=utf-8'
        ]);
        $this->connection->send($response->withFile(PUBLIC_ROOT.'/'.$file));
    }

    public function writeJsonNoFound() {
        $this->writeJson(400, [
            "uri"=>$this->request->host().$this->request->path(),
            "method"=>$this->request->method(),
            "param"=>$this->request->get()?:$this->request->post()?:['empty'=>1]
        ], 'the api uri not found');
    }
}
