<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class MozoCheckerMiddleware
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
      $id_categoria = AutentificadorJWT::ObtenerData($token)->id_categoria;
      $esValido = true;
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    if (
      $esValido &&
      ($id_categoria == "0" || $id_categoria == "1")
    ) {
      $response = $handler->handle($request);
    } else {
      $payload = json_encode(array("Error" => "No tiene permisos de: Mozo"));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}
