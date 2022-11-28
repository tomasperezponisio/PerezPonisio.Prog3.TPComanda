<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';
require_once './utils/AutentificadorJWT.php';

class ProductoBartenderController extends Producto implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $payload = json_encode(array("mensaje" => "Deshabilitado"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    $payload = json_encode(array("mensaje" => "Deshabilitado"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    if (!isset($_GET['id_estado'])) {
      $lista = Producto::traerProductosPorTipo(1);
    } else {
      $id_estado = intval($_GET['id_estado']);  
      $lista = Producto::traerProductosEstadoTipo($id_estado, 1);
    }
    $payload = json_encode(array("lista productos: BARTENDERS" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $id = $parametros['id'];
    $id_estado = $parametros['id_estado'];
    $tiempo_finalizacion = $parametros['tiempo_finalizacion'];

    if (Producto::verificarId($id)) {
      if (Producto::traerTipo($id)["id_tipo"] == 1) {
        Producto::setEstado($id, $id_estado);
        Producto::setTiempoDeFinalizacion($id, $tiempo_finalizacion);
        $payload = json_encode(array("mensaje" => "Producto modificado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "ERROR - solo puede modificar productos de bartender, reingrese ID"));
      }
    } else {
      $payload = json_encode(array("mensaje" => "ERROR - id de producto inexistente"));
    }


    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $payload = json_encode(array("mensaje" => "Deshabilitado"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}
