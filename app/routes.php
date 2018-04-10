<?php 
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Middleware\TestMiddleware;
use App\Middleware\AuthMiddleware;

$app->get('/', 'HomeController:index')->setName('home')->add(new TestMiddleware($container));

$app->get('/article', 'ArticleController:index');

$app->post('/test', 'HomeController:test');
$app->get('/user', 'MemberController:index');

$app->group('', function () {
    $this->get('/api/article', 'ArticleController:index');
    $this->get('/api/article/{id:[0-9]+}', 'ArticleController:detail');
    $this->post('/api/article/update', 'ArticleController:update');
    $this->post('/api/article/delete', 'ArticleController:delete');
    $this->post('/api/auth/editpass', 'AuthController:editpass');
    $this->post('/api/auth/logout', 'AuthController:logout');

    $this->get('/api/member', 'MemberController:index');
    $this->get('/api/member/{id:[0-9]+}', 'MemberController:detail');
    $this->post('/api/member/update', 'MemberController:update');
    $this->post('/api/member/delete', 'MemberController:delete');
    
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
