<?php 
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/', 'HomeController:index')->setName('home');

$app->get('/article', 'ArticleController:index');

$app->get('/test', 'HomeController:test');
$app->get('/user', 'UserController:index');

$app->group('', function () {
    $this->get('/api/article', 'ArticleController:index');
    $this->get('/api/article/{id:[0-9]+}', 'ArticleController:detail');
    $this->post('/api/article/update', 'ArticleController:update');
    $this->post('/api/article/delete', 'ArticleController:delete');
});


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
