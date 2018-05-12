<?php 
namespace App\Middleware;

use \Psr\Http\Message\RequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class PermissionMiddleware
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $next)
    {
        //$response->getBody()->write('BEFORE');

        $disallowed = $this->disallow($request);
        // 无权限返回401状态码
        if (true === $disallowed) {
            return $response->withStatus(401);
        }
        
        $response = $next($request, $response);
        //$response->getBody()->write('AFTER');

        return $response;
    }


    /**
     * 是否允许访问
     * @param Request $request
     * @return bool, true:允许，false:不允许
     */
    private function disallow(Request $request)
    {
        $pathParam = $request->getUri()->getPath();
        $pathParam = str_replace('/api', '', $pathParam);
        $admin_permission = $_SESSION['admin_permission'];
        if (!in_array($pathParam, $admin_permission)) {
            return true;
        }
        return false;
    }
}
