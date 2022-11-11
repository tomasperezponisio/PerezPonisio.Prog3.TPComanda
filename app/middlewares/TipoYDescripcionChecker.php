<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class TipoYDescripcionCheckerMiddleware
{
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();
    // traigo los parametros
    $parametros = $request->getParsedBody();
    // me fijo si me mandaron usuario y clave
    if (isset($parametros['tipo']) && isset($parametros['descripcion'])) {
      // me fijo que no esten vacios
      if ($parametros['tipo'] != "" && $parametros['descripcion'] != "") {
        $tipo = trim(strtolower($parametros['tipo']));
        // me fijo que la categoria sea una de las opciones validas
        if (
          $tipo === "cerveza" ||
          $tipo === "trago" ||
          $tipo === "comida"
        ) {
          $response = $handler->handle($request);
        } else {
          $response->getBody()->write("Error Categoria invalida, debe ser: cerveza / trago / comida");
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
