<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class SocioCheckerMiddleware
{
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();
    $esValido = false;
    $payload = "";
    $token = "";

    try {
      $header = $request->getHeaderLine('Authorization');
      if ($header != null) {
        $token = trim(explode("Bearer", $header)[1]);
      }
      AutentificadorJWT::verificarToken($token);
      $esValido = true;
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    if (
      $esValido &&
      AutentificadorJWT::ObtenerData($token)->id_categoria == "0"
    ) {
      $response = $handler->handle($request);
    } else {
      $payload = json_encode(array("Error" => "No tiene permisos de: Socio"));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}


  //   $response = new Response();
  //   // traigo los parametros
  //   $parametros = $request->getParsedBody();
  //   // me fijo si me mandaron perfil
  //   if (isset($parametros['id_categoria'])) {
  //     // me fijo que no esten vacios
  //     if ($parametros['id_categoria'] != "") {
  //       // me fijo si el perfil es admin        
  //       if ($parametros['id_categoria'] == 0) {          
  //         $response = $handler->handle($request);
  //       } else {
  //         $response->getBody()->write("No tiene permisos de: Socio");
  //       }
  //     } else {
  //       $response->getBody()->write("Error Campo vacio ");
  //     }
  //   } else {
  //     $response->getBody()->write("Datos incompletos");
  //   }
  //   return $response;
  // }
