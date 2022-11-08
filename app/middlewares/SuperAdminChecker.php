<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class SuperAdminCheckerMiddleware
{
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();
    // traigo los parametros
    $parametros = $request->getParsedBody();
    // me fijo si me mandaron usuario y clave
    if (isset($parametros['perfil'])) {
      // me fijo que no esten vacios
      if ( $parametros['perfil'] == "") {
        $response->getBody()->write("Error Campo vacio ");
        // me fijo si el perfil es admin
      } else if ($parametros['perfil'] == "superadmin") {
        $response = $handler->handle($request);
      } else {
        // si no es admin le aviso
        $response->getBody()->write("No tiene los permisos para borrar un usuario");
      }
    } else {
      // aviso que faltaron datos
      $response->getBody()->write("Datos incompletos");
    }
    return $response;
  }
}
