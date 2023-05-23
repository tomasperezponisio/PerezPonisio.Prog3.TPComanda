# Tomás Pérez Ponisio - La Comanda (APIR rest con PHP (Slim Framework))

## Requerimientos de la aplicación

Debemos realizar un sistema para un restaurante que tiene tres sectores: ***barra de tragos y vino***, ***patio cervecero*** y ***cocina***.

Los trabajadores estan diferenciados entre los ***bartender*** , los ***cerveceros***, los ***cocineros*** y los ***mozos***, también estamos los ***socios*** del local.

Los mozos toman los pedidos y manejan las mesas. Al dar de alta un pedido se le carga el nombre del cliente, se le asocia una mesa disponible (mesa que esté cerrada), se dan de alta los productos, se asocian al pedido y la mesa se pone ‘como cliente esperando pedido’.

Los bartenders, cerveceros y cocineros solo pueden ver y cambiar el estado y tiempo de finalización de los productos que le correspondan.

Cuando todos los productos asociados a un pedido estan listos para entregar, el mozo cambia el estado del pedido a ‘listo para servir’. Con el pedido listo el mozo lo entrega y le cambia el estado a ‘entregado’ finalizando la vida del pedido, la mesa cambia su estado a ‘con cliente comiendo’. Luego el mozo cambia el estado de la mesa a ‘con cliente pagando’. Finalmente algún socio cierra la mesa. 

## Endpoints de la API

Hay varios endpoints para las diferentes necesidades de la aplicación:
- /usuarios - Para el manejo de los trabajadores, alta, baja, modificación, back up de la base de usuarios.
- /login - Para que los trabajdores se logueen al sistema.
- /pedidos - Para el manejo de los pedidos, alta, baja y modificación.
- /productos - Para el manejo de los productos de los distintos sectores del restaurante.
- /bartenders - Para que los bartenders puedan ver los pedidos que tienen que preparar y actualizar el estado de los mismos.
- /cerveceros - Para que los cerveceros puedan ver los pedidos que tienen que preparar y actualizar el estado de los mismos.
- /cocineros - Para que los bartenders puedan ver los pedidos que tienen que preparar y actualizar el estado de los mismos.
- /mesas - Para ver que mesas estan libres, cuales ocupadas, manejar la sala.
- /clientes - Para que los clientes puedan ver el estado de su pedido

```php
# Endpoints para los usuarios
// Usuarios
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  // $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
  $group->get('/backupusuarios', \UsuarioController::class . ':DescargarCSV')
    ->add(new SocioCheckerMiddleware())
    ->add(new VerificarTokenMiddleware()); 
  $group->post('[/]', \UsuarioController::class . ':CargarUno')
    ->add(new UsuarioYCategoriaCheckerMiddleware())
    ->add(new SocioCheckerMiddleware())
    ->add(new VerificarTokenMiddleware());
  $group->put('[/]', \UsuarioController::class . ':ModificarUno')->add(new UsuarioYCategoriaCheckerMiddleware())->add(new IdCheckerMiddleware());
  $group->delete('[/]', \UsuarioController::class . ':BorrarUno')->add(new IdCheckerMiddleware());
  $group->post('/login', \UsuarioController::class . ':Login')->add(new LoginMiddleware()); 
  $group->post('/backupusuarios', \UsuarioController::class . ':CargarDatosDesdeCSV'); 
});
```

## Middlewares

En todos los endpoints hay algun middleware sea de verificación de tipo de trabajador, o si es un socio, si esta logueado al sistema o no, para checkear la info que al realizar un pedido, etc.

