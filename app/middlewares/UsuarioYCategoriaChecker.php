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
    if (isset($parametros['categoria']) && isset($parametros['usuario'])) {
      // me fijo que no esten vacios
      if ($parametros['categoria'] != "" && $parametros['usuario'] != "") {
        if (ctype_alpha($parametros['usuario'])){
          $categoria = trim(strtolower($parametros['categoria']));
          // me fijo que la categoria sea una de las opciones validas
          if (
            $categoria === "socio" ||
            $categoria === "mozo" ||
            $categoria === "bartender" ||
            $categoria === "cervecero" ||
            $categoria === "cocinero"
          ) {
            $response = $handler->handle($request);
          } else {
            $response->getBody()->write("Error Categoria invalida, debe ser: socio / mozo / bartender / cervecero / cocinero");
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
