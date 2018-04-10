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
        //print_r($_SESSION['admin_auth']);
        if (!isset($_SESSION['admin_auth'])) {
            $data = [
                'code' => 101,
                'msg' => '未登录'
            ];
            return $response->withJson($data);
        }
        $response = $next($request, $response);
        //$response->getBody()->write('AFTER');

        return $response;
    }
}

