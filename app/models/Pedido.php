<?php

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
    $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
  }

  public static function obtenerPedido($id)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE id = :id");
    $consulta->bindValue(':id', $id, PDO::PARAM_INT);
    $consulta->execute();
    return $consulta->fetchObject('Pedido');
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

}
