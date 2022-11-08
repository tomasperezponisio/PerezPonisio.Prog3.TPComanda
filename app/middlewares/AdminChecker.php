<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AdminCheckerMiddleware
{
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();
    // traigo los parametros
    $parametros = $request->getParsedBody();
    // me fijo si me mandaron perfil
    if (isset($parametros['perfil'])) {
      // me fijo que no esten vacios
      if ($parametros['perfil'] != "") {
        // me fijo si el perfil es admin
        if ($parametros['perfil'] == "admin") {
          $response = $handler->handle($request);
        } else {
          // si no es admin le aviso
          $response->getBody()->write("No tiene los permisos para crear un usuario");
        }
      } else {
        // aviso que vino vacio
        $response->getBody()->write("Error Campo vacio ");
      }
    } else {
      // aviso que faltaron datos
      $response->getBody()->write("Datos incompletos");
    }
    return $response;
  }
}
