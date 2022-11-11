<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';
require_once './utils/AutentificadorJWT.php';

class ProductoController extends Producto implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $tipo = strtolower($parametros['tipo']);
    $descripcion = strtolower($parametros['descripcion']);

    // Creamos el producto
    $prod = new Producto();
    $prod->tipo = $tipo;
    $prod->descripcion = $descripcion;
    $prod->crearProducto();

    $payload = json_encode(array("mensaje" => "Producto creado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos producto por id
    $id = $args['id'];
    $producto = Producto::obtenerProducto($id);
    $payload = json_encode($producto);

    if ($payload != "false") {
      $response->getBody()->write($payload);
    } else {
      $response->getBody()->write("Producto inexistente");
    }
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Producto::obtenerTodos();
    $payload = json_encode(array("listaProducto" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $id = $parametros['id'];
    $tipo = $parametros['tipo'];
    $descripcion = $parametros['descripcion'];
    Producto::modificarProducto($id, $tipo, $descripcion);

    $payload = json_encode(array("mensaje" => "Producto modificado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $id = $parametros['id'];
    if (Producto::verificarId($id)) {
      if (Producto::borrarProducto($id)) {
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
