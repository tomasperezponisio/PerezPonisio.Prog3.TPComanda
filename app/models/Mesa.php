<?php
include_once 'MesaDTO.php';

class Mesa
{
  public $id;
  public $id_estado;

  // por default el estado de la mesa es 0 (cerrada)
  public function crearMesa()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "INSERT INTO mesas (id_estado)
      VALUES (:id_estado)"
    );

    $consulta->bindValue(':id_estado', 0, PDO::PARAM_INT);
    $consulta->execute();

    return $objAccesoDatos->obtenerUltimoId();
  }

  // me traigo una mesa por su id
  public static function obtenerMesa($id)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("SELECT
    mesas.id,
    estado_mesas.estado AS Estado
    FROM mesas
    JOIN estado_mesas
    ON mesas.id_estado = estado_mesas.id
    WHERE mesas.id = :id");
    $consulta->bindValue(':id', $id, PDO::PARAM_STR);
    $consulta->execute();
    return $consulta->fetchObject('Mesa');
  }

  public static function obtenerTodos()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT
      mesas.id,
      estado_mesas.estado AS Estado
      FROM mesas
      JOIN estado_mesas
      ON mesas.id_estado = estado_mesas.id"
    );
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_CLASS, 'MesaDTO');
  }

  public static function modificarMesa($id, $id_estado)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta(
      "UPDATE mesas
      SET id_estado = :id_estado
      WHERE id = :id"
    );
    $consulta->bindValue(':id_estado', $id_estado, PDO::PARAM_INT);
    $consulta->bindValue(':id', $id, PDO::PARAM_INT);
    $consulta->execute();
  }

  public static function traerIdMesaPorIdPedido($id_pedido)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta(
      "SELECT mesas.id
      FROM mesas
      JOIN pedidos_por_mesa ON pedidos_por_mesa.id_mesa = mesas.id
      JOIN pedidos on pedidos_por_mesa.id_pedido = pedidos.id
      WHERE pedidos.id = :id_estado;"
    );
    $consulta->bindValue(':id_estado', $id_pedido, PDO::PARAM_INT);      
    return $consulta->execute();
  }

  public static function borrarMesa($id)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta("DELETE FROM mesas WHERE id = :id");
    $consulta->bindValue(':id', $id, PDO::PARAM_INT);
    return $consulta->execute();
  }

  public static function verificarId($id)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta("SELECT * FROM mesas WHERE id = :id");
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
