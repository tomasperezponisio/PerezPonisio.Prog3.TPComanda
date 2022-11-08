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
require_once './middlewares/Login.php';
require_once './middlewares/UsuarioYClaveChecker.php';
require_once './middlewares/AdminChecker.php';
require_once './middlewares/IdChecker.php';
require_once './middlewares/SuperAdminChecker.php';
require_once './middlewares/Logger.php';
require_once './middlewares/VerificarToken.php';

require_once './controllers/UsuarioController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
  $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(new AdminCheckerMiddleware())->add(new UsuarioYClaveCheckerMiddleware());
  $group->put('[/]', \UsuarioController::class . ':ModificarUno');
  $group->delete('[/]', \UsuarioController::class . ':BorrarUno')->add(new SuperAdminCheckerMiddleware())->add(new IdCheckerMiddleware());
  $group->post('/login', \UsuarioController::class . ':Login')->add(new LoginMiddleware());
});

$app->group('/jwt', function (RouteCollectorProxy $group) {
  $group->post('/login', \UsuarioController::class . ':Login')->add(new LoginMiddleware());
})->add(new VerificarTokenMiddleware());


$app->get(
  '[/]',
  function (Request $request, Response $response) {
    $payload = json_encode(array("metodo" => $_SERVER["REQUEST_METHOD"], "mensaje" => "Slim Framework 4 PHP - Tomas Perez Ponisio"));
    sleep(5);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
)->add(new LoggerMiddleware());

$app->post(
  '[/]',
  function (Request $request, Response $response) {
    $payload = json_encode(array("metodo" => $_SERVER["REQUEST_METHOD"], "mensaje" => "Slim Framework 4 PHP - Tomas Perez Ponisio"));
    sleep(5);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
)->add(new LoggerMiddleware());



//------------------------------------------------------
// JWT test routes
// $app->group('/jwt', function (RouteCollectorProxy $group) {

//   $group->post('/crearToken', function (Request $request, Response $response) {
//     $parametros = $request->getParsedBody();

//     $usuario = $parametros['usuario'];
//     $perfil = $parametros['perfil'];
//     $alias = $parametros['alias'];

//     $datos = array('usuario' => $usuario, 'perfil' => $perfil, 'alias' => $alias);

//     $token = AutentificadorJWT::CrearToken($datos);
//     $payload = json_encode(array('jwt' => $token));

//     $response->getBody()->write($payload);
//     return $response
//       ->withHeader('Content-Type', 'application/json');
//   });

//   $group->get('/devolverPayLoad', function (Request $request, Response $response) {
//     $header = $request->getHeaderLine('Authorization');
//     $token = trim(explode("Bearer", $header)[1]);

//     try {
//       $payload = json_encode(array('payload' => AutentificadorJWT::ObtenerPayLoad($token)));
//     } catch (Exception $e) {
//       $payload = json_encode(array('error' => $e->getMessage()));
//     }

//     $response->getBody()->write($payload);
//     return $response
//       ->withHeader('Content-Type', 'application/json');
//   });

//   $group->get('/devolverDatos', function (Request $request, Response $response) {
//     $header = $request->getHeaderLine('Authorization');
//     $token = trim(explode("Bearer", $header)[1]);

//     try {
//       $payload = json_encode(array('datos' => AutentificadorJWT::ObtenerData($token)));
//     } catch (Exception $e) {
//       $payload = json_encode(array('error' => $e->getMessage()));
//     }

//     $response->getBody()->write($payload);
//     return $response
//       ->withHeader('Content-Type', 'application/json');
//   });

//   $group->get('/verificarToken', function (Request $request, Response $response) {
//     $header = $request->getHeaderLine('Authorization');
//     $token = trim(explode("Bearer", $header)[1]);
//     $esValido = false;

//     try {
//       AutentificadorJWT::verificarToken($token);
//       $esValido = true;
//     } catch (Exception $e) {
//       $payload = json_encode(array('error' => $e->getMessage()));
//     }

//     if ($esValido) {
//       $payload = json_encode(array('valid' => $esValido));
//     }

//     $response->getBody()->write($payload);
//     return $response
//       ->withHeader('Content-Type', 'application/json');
//   });
// });



$app->run();
