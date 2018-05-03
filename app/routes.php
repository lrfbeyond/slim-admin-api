<?php 
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Middleware\AuthMiddleware;

//$app->get('/', 'HomeController:index')->setName('home');

$app->get('/article', 'ArticleController:index');

$app->post('/test', 'HomeController:test');
$app->get('/user', 'MemberController:index');

$app->group('', function() {
    $this->get('/', 'IndexController:index');
});

$app->group('/api', function () {
    $this->get('/home/getTotals', 'HomeController:getTotals');
    $this->get('/home/getOptLog', 'HomeController:getOptLog');
    $this->get('/home/getPieData', 'HomeController:getPieData');
    $this->get('/home/getLineData', 'HomeController:getLineData');

    $this->get('/article', 'ArticleController:index');
    $this->get('/article/{id:[0-9]+}', 'ArticleController:detail');
    $this->post('/article/update', 'ArticleController:update');
    $this->post('/article/upload', 'ArticleController:upload');
    $this->post('/article/delete', 'ArticleController:delete');
    $this->get('/article/getCate', 'ArticleController:getCate');
    $this->get('/article/getTags', 'ArticleController:getTags');

    $this->post('/auth/editpass', 'AuthController:editpass');
    $this->post('/auth/logout', 'AuthController:logout');

    $this->get('/member', 'MemberController:index');
    $this->get('/member/{id:[0-9]+}', 'MemberController:detail');
    $this->post('/member/update', 'MemberController:update');
    $this->post('/member/delete', 'MemberController:delete');

    $this->get('/comment', 'CommentController:index');
    $this->get('/comment/{id:[0-9]+}', 'CommentController:detail');
    $this->post('/comment/reply', 'CommentController:reply');
    $this->post('/comment/delete', 'CommentController:delete');

    $this->get('/logs', 'LogController:index');
    $this->post('/logs/delete', 'LogController:delete');

    $this->get('/setting', 'SettingController:index');
    $this->post('/setting/setOk', 'SettingController:setOk');
    
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
