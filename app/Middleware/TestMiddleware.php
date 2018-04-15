<?php 
namespace App\Middleware;


class TestMiddleware
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $next)
    {
        $response->getBody()->write('BEFORE');
        if (!isset($_SESSION['auth_user'])) {
            $data = [
                'code' => 101,
                'msg' => '未登录'
            ];
            return $response->withJson($data);
        }
        $response = $next($request, $response);
        $response->getBody()->write('AFTER');

        return $response;
    }
}
