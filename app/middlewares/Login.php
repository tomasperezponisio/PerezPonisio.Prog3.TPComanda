<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class LoginMiddleware
{
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();
    // traigo los parametros
    $parametros = $request->getParsedBody();
    // me fijo si me mandaron usuario y clave
    if (isset($parametros['clave']) && isset($parametros['usuario'])) {
      // me fijo que no esten vacios
      if ($parametros['clave'] == "" || $parametros['usuario'] == "") {
        $response->getBody()->write("Error Campo vacio");
      } else {
        // si no estan vacios paso el request
        $response = $handler->handle($request);
      }
    } else {
      // aviso que faltaron datos
      $response->getBody()->write("Datos incompletos");
    }
    return $response;
  }
}
