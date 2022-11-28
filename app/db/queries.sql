CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(4) AUTO_INCREMENT,
  `usuario` varchar(250) NOT NULL,
  `categoria` varchar(250) NOT NULL,
  PRIMARY KEY(id)
) AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` int(4) AUTO_INCREMENT,
  `nombre_cliente` varchar(250) NOT NULL,
  `codigo_para_cliente` varchar(5) NOT NULL,
  `estado` TINYINT(1) NOT NULL,
  PRIMARY KEY(id)
) AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `productos` (
  `id` int(4) AUTO_INCREMENT,
  `tipo` int(1) NOT NULL,
  `descripcion` varchar(250) NOT NULL,
  `tiempo_de_finalizacion` TIME NOT NULL,
  `estado` TINYINT(1) NOT NULL,
  PRIMARY KEY(id)
) AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `mesas` (
  `id` int(5) AUTO_INCREMENT,
  `estado` int(1) NOT NULL,  
  PRIMARY KEY(id)
) AUTO_INCREMENT = 10001;

CREATE TABLE IF NOT EXISTS `productos_por_pedido` (
  `id` int(4) AUTO_INCREMENT,
  `id_producto` int(4) NOT NULL,  
  `id_pedido` int(4) NOT NULL,  
  PRIMARY KEY(id)
) AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `productos_por_pedido` (
  `id` int(4) AUTO_INCREMENT,
  `id_producto` int(4) NOT NULL,  
  `id_pedido` int(4) NOT NULL,  
  PRIMARY KEY(id)
) AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `pedidos_de_usuario` (
  `id` int(4) AUTO_INCREMENT,
  `id_pedido` int(4) NOT NULL,  
  `id_usuario` int(4) NOT NULL,  
  PRIMARY KEY(id)
) AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `pedidos_por_mesa` (
  `id` int(4) AUTO_INCREMENT,
  `id_mesa` int(5) NOT NULL,  
  `id_pedido` int(4) NOT NULL,  
  PRIMARY KEY(id)
) AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `estado_pedido` (
  `id` int(4) AUTO_INCREMENT,
  `descripcion` varchar(250) NOT NULL,    
  PRIMARY KEY(id)
);

INSERT INTO `estado_pedido` (`descripcion`) VALUES
('En preparación'),
('Listo para servir'),
('Entregado');

CREATE TABLE IF NOT EXISTS `tipo_productos` (
  `id` int(4) AUTO_INCREMENT,
  `tipo` varchar(250) NOT NULL,    
  PRIMARY KEY(id)
) AUTO_INCREMENT = 0;

INSERT INTO `tipo_productos` (`tipo`) VALUES
('Cervezaa'),
('Trago'),
('Comida');

CREATE TABLE IF NOT EXISTS `estado_productos` (
  `id` int(4) AUTO_INCREMENT,
  `estado` varchar(250) NOT NULL,    
  PRIMARY KEY(id)
) AUTO_INCREMENT = 0;

INSERT INTO `estado_productos` (`estado`) VALUES
('No ingresado en cocina'),
('En preparación'),
('Listo para servir');