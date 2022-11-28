<?php

include_once 'ProductoDTO.php';

class Producto
{
  public $id;
  public $id_tipo;
  public $descripcion;
  public $tiempo_de_finalizacion;
  public $id_estado;

  // por default el tiempo de finalizacion es NULL y el id_estado 0 ("no ingresado en cocina")
  public function crearProducto()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "INSERT INTO productos (id_tipo, descripcion)
      VALUES (:id_tipo, :descripcion)"
    );

    $consulta->bindValue(':id_tipo', $this->id_tipo, PDO::PARAM_INT);
    $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
    $consulta->execute();

    return $objAccesoDatos->obtenerUltimoId();
  }

  // me traigo un producto por su id, puede haber varios con la misma descripcion y tipo
  public static function obtenerProducto($id)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos WHERE id = :id");
    $consulta->bindValue(':id', $id, PDO::PARAM_STR);
    $consulta->execute();
    return $consulta->fetchObject('Producto');
  }

  public static function obtenerTodos()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos");
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
  }

  public static function modificarProducto($id, $id_tipo, $descripcion)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET id_tipo = :id_tipo, descripcion = :descripcion WHERE id = :id");
    $consulta->bindValue(':id_tipo', $id_tipo, PDO::PARAM_STR);
    $consulta->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);
    $consulta->bindValue(':id', $id, PDO::PARAM_INT);
    $consulta->execute();
  }

  public static function borrarProducto($id)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta("DELETE FROM productos WHERE id = :id");
    $consulta->bindValue(':id', $id, PDO::PARAM_INT);
    return $consulta->execute();
  }

  public static function setTiempoDeFinalizacion($id, $minutos)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET tiempo_de_finalizacion = :tiempo_de_finalizacion WHERE id = :id");
    $consulta->bindValue(':tiempo_de_finalizacion', $minutos, PDO::PARAM_STR);
    $consulta->bindValue(':id', $id, PDO::PARAM_INT);
    $consulta->execute();

    $datosAux = $consulta->fetch(PDO::FETCH_BOTH);
    if ($datosAux) {
      return true;
    } else {
      return false;
    }
  }

  public static function setEstado($id, $id_estado)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET id_estado = :id_estado WHERE id = :id");
    $consulta->bindValue(':id_estado', $id_estado, PDO::PARAM_STR);
    $consulta->bindValue(':id', $id, PDO::PARAM_INT);
    $consulta->execute();

    $datosAux = $consulta->fetch(PDO::FETCH_BOTH);
    if ($datosAux) {
      return true;
    } else {
      return false;
    }
  }

  public static function verificarId($id)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta("SELECT * FROM productos WHERE id = :id");
    $consulta->bindValue(':id', $id, PDO::PARAM_INT);
    $consulta->execute();
    $datosAux = $consulta->fetch(PDO::FETCH_BOTH);
    if ($datosAux) {
      return true;
    } else {
      return false;
    }
  }

  public static function traerTipo($id)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta("SELECT id_tipo FROM productos WHERE id = :id");
    $consulta->bindValue(':id', $id, PDO::PARAM_INT);
    $consulta->execute();
    return $consulta->fetch();
  }

  public static function traerProductosPorTipo($id_tipo)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT id,
      id_tipo,
      descripcion,
      tiempo_de_finalizacion,
      id_estado
      FROM productos
      WHERE id_tipo = :id_tipo"
    );
    $consulta->bindValue(':id_tipo', $id_tipo, PDO::PARAM_INT);
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
  }


  public static function traerProductosEstadoTipo($id_estado, $id_tipo)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT id,
      id_tipo,
      descripcion,
      tiempo_de_finalizacion,
      id_estado
      FROM productos
      WHERE id_tipo = :id_tipo
      AND id_estado = :id_estado"
    );
    $consulta->bindValue(':id_tipo', $id_tipo, PDO::PARAM_INT);
    $consulta->bindValue(':id_estado', $id_estado, PDO::PARAM_INT);
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
  }

  public static function traerProductosPorPedido($id_pedido)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT
      productos.id,     
      tipo_productos.tipo,
      productos.descripcion,
      productos.tiempo_de_finalizacion,
      estado_productos.estado
      
      FROM productos_por_pedido
      INNER JOIN productos ON productos_por_pedido.id_producto = productos.id
      INNER JOIN pedidos ON productos_por_pedido.id_pedido = pedidos.id
      INNER JOIN tipo_productos ON tipo_productos.id = productos.id_tipo
      INNER JOIN estado_productos ON estado_productos.id = productos.id_estado
      WHERE pedidos.id = :id_pedido;"
    );
    $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoDTO');
  }

  public static function verificarProductosListosPorPedido($id_pedido)
  {
    $retorno  = true;
    $lista = Producto::traerProductosPorPedido($id_pedido);    
    foreach ($lista as $item) {
      // id_estado 2 es "listo para servir"
      if ($item->estado != "Listo para servir"){
        $retorno = false;        
      }
    }
    return $retorno;
  }

  public static function traerTiempoMasAltoPorPedido($id_pedido)
  {
    $tiempo_mayor_finalizacion = 0;
    $lista = Producto::traerProductosPorPedido($id_pedido);    
    foreach ($lista as $item) {      
      if ($item->tiempo_de_finalizacion > $tiempo_mayor_finalizacion){
        $tiempo_mayor_finalizacion = $item->tiempo_de_finalizacion;
      }
    }
    return $tiempo_mayor_finalizacion;
  }
}
