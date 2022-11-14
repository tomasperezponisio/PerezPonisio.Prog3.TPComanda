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
    if (isset($parametros['id_tipo']) && isset($parametros['descripcion'])) {
      $id_tipo = $parametros['id_tipo'];
      // me fijo que no esten vacios
      if ($id_tipo != "" && $id_tipo != "") {
        // me fijo que la categoria sea una opcion valida
        if (is_numeric($id_tipo)) {
          $id_tipo = intval($id_tipo);
          if (
            $id_tipo === 0 ||  // cerveza
            $id_tipo === 1 ||  // trago
            $id_tipo === 2     // trago
          ) {
            $response = $handler->handle($request);
          } else {
            $response->getBody()->write("Error Categoria invalida, debe ser: 0 (cerveza) / 1 (trago) / 2 (comida)");
          }
        } else {
          $response->getBody()->write("Error Debe ingresar un numero");
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
