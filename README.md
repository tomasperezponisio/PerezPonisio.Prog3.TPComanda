# Tomás Pérez Ponisio - TP La Comanda

Los mozos toman los pedidos y manejan las mesas. Al dar de alta un pedido se le carga el nombre del cliente, se le asocia una mesa disponible (mesa que esté cerrada), se dan de alta los productos, se asocian al pedido y la mesa se pone ‘como cliente esperando pedido’.
Los bartenders, cerveceros y cocineros solo pueden ver y cambiar el estado y tiempo de finalización de los productos que le correspondan.
Cuando todos los productos asociados a un pedido estan listos para entregar, el mozo cambia el estado del pedido a ‘listo para servir’. Con el pedido listo el mozo lo entrega y le cambia el estado a ‘entregado’ finalizando la vida del pedido, la mesa cambia su estado a ‘con cliente comiendo’. Luego el mozo cambia el estado de la mesa a ‘con cliente pagando’. Finalmente algún socio cierra la mesa. 

https://drawsql.app/teams/tomas-perez-ponisios-team/diagrams/la-comanda


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

