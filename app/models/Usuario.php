<?php

include_once "UsuarioConCategoria.php";

class Usuario
{
  public $id;
  public $usuario;
  public $clave;
  public $id_categoria;  

  public function crearUsuario()
  {
    // var_dump($this->usuario);
    // var_dump($this->clave);
    // var_dump($this->id_categoria);
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave, id_categoria) VALUES (:usuario,:clave, :id_categoria)");
    $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
    $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
    $consulta->bindValue(':clave', $claveHash);
    $consulta->bindValue(':id_categoria', $this->id_categoria, PDO::PARAM_INT);
    $consulta->execute();

    return $objAccesoDatos->obtenerUltimoId();
  }

  public static function obtenerTodos()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT
      usuarios.id,
      usuarios.usuario,
      usuarios.clave,
      categoria_usuarios.categoria
      FROM usuarios
      JOIN categoria_usuarios
      ON usuarios.id_categoria = categoria_usuarios.id
      WHERE usuarios.fecha_baja IS NULL"
    );
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_CLASS, 'UsuarioConCategoria');
  }

  public static function obtenerUsuario($usuario)
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "SELECT
      id,
      usuario,
      clave,
      id_categoria
      FROM usuarios
      WHERE usuario = :usuario"
    );
    $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
    $consulta->execute();
    return $consulta->fetchObject('Usuario');
  }

  public static function modificarUsuario($id, $usuario, $clave, $id_categoria)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta(
      "UPDATE usuarios
      SET usuario = :usuario,
      clave = :clave,
      id_categoria = :id_categoria
      WHERE id = :id");
    $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
    $claveHash = password_hash($clave, PASSWORD_DEFAULT);
    $consulta->bindValue(':clave', $claveHash);
    $consulta->bindValue(':id_categoria', $id_categoria, PDO::PARAM_STR);
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
    $consulta = $objAccesoDato->prepararConsulta("SELECT fecha_baja FROM usuarios WHERE usuario = :usuario");
    $consulta->bindValue(':usuario', $usuario, PDO::PARAM_INT);
    $consulta->execute();
    $datosAux = $consulta->fetch(PDO::FETCH_ASSOC);
    if ($datosAux["fecha_baja"] == NULL) {
      return true;
    } else {
      return false;
    }
  }
}
