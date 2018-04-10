<?php 
namespace App\Middleware;


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
        // if (false === $hasLogin) {
        //     $data = [
        //         'code' => 101,
        //         'msg' => '未登录'
        //     ];
        //     return $response->withJson($data);
        // }
        
        $response = $next($request, $response);
        //$response->getBody()->write('AFTER');

        return $response;
    }

    private function chkSign()
    {
        $auth = isset($_SESSION['admin_auth']) ? $_SESSION['admin_auth'] : '';
        $token = isset($_SERVER['HTTP_X_TOKEN']) ? $_SERVER['HTTP_X_TOKEN'] : '';
        if (!empty($auth) && !empty($token)) {
            $key = $this->safekey; //安全密钥
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

