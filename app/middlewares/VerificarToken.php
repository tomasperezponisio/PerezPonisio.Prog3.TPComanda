<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class VerificarTokenMiddleware
{
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();
    $esValido = false;

    try {
      $header = $request->getHeaderLine('Authorization');
      if ($header != null)
      {
        $token = trim(explode("Bearer", $header)[1]);
      }
      AutentificadorJWT::verificarToken($token);
      $esValido = true;
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    if ($esValido) {
      $payload = json_encode(array('valid' => "Token válido"));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}
