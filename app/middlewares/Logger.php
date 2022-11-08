<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class LoggerMiddleware
{
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    //fecha antes
    $before = date("Y-m-d H:i:s");

    //continuo al controller 
    $response = $handler->handle($request);
    $existingContent = json_decode($response->getBody());

    //despues
    $response = new Response();
    $existingContent->fechaAntes = $before;
    $existingContent->fechaDespues = date("Y-m-d H:i:s");

    $payload = json_encode($existingContent);
    
    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');
  }
}
