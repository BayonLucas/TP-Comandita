-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-07-2023 a las 18:47:28
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `_tp-comandita`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `_id` int(11) NOT NULL,
  `_nombre` varchar(20) NOT NULL,
  `_estado` int(11) NOT NULL,
  `_fechaIngreso` varchar(50) NOT NULL,
  `_codMesa` varchar(5) DEFAULT NULL,
  `_codPedido` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`_id`, `_nombre`, `_estado`, `_fechaIngreso`, `_codMesa`, `_codPedido`) VALUES
(14, 'Manchas', 1, '2023-07-04 12:34:47', '79A2B', 'RtlUi');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuestas`
--

CREATE TABLE `encuestas` (
  `_id` int(11) NOT NULL,
  `_idCliente` int(11) NOT NULL,
  `_idUsuario` int(11) NOT NULL,
  `_idPedido` int(11) NOT NULL,
  `_idMesa` int(11) NOT NULL,
  `_ptoMesa` int(11) NOT NULL,
  `_ptoMozo` int(11) NOT NULL,
  `_ptoResto` int(11) NOT NULL,
  `_ptoChef` int(11) NOT NULL,
  `_resenia` varchar(66) NOT NULL,
  `_fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `encuestas`
--

INSERT INTO `encuestas` (`_id`, `_idCliente`, `_idUsuario`, `_idPedido`, `_idMesa`, `_ptoMesa`, `_ptoMozo`, `_ptoResto`, `_ptoChef`, `_resenia`, `_fecha`) VALUES
(1, 14, 17, 10, 1, 10, 8, 9, 10, 'Lugar encantados, Servicio alabable, ambiente Increible', '2023-07-04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `_id` int(11) NOT NULL,
  `_estado` int(11) NOT NULL,
  `_codMesa` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`_id`, `_estado`, `_codMesa`) VALUES
(1, 5, '79A2B'),
(2, 0, 'AX257'),
(3, 0, 'B2G5T'),
(4, 0, 'QWE12'),
(5, 0, 'ASD45'),
(6, 0, 'ZCX78'),
(7, 0, 'RTY89'),
(8, 0, '56FGH');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `_id` int(11) NOT NULL,
  `_idProducto` int(11) NOT NULL,
  `_cantidad` int(11) NOT NULL,
  `_estado` int(11) NOT NULL,
  `_fechaInicio` varchar(50) DEFAULT NULL,
  `_fechaEstimadaFinal` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`_id`, `_idProducto`, `_cantidad`, `_estado`, `_fechaInicio`, `_fechaEstimadaFinal`) VALUES
