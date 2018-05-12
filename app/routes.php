<?php 
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;

//$app->get('/', 'HomeController:index')->setName('home');

$app->get('/article', 'ArticleController:index');

$app->get('/user', 'MemberController:index');

$app->group('', function() {
    $this->get('/', 'IndexController:index');
    $this->get('/test', 'IndexController:test');
});
$app->get('/hello/{name}', function ($request, $response, $args) {
    return $this->view->render($response, 'profile.html', [
        'name' => $args['name']
    ]);
})->setName('profile');

// 需要验证用户登录状态和用户组操作权限
$app->group('/api', function () {
    $this->get('/article', 'ArticleController:index');
    $this->post('/article/update', 'ArticleController:update');
    $this->post('/article/upload', 'ArticleController:upload');
    $this->post('/article/delete', 'ArticleController:delete');

    $this->get('/catelog', 'CatelogController:index');
    $this->get('/catelog/getCateTree', 'CatelogController:getCateTree');
    $this->post('/catelog/update', 'CatelogController:update');
    $this->post('/catelog/delete', 'CatelogController:delete');

    $this->get('/member', 'MemberController:index');
    $this->post('/member/update', 'MemberController:update');
    $this->post('/member/delete', 'MemberController:delete');

    $this->get('/comment', 'CommentController:index');
    //$this->get('/comment/{id:[0-9]+}', 'CommentController:detail');
    $this->post('/comment/reply', 'CommentController:reply');
    $this->post('/comment/delete', 'CommentController:delete');

    $this->get('/admin', 'AdminController:index');
    $this->post('/admin/update', 'AdminController:update');
    $this->post('/admin/delete', 'AdminController:delete');
    $this->post('/admin/resetPass', 'AdminController:resetPass');

    $this->get('/role', 'RoleController:index');
    $this->post('/role/update', 'RoleController:update');
    $this->post('/role/delete', 'RoleController:delete');

    $this->get('/logs', 'LogController:index');
    $this->post('/logs/delete', 'LogController:delete');

    $this->post('/setting/setOk', 'SettingController:setOk');
    
})->add(new AuthMiddleware($container))->add(new PermissionMiddleware($container));

// 需要验证用户登录状态
$app->group('/api', function () {
    $this->get('/home/getTotals', 'HomeController:getTotals');
    $this->get('/home/getOptLog', 'HomeController:getOptLog');
    $this->get('/home/getPieData', 'HomeController:getPieData');
    $this->get('/home/getLineData', 'HomeController:getLineData');
    $this->get('/article/{id:[0-9]+}', 'ArticleController:detail');
    $this->get('/article/getCate', 'ArticleController:getCate');
    $this->get('/article/getTags', 'ArticleController:getTags');
    $this->get('/catelog/{id:[0-9]+}', 'CatelogController:detail');
    $this->get('/member/{id:[0-9]+}', 'MemberController:detail');
    $this->get('/comment/{id:[0-9]+}', 'CommentController:detail');
    $this->get('/admin/{id:[0-9]+}', 'AdminController:detail');
    $this->get('/role/{id:[0-9]+}', 'RoleController:detail');
    $this->get('/role/getPermission', 'RoleController:getPermission');
    $this->get('/setting', 'SettingController:index');

    $this->post('/auth/editpass', 'AuthController:editpass');
    $this->post('/auth/logout', 'AuthController:logout');
    
})->add(new AuthMiddleware($container));

$app->post('/api/auth', 'AuthController:chkLogin');
$app->get('/api/auth/captcha', 'AuthController:captcha');


// $app->get('/hello/{name}', function (Request $request, Response $response) {
//     $name = $request->getAttribute('name');
//     $response->getBody()->write("Hello123, $name");
//     $this->logger->addInfo("Something interesting happened");
//     return $response;
// });

// $app->get('/tickets', function (Request $request, Response $response) {
//     $this->logger->addInfo("Ticket list");
//     $mapper = new TicketMapper($this->db);
//     $tickets = $mapper->getTickets();

//     $response->getBody()->write(var_export($tickets, true));
//     return $response;
// });

// $app->get('/ticket/{id}', function (Request $request, Response $response, $args) {
//     $ticket_id = (int)$args['id'];
//     $mapper = new TicketMapper($this->db);
//     $ticket = $mapper->getTicketById($ticket_id);

//     $response->getBody()->write(var_export($ticket, true));
//     return $response;
// });

// $app->get('/', '\App\Contoller\HomeController::index');
