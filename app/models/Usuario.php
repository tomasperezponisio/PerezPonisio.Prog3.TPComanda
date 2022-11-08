<?php

class Usuario
{
  public $id;
  public $usuario;
  public $clave;

  public function crearUsuario()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave) VALUES (:usuario, :clave)");
    // $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
    $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
    $consulta->bindValue(':clave', $this->clave);
    $consulta->execute();

    return $objAccesoDatos->obtenerUltimoId();
  }

  public static function obtenerTodos()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave FROM usuarios WHERE fechaBaja IS NULL");
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
  }

  public static function obtenerUsuario($usuario)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, fechaBaja FROM usuarios WHERE usuario = :usuario");
    $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
    $consulta->execute();
    return $consulta->fetchObject('Usuario');
  }

  public static function modificarUsuario($id, $nombre, $clave)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave WHERE id = :id");
    $consulta->bindValue(':usuario', $nombre, PDO::PARAM_STR);
    // $claveHash = password_hash($clave, PASSWORD_DEFAULT);
    $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
    $consulta->bindValue(':id', $id, PDO::PARAM_INT);
    $consulta->execute();
  }

  public static function borrarUsuario($usuarioId)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
    $fecha = new DateTime(date("d-m-Y"));
    $consulta->bindValue(':id', $usuarioId, PDO::PARAM_INT);
    $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
    return $consulta->execute();
  }

  public static function verificarUsuario($usuario)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta("SELECT * FROM usuarios WHERE usuario = :usuario");
    $consulta->bindValue(':usuario', $usuario, PDO::PARAM_INT);
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
    $consulta = $objAccesoDato->prepararConsulta("SELECT * FROM usuarios WHERE id = :id");
    $consulta->bindValue(':id', $id, PDO::PARAM_INT);
    $consulta->execute();
    $datosAux = $consulta->fetch(PDO::FETCH_BOTH);
    if ($datosAux) {
      return true;
    } else {
      return false;
    }
  }

  public static function estaActivo($usuario)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta("SELECT fechaBaja FROM usuarios WHERE usuario = :usuario");
    $consulta->bindValue(':usuario', $usuario, PDO::PARAM_INT);
    $consulta->execute();
    $datosAux = $consulta->fetch(PDO::FETCH_ASSOC);
    if ($datosAux["fechaBaja"] == NULL) {
      return true;
    } else {
      return false;
    }
  }
}
