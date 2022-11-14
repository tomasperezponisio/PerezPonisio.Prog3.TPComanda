<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';
require_once './utils/AutentificadorJWT.php';

class PedidoController extends Pedido implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $nombre_cliente = $parametros['nombre_cliente'];
    $codigo_para_cliente = $parametros['codigo_para_cliente'];
    // cada pedido comienza con pedido 0 (En preparacion)
    $id_estado = 0;

    // Creamos el pedido
    $pedido = new Pedido();
    $pedido->nombre_cliente = $nombre_cliente;
    $pedido->codigo_para_cliente = $codigo_para_cliente;
    $pedido->id_estado = $id_estado;
    $pedido->crearPedido();

    $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos pedido por id
    $id = $args['id'];
    $pedido = Pedido::obtenerPedido($id);
    $payload = json_encode($pedido);

    if ($payload != "false") {
      if (Pedido::verificarId($id)) {
        $response->getBody()->write($payload);
      } else {
        $response->getBody()->write("Usuario eliminado");
      }
    } else {
      $response->getBody()->write("Usuario inexistente");
    }
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Pedido::obtenerTodos();
    $payload = json_encode(array("lista pedidos" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $id = $parametros['id'];
    if (Pedido::verificarId($id)) {
      $id_estado = $parametros['id_estado'];
      
      Pedido::modificarPedido($id, $id_estado);
      $payload = json_encode(array("mensaje" => "Estado de pedido modificado con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "Error - ID Inexistente"));
    }
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usuarioId = $parametros['usuarioId'];
    if (Usuario::verificarId($usuarioId)) {
      if (Usuario::borrarUsuario($usuarioId)) {
        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Error al borrar el usuario"));
      }
    } else {
      $payload = json_encode(array("mensaje" => "ID inexistente"));
    }
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  
}
