<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class IdCheckerMiddleware
{
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();
    // traigo los parametros
    $parametros = $request->getParsedBody();
    // me fijo si me mandaron usuario y clave
    if (isset($parametros['id']) ) {
      // me fijo que no esten vacios
      if ($parametros['id'] != "") {
        $response = $handler->handle($request);
      } else {
        $response->getBody()->write("Error Campo vacio ");
      }
    } else {
      // aviso que faltaron datos
      $response->getBody()->write("Datos incompletos");
    }
    return $response;
  }
}
