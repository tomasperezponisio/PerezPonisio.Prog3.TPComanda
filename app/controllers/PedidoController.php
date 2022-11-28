<?php
require_once './models/Pedido.php';
require_once './models/Producto.php';
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';
require_once './utils/AutentificadorJWT.php';
require_once './utils/GestorArchivos.php';

class PedidoController extends Pedido implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $nombre_cliente = $parametros["datosPedido"][0]["nombre_cliente"];
    $codigo_para_cliente = $parametros["datosPedido"][0]["codigo_para_cliente"];
    // cada pedido comienza con pedido 0 (En preparacion)
    $id_estado = intval($parametros["datosPedido"][0]["id_estado"]);
    $id_mesa = intval($parametros["datosPedido"][0]["id_mesa"]);

    // Creamos el pedido
    $pedido = new Pedido();
    $pedido->nombre_cliente = $nombre_cliente;
    $pedido->codigo_para_cliente = $codigo_para_cliente;
    $pedido->id_estado = $id_estado;
    $id_pedido = $pedido->crearPedido();

    // Creamos los productos que vinieron
    $listaProductos = $parametros["datosPedido"][0]["listaProductos"];
    foreach ($listaProductos as $key => $value) {
      // Creamos el producto
      $prod = new Producto();
      $prod->id_tipo = intval($value["id_tipo"]);
      $prod->descripcion = $value["descripcion"];
      $id_producto = $prod->crearProducto();

      // Cargamos el producto y el pedido en la tabla que los asocia
      Pedido::registrarProductosPorPedido($id_pedido, $id_producto);
    }

    // Registramos el pedido al mozo que lo creó
    $token = "";
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);
    $id_usuario =  AutentificadorJWT::ObtenerData($token)->id;
    Pedido::registrarPedidosDeUsuario($id_pedido, $id_usuario);

    // Asignamos el pedido a una mesa libre (cerrada)
    Pedido::registrarPedidosEnMesa($id_pedido, $id_mesa);
    // cambiamos el estado de la mes a 1 (Con cliente esperando pedido)
    Mesa::modificarMesa($id_mesa, 1);

    $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

    $response->getBody()->write($payload);
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

  public function TraerUno($request, $response, $args)
  {
    // Buscamos productos por id de pedido
    $id_pedido = $args['id_pedido'];

    // Mostramos la info de un pedido
    $pedido = Pedido::obtenerPedido($id_pedido);

    // mostramos la info de los productos asociados a ese pedido
    $lista = Producto::traerProductosPorPedido($id_pedido);
    array_unshift($lista, $pedido);
    array_unshift($lista, "DATOS DEL PEDIDO");
    array_splice($lista, 2, 0, "PRODUCTOS DEL PEDIDO");
    // Traemos el mayor tiempo de finalizacion de los productos y lo mostramos
    // en el pedido al que pertenece
    $tiempo_finalizacion = Producto::traerTiempoMasAltoPorPedido($id_pedido);
    array_splice($lista, 2, 0, "TIEMPO DE FINALIZACION: " . $tiempo_finalizacion . " min");    

    $payload = json_encode($lista);

    if ($payload != "false") {
      if (Pedido::verificarId($id_pedido)) {
        $response->getBody()->write($payload);
      } else {
        $response->getBody()->write("Pedido eliminado");
      }
    } else {
      $response->getBody()->write("Pedido inexistente");
    }
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $id = $parametros['id'];
    if (Pedido::verificarId($id)) {
      $id_estado = intval($parametros['id_estado']);
      Mesa::traerIdMesaPorIdPedido($id);
      if ($id_estado == 1 || $id_estado == 2) {
        if (Producto::verificarProductosListosPorPedido($id)) {
          Pedido::modificarPedido($id, $id_estado);
          if ($id_estado == 2){
            // si el estado de pedido cambia a ENTREGADO
            // el estado de la mesa cambia a CON CLIENTE COMIENDO
            Mesa::modificarMesa(Mesa::traerIdMesaPorIdPedido($id), 2);
          }
          $payload = json_encode(array("mensaje" => "Estado de pedido modificado con exito"));
        } else {
          $payload = json_encode(array("mensaje" => "Error - Hay productos en preparacion"));
        }
      } else {
        Pedido::modificarPedido($id, $id_estado);
        $payload = json_encode(array("mensaje" => "Estado de pedido modificado con exito"));
      }
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

  public function CargarFoto($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $id = $parametros['id'];
    $rutaImagenes = "./Imagenes/";
    $gestor = new GestorArchivos($rutaImagenes);
    
    $path_foto = $gestor->GuardarFoto("pedido_numero_".$id);   

    $payload = json_encode(array("mensaje" => "Foto cargada con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  
}
