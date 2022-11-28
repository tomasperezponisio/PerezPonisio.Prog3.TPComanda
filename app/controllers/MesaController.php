<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';
require_once './utils/AutentificadorJWT.php';

class MesaController extends Mesa implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    // $parametros = $request->getParsedBody();    
    // $descripcion = strtolower($parametros['id_estado']);

    // Creamos la mesa
    $mesa = new Mesa();    
    $mesa->crearMesa();

    $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos mesa por id
    $id = $args['id'];
    $mesa = Mesa::obtenerMesa($id);
    $payload = json_encode($mesa);

    if ($payload != "false") {
      $response->getBody()->write($payload);
    } else {
      $response->getBody()->write("Mesa inexistente");
    }
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Mesa::obtenerTodos();
    $payload = json_encode(array("Lista Mesas" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $id = $parametros['id'];
    $id_estado = intval($parametros['id_estado']);    

    $token = "";
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);
    $id_categoria =  AutentificadorJWT::ObtenerData($token)->id_categoria;
  
    if ($id_estado == 0 && $id_categoria !=0 ){
      $payload = json_encode(array("Error" => "Solo los socios pueden cerrar una mesa"));
    } else {
      Mesa::modificarMesa($id, $id_estado);
      $payload = json_encode(array("mensaje" => "Estado de mesa modificado con exito"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $id = $parametros['id'];
    if (Mesa::verificarId($id)) {
      if (Mesa::borrarMesa($id)) {
        $payload = json_encode(array("mensaje" => "Mesa borrada con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Error al borrar la mesa"));
      }
    } else {
      $payload = json_encode(array("mensaje" => "ID inexistente"));
    }
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

}