```php
# Middleware de login, chequea que manden los datos de usuario y contraseña
# y que no esten vacios
class LoginMiddleware
{
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();
    // traigo los parametros
    $parametros = $request->getParsedBody();
    // me fijo si me mandaron usuario y clave
    if (isset($parametros['clave']) && isset($parametros['usuario'])) {
      // me fijo que no esten vacios
      if ($parametros['clave'] == "" || $parametros['usuario'] == "") {
        $response->getBody()->write("Error Campo vacio");
      } else {
        // si no estan vacios paso el request
        $response = $handler->handle($request);
      }
    } else {
      // aviso que faltaron datos
      $response->getBody()->write("Datos incompletos");
    }
    return $response;
  }
}
```

```php
# Middleware que chequea que el request venga de un socio
# Verifica que venga un token válido, y que el mismo sea de socio
# En caso contrario avisa que el requester no tiene permiso de socio
class SocioCheckerMiddleware
{
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();
    $esValido = false;
    $payload = "";
    $token = "";

    try {
      $header = $request->getHeaderLine('Authorization');
      if ($header != null) {
        $token = trim(explode("Bearer", $header)[1]);
      }
      AutentificadorJWT::verificarToken($token);
      $esValido = true;
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    if (
      $esValido &&
      AutentificadorJWT::ObtenerData($token)->id_categoria == "0"
    ) {
      $response = $handler->handle($request);
    } else {
      $payload = json_encode(array("Error" => "No tiene permisos de: Socio"));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}
```

## Diagrama de BBDD

https://drawsql.app/teams/tomas-perez-ponisios-team/diagrams/la-comanda

```php
# Uno de los models de producto, trae los productos por tipo (cerveza, comida, etc)
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

```

## Usuarios

![usuarios2](https://user-images.githubusercontent.com/4174170/201730164-1ea7bd61-80fd-4bf3-b9e9-983d5bbb2d96.png)

Los usuarios:

- id:
- id_categoría:
  - 0 => socio: puede ver todo
  - 1 => mozo: ve los pedidos y mesas
  - 2 => bartender: ve los tragos 
  - 3 => cervecero: ver las cervezas
  - 4 => cocinero: ve la comida
- usuario: nombre del socio
- fecha_baja:


## Productos

![productos2](https://user-images.githubusercontent.com/4174170/201729729-01b32513-2d86-401e-bcb9-5b419a592e94.png)

Cuando todos los productos asociados a un pedido tienen el estado "listo para servir", el pedido cambia su estado a "listo para servir"

- id: 
- id_itpo:
  - 0 => Cerveza
  - 1 => Trago
  - 2 => Comida
- descripción: la descripción del producto (Corona, Fernet, Hamburguesa, etc)
- tiempo_de_finalizacion: tiempo en minutos => null por default
- id_estado:
  - 0 => “no ingresado en cocina” => estado por default
  - 1 => “en preparación”
  - 2 => “listo para servir”
- Preguntar por esto.
  - La verificacion si la consulta retorna algo para devolver true:

## Mesas

![mesas2](https://user-images.githubusercontent.com/4174170/201730249-8623e1a4-ba19-45a4-bcea-f6974e09013f.png)

Las mesas por default estan cerradas y solo en ese estado pueden ser asignadas a un pedido. Las manejan mozos y socios. Solo los socios las pueden cerrar.

- id:
- id_estado:
  - 0 => Cerrada
  - 1 => Con cliente esperando pedido
  - 2 => Con cliente comiendo
  - 3 => Con cliente pagando

## Pedidos

![pedidos2](https://user-images.githubusercontent.com/4174170/201730386-2f21bc49-3af1-47b8-a041-f4141febf1b8.png)

Los pedidos los hacen solo los mozos. Estan asociados a una mesa, y tienen asociados productos. Recien cuando todos los pedidos asociados estan 'listos para servir', el pedido pasa a 'listo para servir', y luego a 'entregado' finalizando la vida del mismo.

- id:
- nombre_cliente:
- codigo_para_cliente:
- id_estado:
  - 0 => En preparacion
  - 1 => Listo para servir
  - 2 => Entregado






```sh
cd C:\<ruta-del-repo-clonado>
composer update
php -S localhost:666 -t app
```

- Abrir desde http://localhost:666/

