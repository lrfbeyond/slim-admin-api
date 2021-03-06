<?php 
namespace App\Middleware;

use \Psr\Http\Message\RequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AuthMiddleware
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $next)
    {
        //$response->getBody()->write('BEFORE');
        $hasLogin = $this->chkSign();
        if (false === $hasLogin) {
            $data = [
                'result' => 'failed',
                'code' => -1,
                'msg' => '未登录'
            ];
            return $response->withJson($data);
            // return $response->withStatus(401);
        }
        
        $response = $next($request, $response);
        //$response->getBody()->write('AFTER');

        return $response;
    }

    private function chkSign()
    {
        $auth = isset($_SESSION['admin_auth']) ? $_SESSION['admin_auth'] : '';
        $token = isset($_SERVER['HTTP_X_TOKEN']) ? $_SERVER['HTTP_X_TOKEN'] : '';
        if (!empty($auth) && !empty($token)) {
            $key = $this->container->safekey; //安全密钥
            $sign = dataAuthSign($auth, $key);
            if ($sign == $token) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

