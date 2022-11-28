<?php
require_once './models/Pedido.php';
require_once './models/Producto.php';
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';


class ClienteController implements IApiUsable
{
  public function TraerUno($request, $response, $args)
  {
    // Buscamos productos por id de pedido    
    $codigo_para_cliente = $_GET['codigo_para_cliente'];
    $id_mesa = $_GET['codigo_para_cliente'];
    
    $id_pedido = Pedido::traerIdPorCodigoDeCliente($codigo_para_cliente);          
    $tiempo_finalizacion = Producto::traerTiempoMasAltoPorPedido($id_pedido["id"]);
    $payload = json_encode(array("Tiempo de Finalización" => $tiempo_finalizacion));
    $response->getBody()->write($payload);    
    
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function CargarUno($request, $response, $args)
  {
  }

  public function TraerTodos($request, $response, $args)
  {
  }

  public function ModificarUno($request, $response, $args)
  {
  }

  public function BorrarUno($request, $response, $args)
  {
  }
}
