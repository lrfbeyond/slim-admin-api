<?php 
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Middleware\AuthMiddleware;

$app->get('/', 'HomeController:index')->setName('home');

$app->get('/article', 'ArticleController:index');

$app->post('/test', 'HomeController:test');
$app->get('/user', 'MemberController:index');

$app->group('', function () {
    $this->get('/api/home/getTotals', 'HomeController:getTotals');
    $this->get('/api/home/getOptLog', 'HomeController:getOptLog');
    $this->get('/api/home/getPieData', 'HomeController:getPieData');
    $this->get('/api/home/getLineData', 'HomeController:getLineData');

    $this->get('/api/article', 'ArticleController:index');
    $this->get('/api/article/{id:[0-9]+}', 'ArticleController:detail');
    $this->post('/api/article/update', 'ArticleController:update');
    $this->post('/api/article/upload', 'ArticleController:upload');
    $this->post('/api/article/delete', 'ArticleController:delete');
    $this->get('/api/article/getCate', 'ArticleController:getCate');
    $this->get('/api/article/getTags', 'ArticleController:getTags');

    $this->post('/api/auth/editpass', 'AuthController:editpass');
    $this->post('/api/auth/logout', 'AuthController:logout');

    $this->get('/api/member', 'MemberController:index');
    $this->get('/api/member/{id:[0-9]+}', 'MemberController:detail');
    $this->post('/api/member/update', 'MemberController:update');
    $this->post('/api/member/delete', 'MemberController:delete');

    $this->get('/api/comment', 'CommentController:index');
    $this->get('/api/comment/{id:[0-9]+}', 'CommentController:detail');
    $this->post('/api/comment/reply', 'CommentController:reply');
    $this->post('/api/comment/delete', 'CommentController:delete');

    $this->get('/api/logs', 'LogController:index');
    $this->post('/api/logs/delete', 'LogController:delete');

    $this->get('/api/setting', 'SettingController:index');
    $this->post('/api/setting/setOk', 'SettingController:setOk');
    
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
