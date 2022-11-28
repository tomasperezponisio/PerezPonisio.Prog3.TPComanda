<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Logger;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';

// Middlewares de datos
require_once './middlewares/UsuarioYCategoriaChecker.php';
require_once './middlewares/TipoYDescripcionChecker.php';
require_once './middlewares/IdChecker.php';

// Middlewares de acceso
require_once './middlewares/VerificarToken.php';
require_once './middlewares/Login.php';
require_once './middlewares/SocioChecker.php';
require_once './middlewares/MozoChecker.php';
require_once './middlewares/CerveceroChecker.php';
require_once './middlewares/BartenderChecker.php';
require_once './middlewares/CocineroChecker.php';

// Controllers
require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/ProductoCerveceroController.php';
require_once './controllers/ProductoBartenderController.php';
require_once './controllers/ProductoCocineroController.php';
require_once './controllers/ClienteController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Usuarios
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  // $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
  $group->get('/backupusuarios', \UsuarioController::class . ':DescargarCSV')
    ->add(new SocioCheckerMiddleware())
    ->add(new VerificarTokenMiddleware()); 
  $group->post('[/]', \UsuarioController::class . ':CargarUno')
    ->add(new UsuarioYCategoriaCheckerMiddleware())
    ->add(new SocioCheckerMiddleware())
    ->add(new VerificarTokenMiddleware());
  $group->put('[/]', \UsuarioController::class . ':ModificarUno')->add(new UsuarioYCategoriaCheckerMiddleware())->add(new IdCheckerMiddleware());
  $group->delete('[/]', \UsuarioController::class . ':BorrarUno')->add(new IdCheckerMiddleware());
  $group->post('/login', \UsuarioController::class . ':Login')->add(new LoginMiddleware()); 
  $group->post('/backupusuarios', \UsuarioController::class . ':CargarDatosDesdeCSV'); 
});

$app->group('/jwt', function (RouteCollectorProxy $group) {
  $group->post('/login', \UsuarioController::class . ':Login')->add(new LoginMiddleware());
})->add(new VerificarTokenMiddleware());

// Pedidos
$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos');
  $group->get('/{id_pedido}', \PedidoController::class . ':TraerUno');
  $group->post('[/]', \PedidoController::class . ':CargarUno');
  $group->post('/foto', \PedidoController::class . ':CargarFoto');
  $group->put('[/]', \PedidoController::class . ':ModificarUno');
  $group->delete('[/]', \PedidoController::class . ':BorrarUno');  
})->add(new MozoCheckerMiddleware())->add(new VerificarTokenMiddleware());

// Producto
$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/{id}', \ProductoController::class . ':TraerUno');
  $group->post('[/]', \ProductoController::class . ':CargarUno');
  $group->put('[/]', \ProductoController::class . ':ModificarUno');
  $group->delete('[/]', \ProductoController::class . ':BorrarUno')->add(new IdCheckerMiddleware());
})->add(new VerificarTokenMiddleware());

// Bartenders
$app->group('/bartenders', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoBartenderController::class . ':TraerTodos');
  $group->put('[/]', \ProductoBartenderController::class . ':ModificarUno');
})->add(new BartenderCheckerMiddleware())->add(new VerificarTokenMiddleware());

// Cerveceros
$app->group('/cerveceros', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoCerveceroController::class . ':TraerTodos');  
  $group->put('[/]', \ProductoCerveceroController::class . ':ModificarUno');
})->add(new CerveceroCheckerMiddleware())->add(new VerificarTokenMiddleware());

// Cocineros
$app->group('/cocineros', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoCocineroController::class . ':TraerTodos');
  $group->put('[/]', \ProductoCocineroController::class . ':ModificarUno');
})->add(new CocineroCheckerMiddleware())->add(new VerificarTokenMiddleware());

// Mesa
$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->get('/{id}', \MesaController::class . ':TraerUno');
  $group->post('[/]', \MesaController::class . ':CargarUno');
  $group->put('[/]', \MesaController::class . ':ModificarUno');
  $group->delete('[/]', \MesaController::class . ':BorrarUno')->add(new IdCheckerMiddleware());
})->add(new VerificarTokenMiddleware());

// Cliente
$app->group('/clientes', function (RouteCollectorProxy $group) {  
  $group->get('[/]', \ClienteController::class . ':TraerUno');
});



$app->get(
  '[/]',
  function (Request $request, Response $response) {
    $payload = json_encode(array("metodo" => $_SERVER["REQUEST_METHOD"], "mensaje" => "Slim Framework 4 PHP - Tomas Perez Ponisio - La Comanda"));
    sleep(5);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
);

$app->run();
