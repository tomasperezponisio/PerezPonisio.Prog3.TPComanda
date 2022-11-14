<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class UsuarioYCategoriaCheckerMiddleware
{
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();
    // traigo los parametros
    $parametros = $request->getParsedBody();
    // me fijo si me mandaron usuario y clave
    if (isset($parametros['id_categoria']) && isset($parametros['usuario'])) {
      // me fijo que no esten vacios
      if ($parametros['id_categoria'] != "" && $parametros['usuario'] != "") {
        if (ctype_alpha($parametros['usuario'])) {
          if (is_numeric($parametros['id_categoria'])) {
            $id_categoria = intval($parametros['id_categoria']);
            // me fijo que la categoria sea una de las opciones validas
            if (
              $id_categoria === 0 ||  // socio
              $id_categoria === 1 ||  // mozo
              $id_categoria === 2 ||  // bartender
              $id_categoria === 3 ||  // cervecero
              $id_categoria === 4     // cocinero
            ) {
              $response = $handler->handle($request);
            } else {
              $response->getBody()->write("Error Categoria invalida, debe ser: 0 (socio) / 1 (mozo) / 2 (bartender) / 3 (cervecero) / 4 (cocinero)");
            }
          } else {
            $response->getBody()->write("Error Categoria debe ser un numero");
          }
        } else {
          $response->getBody()->write("Error el usuario debe ser solo letras");
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
