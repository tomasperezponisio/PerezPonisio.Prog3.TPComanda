<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';
require_once './utils/AutentificadorJWT.php';

class UsuarioController extends Usuario implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usuario = $parametros['usuario'];
    $id_categoria = $parametros['id_categoria'];

    // Creamos el usuario
    $usr = new Usuario();
    $usr->usuario = $usuario;
    $usr->id_categoria = $id_categoria;
    $usr->crearUsuario();

    $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos usuario por nombre
    $usr = $args['usuario'];
    $usuario = Usuario::obtenerUsuario($usr);
    $payload = json_encode($usuario);

    if ($payload != "false") {
      if (Usuario::estaActivo($usr)) {
        $response->getBody()->write($payload);
      } else {
        $response->getBody()->write("Usuario eliminado");
      }
    } else {
      $response->getBody()->write("Usuario inexistente");
    }
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Usuario::obtenerTodos();
    $payload = json_encode(array("listaUsuario" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $id = $parametros['id'];
    if (Usuario::verificarId($id)) {
      $usuario = $parametros['usuario'];
      $id_categoria = $parametros['id_categoria'];
      Usuario::modificarUsuario($id, $usuario, $id_categoria);
      $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "Error - ID Inexistente"));
    }
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usuarioId = $parametros['usuarioId'];
    if (Usuario::verificarId($usuarioId)) {
      if (Usuario::borrarUsuario($usuarioId)) {
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

  public function Login($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    // Creamos un usuario con los datos recibidos
    $usr = new Usuario();
    $usr->usuario = $parametros['usuario'];
    $usr->clave = $parametros['clave'];
    // Traigo un usuario de la BD con el nombre de usuario que recibi
    $usuario = Usuario::obtenerUsuario($usr->usuario);

    // y comparo nombre y clave
    if (
      $usuario->usuario == $usr->usuario &&
      $usuario->clave == $usr->clave
    ) {
      //----------------------------------------------------
      // devolver un JWT
      //---------------------------------------------------
      $datos = array('usuario' => $usr->usuario);
      $token = AutentificadorJWT::CrearToken($datos);
      $payload = json_encode(array('jwt' => $token));
      $response->getBody()->write($payload);
      // $response->getBody()->write("Logeado exitosamente");
    } else {
      $response->getBody()->write("Error en clave y/o usuario");
    }
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