(10, 2, 1, 2, '2023-07-04 13:09:07', '23-07-04 13:29:07'),
(10, 4, 2, 2, '2023-07-04 13:09:07', '23-07-04 13:49:07'),
(10, 3, 1, 2, '2023-07-04 13:09:07', '23-07-04 13:51:07'),
(10, 1, 1, 2, '2023-07-04 13:09:07', '23-07-04 13:56:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_producto`
--

CREATE TABLE `pedido_producto` (
  `_id` int(11) NOT NULL,
  `_idUsuario` int(11) NOT NULL,
  `_idCliente` int(11) NOT NULL,
  `_idMesa` int(11) NOT NULL,
  `_codPedido` varchar(5) NOT NULL,
  `_codMesa` varchar(5) NOT NULL,
  `_estado` int(11) NOT NULL,
  `_fechaIngreso` varchar(50) NOT NULL,
  `_tiempoTotalEspera` int(11) DEFAULT NULL,
  `_fechaFinalizado` varchar(50) DEFAULT NULL,
  `_importeTotal` int(11) NOT NULL,
  `_fotoCliente` varchar(50) DEFAULT NULL,
  `_fechaAnulado` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `pedido_producto`
--

INSERT INTO `pedido_producto` (`_id`, `_idUsuario`, `_idCliente`, `_idMesa`, `_codPedido`, `_codMesa`, `_estado`, `_fechaIngreso`, `_tiempoTotalEspera`, `_fechaFinalizado`, `_importeTotal`, `_fotoCliente`, `_fechaAnulado`) VALUES
(10, 17, 14, 1, 'RtlUi', '79A2B', 2, '2023-07-04 13:09:07', 30, '2023-07-04 13:47:05', 11500, '.\\_FotosPedidos\\2023-07-04-Manchas.jpg', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `_id` int(11) NOT NULL,
  `_idSector` int(11) NOT NULL,
  `_nombre` varchar(50) NOT NULL,
  `_precio` int(11) NOT NULL,
  `_tiempoPreparado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`_id`, `_idSector`, `_nombre`, `_precio`, `_tiempoPreparado`) VALUES
(1, 4, 'Daikiri', 1200, 5),
(2, 2, 'Mila a Caballo', 2500, 20),
(3, 3, 'Corona', 800, 2),
(4, 2, 'Hamburguesas de garbanzo', 3500, 20),
(5, 1, 'Volcan de Chocolate', 2500, 15),
(6, 1, 'Tiramisu', 2200, 5),
(7, 4, 'Tinto La Estancia', 1700, 2),
(8, 3, 'Honey', 700, 2),
(9, 3, 'IPA Negra', 950, 2),
(10, 3, 'Red Patagonia', 600, 2),
(11, 1, 'Flan', 1350, 4),
(12, 1, 'Budin de Pan', 1350, 4),
(13, 4, 'El 7mo Regimiento', 1700, 6),
(14, 4, 'Vino Blanco patero', 2500, 2),
(15, 2, 'Sorrentinos JyQ con Parisien', 4000, 25),
(16, 2, 'Pizza individual', 2100, 20);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sectores`
--

CREATE TABLE `sectores` (
  `_id` int(11) NOT NULL,
  `_nombre` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `sectores`
--

INSERT INTO `sectores` (`_id`, `_nombre`) VALUES
(1, 'Candy Bar'),
(2, 'Cocina'),
(3, 'Barra de Choperas'),
(4, 'Barra de Tragos y Vinos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `_id` int(11) NOT NULL,
  `_rol` varchar(15) NOT NULL,
  `_idSector` int(11) NOT NULL,
  `_nombre` varchar(15) NOT NULL,
  `_dni` varchar(8) NOT NULL,
  `_estado` int(11) NOT NULL,
  `_fechaRegistro` date NOT NULL,
  `_fechaBaja` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`_id`, `_rol`, `_idSector`, `_nombre`, `_dni`, `_estado`, `_fechaRegistro`, `_fechaBaja`) VALUES
(1, 'Socio', 1, 'Lucas', '40673980', 0, '2023-06-24', NULL),
(2, 'Socio', 1, 'Franco', '27858561', 0, '2023-06-24', NULL),
(3, 'Socio', 1, 'Agustin', '32165485', 0, '2023-06-24', NULL),
(4, 'Bartender', 4, 'Aldana', '40971565', 0, '2023-06-24', NULL),
(7, 'Bartender', 4, 'Ezrra', '22222222', 0, '2023-06-24', NULL),
(8, 'Cervecero', 3, 'Ezequiel', '38222222', 0, '2023-06-24', NULL),
(9, 'Cervecero', 3, 'Gabriel', '43987987', 0, '2023-06-24', NULL),
(10, 'Cocinero', 2, 'Agus', '43456456', 0, '2023-06-24', NULL),
(11, 'Cocinero', 2, 'Sol', '40321321', 0, '2023-06-24', NULL),
(12, 'Cocinero', 2, 'Florencia', '40159159', 0, '2023-06-24', NULL),
(13, 'Cocinero', 2, 'Raul', '16582462', 0, '2023-06-24', NULL),
(14, 'Mozo', 0, 'Maria', '24555555', 0, '2023-06-24', NULL),
(15, 'Mozo', 0, 'Maximo', '40321456', 0, '2023-06-24', NULL),
(16, 'Mozo', 0, 'Cecil', '11111111', 0, '2023-06-24', NULL),
(17, 'Mozo', 0, 'Clare', '99999999', 0, '2023-06-24', NULL),
(21, 'Mozo', 0, 'Pruebita', '7777777', 0, '2023-06-24', '2023-06-25');

--
-- Ýndices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`_id`);

--
-- Indices de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  ADD PRIMARY KEY (`_id`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`_id`);

--
-- Indices de la tabla `pedido_producto`
--
ALTER TABLE `pedido_producto`
  ADD PRIMARY KEY (`_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`_id`);

--
-- Indices de la tabla `sectores`
--
ALTER TABLE `sectores`
  ADD PRIMARY KEY (`_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `pedido_producto`
--
ALTER TABLE `pedido_producto`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `sectores`
--
ALTER TABLE `sectores`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
