<?php

class Producto
{
  //   public $id;
  public $tipo;
  public $descripcion;
  public $tiempo_de_finalizacion;
  public $estado;

  // por default el tiempo de finalizacion es NULL y el estado 0 ("no ingresado en cocina")
  public function crearProducto()
  {
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta(
      "INSERT INTO productos (tipo, descripcion)
      VALUES (:tipo, :descripcion)"
    );

    $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
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

  public static function modificarProducto($id, $tipo, $descripcion)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET tipo = :tipo, descripcion = :descripcion WHERE id = :id");
    $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
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

  public function setTiempoDeFinalizacion($id, $minutos)
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

  public function setEstado($id, $estado)
  {
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET estado = :estado WHERE id = :id");
    $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
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


  // public static function verificarUsuario($usuario)
  // {
  //   $objAccesoDato = AccesoDatos::obtenerInstancia();
  //   $consulta = $objAccesoDato->prepararConsulta("SELECT * FROM usuarios WHERE usuario = :usuario");
  //   $consulta->bindValue(':usuario', $usuario, PDO::PARAM_INT);
  //   $consulta->execute();
  //   $datosAux = $consulta->fetch(PDO::FETCH_BOTH);
  //   if ($datosAux) {
  //     return true;
  //   } else {
  //     return false;
  //   }
  // }



  // public static function estaActivo($usuario)
  // {
  //   $objAccesoDato = AccesoDatos::obtenerInstancia();
  //   $consulta = $objAccesoDato->prepararConsulta("SELECT fecha_baja FROM usuarios WHERE usuario = :usuario");
  //   $consulta->bindValue(':usuario', $usuario, PDO::PARAM_INT);
  //   $consulta->execute();
  //   $datosAux = $consulta->fetch(PDO::FETCH_ASSOC);
  //   if ($datosAux["fecha_baja"] == NULL) {
  //     return true;
  //   } else {
  //     return false;
  //   }
  // }

  // -----------------------------------------------------
  // TIEMPO DE FINALIZACION Y ESTADO POR DATE
  // -----------------------------------------------------

  // public function setTiempoDeFinalizacion()
  // {
  //   // asigno el tiempo de finalizacion dependiendo del tipo de producto
  //   switch ($this->tipo) {
  //     case 'cerveza':
  //       // tiempo de preparacion de cerveza 5 min
  //       $this->tiempoDeFinalizacion = date('Y-m-d h:m:s', (strtotime(date('Y-m-d h:m:s')) + (60 * 5)));        
  //       break;
  //     case 'trago':
  //       // tiempo de preparacion de cerveza 10 min
  //       $this->tiempoDeFinalizacion = date('Y-m-d h:m:s', (strtotime(date('Y-m-d h:m:s')) + (60 * 10)));
  //       break;
  //     case 'comida':
  //       // tiempo de preparacion de cerveza 35 min
  //       $this->tiempoDeFinalizacion = date('Y-m-d h:m:s', (strtotime(date('Y-m-d h:m:s')) + (60 * 35)));        
  //       break;
  //   }
  // }

  // public function setEstado()
  // {
  //   // si el tiempo de finalizacion es mayor a la fecha y hora atcual, el estado es true (activo)
  //   if ($this->tiempoDeFinalizacion > date('d-m-Y h:i:s')) {
  //     $this->estado = true;
  //   } else {
  //     $this->estado = false;
  //   }
  // }

}
