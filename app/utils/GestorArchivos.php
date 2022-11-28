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
}
