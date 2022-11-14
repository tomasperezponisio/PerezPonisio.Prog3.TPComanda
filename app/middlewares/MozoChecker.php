<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class MozoCheckerMiddleware
{
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();
    // traigo los parametros
    $parametros = $request->getParsedBody();
    // me fijo si me mandaron perfil
    if (isset($parametros['id_categoria'])) {
      // me fijo que no esten vacios
      if ($parametros['id_categoria'] != "") {
        // me fijo si el perfil es admin
        if ($parametros['id_categoria'] == 1) {
          $response = $handler->handle($request);
        } else {
          $response->getBody()->write("No tiene permisos de: mozo");
        }
      } else {
        $response->getBody()->write("Error Campo vacio ");
      }
    } else {
      $response->getBody()->write("Datos incompletos");
    }
    return $response;
  }
}
