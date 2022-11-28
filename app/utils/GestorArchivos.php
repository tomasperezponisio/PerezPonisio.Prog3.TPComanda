<?php

class GestorArchivos
{
  public $path;

  public function __construct($path)
  {
    if (!file_exists($path)) {
      mkdir($path, 077, true);
    }
    $this->path = $path;
  }

  public function GuardarFoto($nombre)
  {
    $retorno = false;
    $archivo = $nombre . ".jpg";
    $destino = $this->path . $archivo;
    if (
      $_FILES['foto']['type'] == 'image/jpeg' ||
      $_FILES['foto']['type'] == 'image/jpg' ||
      $_FILES['foto']['type'] == 'image/png'
    ) {
      move_uploaded_file($_FILES['foto']['tmp_name'], $destino);
      $retorno = $destino;
    }
    return $retorno;
  }

  public static function MoverArchivo($rutaOrigen, $rutaDestino, $rutaDestinoFull)
  {
    $retorno = false;
    if (!file_exists($rutaDestino)) {
      mkdir($rutaDestino, 077, true);
    }
    if (rename($rutaOrigen, $rutaDestinoFull)) {
      $retorno = true;
    }
    return $retorno;
  }


  public static function LeerUsuariosCSV($rutaArchivo)
  {
    $retorno = "Los usuarios ya existen";
    if (file_exists($rutaArchivo)) {
      $file = fopen($rutaArchivo, "r");
      $flag = 0;
      //obtengo cada linea del archivo csv, mientras fget no sea false, significa que no estoy al final del archivo
      while (($lineaArchivo = fgetcsv($file, 1000, ",")) !== false) {

        $usuario = new Usuario();
        $usuario->id = $lineaArchivo[0];
        $usuario->nombre = $lineaArchivo[1];
        $usuario->clave = $lineaArchivo[2];
        $usuario->id_rol = $lineaArchivo[3];
        if (Usuario::verificarUsuario($usuario->id)) {
          $flag = 1;
        } else {
          $flag = 0;
          $usuario->crearUsuario();
        }
      }

      if ($flag == 0) {
        $retorno = "Los usuarios no existentes han sido creados";
      }
    }

    return $retorno;
  }
}
