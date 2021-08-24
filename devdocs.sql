-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-02-2021 a las 07:21:45
-- Versión del servidor: 10.4.17-MariaDB
-- Versión de PHP: 8.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `devdocs`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docs`
--

CREATE TABLE `docs` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `descripcion` text NOT NULL,
  `imagen` text NOT NULL,
  `direccion` text NOT NULL,
  `idioma` text NOT NULL,
  `tipo` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `docs`
--

INSERT INTO `docs` (`id`, `nombre`, `descripcion`, `imagen`, `direccion`, `idioma`, `tipo`) VALUES
(1, 'Squirrel', 'Documentacion Squirrel', 'squirrel.png', 'squirrel/index.html', 'Ingles', 'Programación'),
(2, 'Codeigniter', 'Documentación Codeigniter', 'codeigniter.svg', 'codeigniter/index.html', 'Ingles', 'Programación'),
(3, 'W3Schools', 'W3Schools', 'w3schools.png', 'w3schools/index.html', 'Español', 'Programación'),
(21, 'Blender', 'Documentación Blender 2.91', 'blender3.png', 'blender', 'Español', 'Diseño');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `link` text NOT NULL,
  `idioma` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `nombre`, `link`, `idioma`) VALUES
(4, 'asas', 'asas', 'asas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `pass` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `pass`) VALUES
(1, 'WhiteAssassins', 'b661d2893ff2e316b6e243ed9b11aff3'),
(2, 'Erick13', '6d697a620dcb48de1de0406735e4b54e');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `docs`
--
ALTER TABLE `docs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `docs`
--
ALTER TABLE `docs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
