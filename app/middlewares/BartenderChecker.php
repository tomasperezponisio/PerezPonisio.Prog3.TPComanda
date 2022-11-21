<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class BartenderCheckerMiddleware
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
      AutentificadorJWT::ObtenerData($token)->id_categoria == "2"
    ) {
      $response = $handler->handle($request);
    } else {
      $payload = json_encode(array("Error" => "No tiene permisos de: Bartender"));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}
