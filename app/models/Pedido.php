<?php
include_once 'PedidoDTO.php';

class Pedido
{
  public $id;
  public $nombre_cliente;
  public $codigo_para_cliente;
  public $id_estado;

  public function crearPedido()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "INSERT INTO pedidos
      (nombre_cliente,
      codigo_para_cliente,
      id_estado)
      VALUES 
      (:nombre_cliente,
      :codigo_para_cliente,
      :id_estado)"
    );

    $consulta->bindValue(':nombre_cliente', $this->nombre_cliente, PDO::PARAM_STR);
    $consulta->bindValue(':codigo_para_cliente', $this->codigo_para_cliente, PDO::PARAM_STR);
    $consulta->bindValue(':id_estado', $this->id_estado, PDO::PARAM_INT);
    $consulta->execute();

    return $objAccesoDatos->obtenerUltimoId();
  }

  public static function obtenerTodos()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT 
      pedidos.id,
      pedidos.nombre_cliente,
      pedidos.codigo_para_cliente,
      estado_pedido.descripcion as 'estado'
      FROM pedidos
      JOIN estado_pedido ON pedidos.id_estado = estado_pedido.id");
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoDTO');
  }

  public static function obtenerPedido($id)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT 
      pedidos.id,
      pedidos.nombre_cliente,
      pedidos.codigo_para_cliente,
      estado_pedido.descripcion as 'estado'
      FROM pedidos
      JOIN estado_pedido
      ON pedidos.id_estado = estado_pedido.id
      WHERE pedidos.id = :id"
    );
    $consulta->bindValue(':id', $id, PDO::PARAM_INT);
    $consulta->execute();
    return $consulta->fetchObject('PedidoDTO');
  }

  public static function modificarPedido($id, $id_estado)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta(
      "UPDATE pedidos
      SET id_estado = :id_estado
      WHERE id = :id"
    );    
    $consulta->bindValue(':id_estado', $id_estado, PDO::PARAM_INT);
    $consulta->bindValue(':id', $id, PDO::PARAM_INT);
    $consulta->execute();
  }

  public static function borrarUsuario($usuarioId)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fecha_baja = :fecha_baja WHERE id = :id");
    $fecha = new DateTime(date("d-m-Y"));
    $consulta->bindValue(':id', $usuarioId, PDO::PARAM_INT);
    $consulta->bindValue(':fecha_baja', date_format($fecha, 'Y-m-d H:i:s'));
    return $consulta->execute();
  }

  public static function verificarId($id)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta("SELECT * FROM pedidos WHERE id = :id");
    $consulta->bindValue(':id', $id, PDO::PARAM_INT);
    $consulta->execute();
    $datosAux = $consulta->fetch(PDO::FETCH_BOTH);
    if ($datosAux) {
      return true;
    } else {
      return false;
    }
  }

  public static function registrarProductosPorPedido($id_pedido, $id_producto)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "INSERT INTO productos_por_pedido
      (id_producto,
      id_pedido)
      VALUES 
      (:id_producto,
      :id_pedido)"
    );

    $consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);
    $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $consulta->execute();

    return $objAccesoDatos->obtenerUltimoId();
  }

  public static function registrarPedidosDeUsuario($id_pedido, $id_usuario)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "INSERT INTO pedidos_de_usuario
      (id_usuario,
      id_pedido)
      VALUES 
      (:id_usuario,
      :id_pedido)"
    );

    $consulta->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $consulta->execute();

    return $objAccesoDatos->obtenerUltimoId();
  }

  public static function registrarPedidosEnMesa($id_pedido, $id_mesa)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "INSERT INTO pedidos_por_mesa
      (id_mesa,
      id_pedido)
      VALUES 
      (:id_mesa,
      :id_pedido)"
    );

    $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_INT);
    $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $consulta->execute();

    return $objAccesoDatos->obtenerUltimoId();
  }

}
