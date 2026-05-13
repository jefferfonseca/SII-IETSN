-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-04-2026 a las 15:29:45
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ietsannicolas`
--
CREATE DATABASE IF NOT EXISTS `ietsannicolas` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ietsannicolas`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--

DROP TABLE IF EXISTS `asistencia`;
CREATE TABLE `asistencia` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `id_grado` int(11) NOT NULL,
  `estado` enum('presente','ausente') DEFAULT 'presente',
  `metodo` enum('qr','manual','prestamo') DEFAULT 'manual',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asistencia`
--

INSERT INTO `asistencia` (`id`, `id_usuario`, `fecha`, `id_grado`, `estado`, `metodo`, `created_at`) VALUES
(1, 78, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:17:05'),
(2, 91, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:17:08'),
(3, 83, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:17:10'),
(4, 82, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:17:13'),
(5, 89, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:17:16'),
(6, 94, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:17:20'),
(7, 76, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:17:24'),
(8, 93, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:17:26'),
(9, 92, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:17:28'),
(10, 79, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:17:30'),
(11, 87, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:17:31'),
(12, 75, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:17:33'),
(13, 72, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:17:35'),
(14, 73, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:17:38'),
(15, 80, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:17:41'),
(16, 84, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:17:48'),
(17, 88, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:17:52'),
(18, 90, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:17:57'),
(19, 74, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:18:00'),
(20, 86, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:18:01'),
(21, 95, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:18:03'),
(22, 81, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:18:04'),
(23, 85, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:18:06'),
(24, 77, '2026-04-06', 5, 'presente', 'manual', '2026-04-06 13:18:07'),
(25, 125, '2026-04-06', 7, 'presente', 'manual', '2026-04-06 18:50:52'),
(26, 121, '2026-04-06', 7, 'presente', 'manual', '2026-04-06 18:50:53'),
(27, 124, '2026-04-06', 7, 'presente', 'manual', '2026-04-06 18:50:53'),
(28, 127, '2026-04-06', 7, 'presente', 'manual', '2026-04-06 18:50:54'),
(29, 123, '2026-04-06', 7, 'presente', 'manual', '2026-04-06 18:50:55'),
(30, 128, '2026-04-06', 7, 'presente', 'manual', '2026-04-06 18:50:56'),
(31, 130, '2026-04-06', 7, 'presente', 'manual', '2026-04-06 18:50:56'),
(32, 131, '2026-04-06', 7, 'presente', 'manual', '2026-04-06 18:50:58'),
(33, 136, '2026-04-06', 7, 'presente', 'manual', '2026-04-06 18:50:58'),
(34, 129, '2026-04-06', 7, 'presente', 'manual', '2026-04-06 18:50:59'),
(35, 134, '2026-04-06', 7, 'presente', 'manual', '2026-04-06 18:50:59'),
(36, 135, '2026-04-06', 7, 'presente', 'manual', '2026-04-06 18:51:00'),
(37, 118, '2026-04-06', 7, 'presente', 'manual', '2026-04-06 18:51:00'),
(38, 120, '2026-04-06', 7, 'presente', 'manual', '2026-04-06 18:51:00'),
(39, 122, '2026-04-06', 7, 'presente', 'manual', '2026-04-06 18:51:04'),
(40, 133, '2026-04-06', 7, 'presente', 'manual', '2026-04-06 18:51:05'),
(41, 119, '2026-04-06', 7, 'ausente', 'manual', '2026-04-06 18:51:06'),
(42, 126, '2026-04-06', 7, 'presente', 'manual', '2026-04-06 18:51:07'),
(43, 132, '2026-04-06', 7, 'presente', 'manual', '2026-04-06 18:51:07'),
(44, 108, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:34:21'),
(45, 116, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:34:23'),
(46, 105, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:34:25'),
(47, 113, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:34:28'),
(48, 99, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:34:31'),
(49, 114, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:34:33'),
(50, 96, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:34:38'),
(51, 97, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:34:45'),
(52, 117, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:34:52'),
(53, 112, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:34:54'),
(54, 110, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:34:57'),
(55, 109, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:35:01'),
(56, 100, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:35:03'),
(57, 102, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:35:05'),
(58, 103, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:35:06'),
(59, 98, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:35:10'),
(60, 111, '2026-04-07', 6, 'ausente', 'manual', '2026-04-07 12:35:12'),
(61, 107, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:35:19'),
(62, 101, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:35:22'),
(63, 106, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:35:24'),
(64, 104, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:35:25'),
(65, 115, '2026-04-07', 6, 'presente', 'manual', '2026-04-07 12:35:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

DROP TABLE IF EXISTS `bitacora`;
CREATE TABLE `bitacora` (
  `id` int(11) NOT NULL,
  `id_elemento` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `accion` enum('prestamo','devolucion','mantenimiento','fuera_servicio','observacion') NOT NULL,
  `detalle` text NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `hash_actual` char(64) NOT NULL,
  `hash_anterior` char(64) DEFAULT NULL,
  `estado_anterior` enum('Disponible','Prestado','Mantenimiento','Fuera de servicio') DEFAULT NULL,
  `estado_nuevo` enum('Disponible','Prestado','Mantenimiento','Fuera de servicio') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `codigo`, `nombre`, `activo`) VALUES
(1, 'PC', 'Computador', 1),
(2, 'CG', 'Cargador', 1),
(3, 'IMP', 'Impresora', 1),
(4, 'PRO', 'Proyector', 1),
(5, 'SW', 'Switch', 1),
(6, 'RT', 'Router', 1),
(7, 'TV', 'Televisor', 1),
(8, 'OT', 'Otro', 1),
(9, 'EX', 'Extensión', 1),
(10, 'DC', 'Deco', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `elementos`
--

DROP TABLE IF EXISTS `elementos`;
CREATE TABLE `elementos` (
  `id_elemento` int(11) NOT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `codigo` varchar(30) NOT NULL,
  `serial` varchar(100) DEFAULT NULL,
  `qr_token` varchar(32) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `estado` enum('Disponible','Prestado','Mantenimiento','Fuera de servicio') DEFAULT NULL,
  `observaciones_generales` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `elementos`
--

INSERT INTO `elementos` (`id_elemento`, `id_categoria`, `codigo`, `serial`, `qr_token`, `nombre`, `estado`, `observaciones_generales`, `created_at`) VALUES
(1, 1, 'LAB-PC-001', 'F286754', 'e0bfec1f48aacde5f7a90bfdf8897af0', 'PC DELL', 'Disponible', 'PC Nuevo', '2026-02-23 13:12:07'),
(2, 1, 'LAB-PC-002', '55Q5754', '2b59080a849904a63120be85b3c2f8d0', 'PC DELL', 'Disponible', 'PC Nuevo', '2026-02-23 13:12:08'),
(3, 1, 'LAB-PC-003', '4RXCQ74', 'c80418dea5440949425c1573bc619b76', 'PC DELL', 'Disponible', 'PC Nuevo', '2026-02-23 13:12:08'),
(4, 1, 'LAB-PC-004', '7MRCQ74', 'f89f670da0aabdfedee2b4ec054b2216', 'PC DELL', 'Disponible', 'PC Nuevo', '2026-02-23 13:12:08'),
(5, 1, 'LAB-PC-005', '884FQ74', '4ee2bcc64c00bb9ffb7552ac0e7e9fe8', 'PC DELL', 'Disponible', 'PC Nuevo', '2026-02-23 13:12:08'),
(6, 1, 'LAB-PC-006', 'JTXCQ74', '82efa5134bd7411c67e3195d433ad764', 'PC DELL', 'Disponible', 'PC Nuevo', '2026-02-23 13:12:08'),
(7, 1, 'LAB-PC-007', 'TJ8DQ74', '7ba935b200d16f533589c4394cb3a740', 'PC DELL', 'Disponible', 'PC Nuevo', '2026-02-23 13:12:08'),
(8, 1, 'LAB-PC-008', 'FZ76754', '8fa5f9f3984c44fbf8e391fd3008f935', 'PC DELL', 'Disponible', 'PC Nuevo', '2026-02-23 13:12:08'),
(9, 1, 'LAB-PC-009', '75Q5754', '36c1f17e950115b35f50a3a0244bc5a9', 'PC DELL', 'Disponible', 'PC Nuevo', '2026-02-23 13:12:08'),
(10, 1, 'LAB-PC-010', 'JZXCQ74', 'bada54bad2830656659589a9b99a9908', 'PC DELL', 'Disponible', 'PC Nuevo', '2026-02-23 13:12:08'),
(11, 1, 'LAB-PC-011', '2123BP010307', 'cdb77f5e4b45fe50da9a75f2285faf76', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(12, 1, 'LAB-PC-012', '2123BP010319', 'b0a32d74ef590eecd4421695233cc887', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(13, 1, 'LAB-PC-013', '2123BP009794', 'cee8efb1073c7c3aabffd9a8aac68cc6', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(14, 1, 'LAB-PC-014', '2123BP012916', '733ee8e28a1bef82b8c0ac5abd6cefaa', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(15, 1, 'LAB-PC-015', '2123BP010314', '3a67de86d2ba312e31b9043e7c95af37', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(16, 1, 'LAB-PC-016', '2123BP002338', 'cda91a7f80cd07f6964ddd17e4ac36e8', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(17, 1, 'LAB-PC-017', 'PE2021009753', '5985ea2656e2528e990e2d468ec24d46', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(18, 1, 'LAB-PC-018', '2123BP009933', '842df662180f06dd0cceea0415a13c7d', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(19, 1, 'LAB-PC-019', 'PG2021009936', 'c5913d0dfba83a90605bfb2c6772a345', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(20, 1, 'LAB-PC-020', '2123BP002649', 'f6b1b7b006135f16eddafa181ae9a3f7', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(21, 1, 'LAB-PC-021', 'PG2021007454', '465824e9cb400e2967b4c881381c7213', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(22, 1, 'LAB-PC-022', '2123BP006344', 'd6cc69723da73c72c22fc658eb8968ba', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(23, 1, 'LAB-PC-023', 'PG2021009668', '6fdcc26c6d32f9eecc0732ec2a083f8d', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(24, 1, 'LAB-PC-024', '2123BP000985', '3da938db194cf4ac8651aa6cb883e4d0', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(25, 1, 'LAB-PC-025', '2123BP012929', '57e05ec2fbcbba77c04f108982845068', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(26, 1, 'LAB-PC-026', '2123BP005495', '063018fae4418607857ab05d15f58f7d', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(27, 1, 'LAB-PC-027', '2223BP007154', '5628be02db5f5ccda24f2be0c7930a3c', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(28, 1, 'LAB-PC-028', '2223BP015258', 'eb3cb0dc81a667adbd7196718c8e21eb', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(29, 1, 'LAB-PC-029', '2223BP007604', 'edb0a51eee2f3844f7e528630aba68ba', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(30, 1, 'LAB-PC-030', '2223BP005038', 'c41deb038cb0a63ebdf6b97621176d53', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(31, 1, 'LAB-PC-031', '2223BP008987', '11de6ee036a76b5cb13f9dbe738171fb', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(32, 1, 'LAB-PC-032', '2223BP006330', '6afb26a17214f76f8003d0181415f7ba', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(33, 1, 'LAB-PC-033', '2223BP026089', '39ba7e1bed0c6682d37aa62daf91db24', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(34, 1, 'LAB-PC-034', '2223BP014561', '6b574ec41050eb5928b84aed27c5c874', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(35, 1, 'LAB-PC-035', '2223BP031592', '3ff34764771bcaef00f6df17af818a9e', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(36, 1, 'LAB-PC-036', '2223BP006319', '32fb54209df1fbf9fcdb53f013d68004', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(37, 1, 'LAB-PC-037', '2223BP027575', 'e756290f1f7b09723c2a8970d185c165', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(38, 1, 'LAB-PC-038', '2223BP012066', 'bac209c8ccefc97d7f6593fe13778e5b', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(39, 1, 'LAB-PC-039', '2223BP013284', '19e539e2e267beabdf3e652eb953d4cd', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(40, 1, 'LAB-PC-040', '2223BP012504', '9600bffc52ea31e05da34e3b2ddf4cbf', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(41, 1, 'LAB-PC-041', '2223BP004367', '8a2abb08f3f886154d6e46771d6829d5', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(42, 1, 'LAB-PC-042', '2223BP013361', '9661fb24dbc3ae295bf7b8454998dec6', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(43, 1, 'LAB-PC-043', '2223BP007068', 'abf8f287e9a27ae8c5ef0b2194c6d0c2', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(44, 1, 'LAB-PC-044', '2223BP007011', '947e2b715f9cf44d4cc443f86fc2425f', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(45, 1, 'LAB-PC-045', '2223BP012096', '29ad5531a75af2e2b52d8102421be96e', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(46, 1, 'LAB-PC-046', '2223BP012040', 'a01f317718003e3ec7e324b3cd3a2722', 'PC Compumax', 'Disponible', 'Sin Novedad', '2026-02-23 13:12:08'),
(93, 2, 'LAB-CG-001', 'LAB-CG-001', '59c7caf65e7c030a51470f90d04fdca1', 'Cargador PC DELL', 'Disponible', 'Cargador PC Nuevo', '2026-02-27 14:13:13'),
(94, 2, 'LAB-CG-002', 'LAB-CG-002', '2b4acddfc4072b13eaff55fc33de7675', 'Cargador PC DELL', 'Disponible', 'Cargador PC Nuevo', '2026-02-27 14:13:13'),
(95, 2, 'LAB-CG-003', 'LAB-CG-003', 'a8606553f70ca390c0e1e9bf67ed9698', 'Cargador PC DELL', 'Disponible', 'Cargador PC Nuevo', '2026-02-27 14:13:13'),
(96, 2, 'LAB-CG-004', 'LAB-CG-004', 'deba9ea639f39fccc130412fe97eba4b', 'Cargador PC DELL', 'Disponible', 'Cargador PC Nuevo', '2026-02-27 14:13:13'),
(97, 2, 'LAB-CG-005', 'LAB-CG-005', '44aa8e26db9681f2669e679157887d9c', 'Cargador PC DELL', 'Disponible', 'Cargador PC Nuevo', '2026-02-27 14:13:13'),
(98, 2, 'LAB-CG-006', 'LAB-CG-006', '41964c84906f90ceecc00be25c943882', 'Cargador PC DELL', 'Disponible', 'Cargador PC Nuevo', '2026-02-27 14:13:13'),
(99, 2, 'LAB-CG-007', 'LAB-CG-007', '989309ce35a461e5f32c6cd497befde2', 'Cargador PC DELL', 'Disponible', 'Cargador PC Nuevo', '2026-02-27 14:13:13'),
(100, 2, 'LAB-CG-008', 'LAB-CG-008', '3e67aa7f851fc1fb5e4e589e3fba796f', 'Cargador PC DELL', 'Disponible', 'Cargador PC Nuevo', '2026-02-27 14:13:13'),
(101, 2, 'LAB-CG-009', 'LAB-CG-009', '937b5f6ec85c70a113986172af9e53ef', 'Cargador PC DELL', 'Disponible', 'Cargador PC Nuevo', '2026-02-27 14:13:13'),
(102, 2, 'LAB-CG-010', 'LAB-CG-010', '3be5ba7bdd705b74c104210cc7699790', 'Cargador PC DELL', 'Disponible', 'Cargador PC Nuevo', '2026-02-27 14:13:13'),
(103, 2, 'LAB-CG-011', 'LAB-CG-011', '0cf235bc9a1b3944508a9caaa4a9930e', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(104, 2, 'LAB-CG-012', 'LAB-CG-012', 'd0f8bf3a7918230be81f4ff4d024ca17', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(105, 2, 'LAB-CG-013', 'LAB-CG-013', 'fe781d2a26dad4ec90943ca53500718f', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(106, 2, 'LAB-CG-014', 'LAB-CG-014', '965abfb142075aec21a2097e8059dc8e', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(107, 2, 'LAB-CG-015', 'LAB-CG-015', '48d8a30afc4d4f82e6ff78fa4980bdf3', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(108, 2, 'LAB-CG-016', 'LAB-CG-016', '7a7c53aadad556a6d8e22ac76a6514b8', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(109, 2, 'LAB-CG-017', 'LAB-CG-017', '3ce8f2585e985e50bdd4936afe0b4285', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(110, 2, 'LAB-CG-018', 'LAB-CG-018', '6de441a6b230b6ee2ac8110e5a40da0f', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(111, 2, 'LAB-CG-019', 'LAB-CG-019', '440a296711fbc253b6dfc68b5d439ee0', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(112, 2, 'LAB-CG-020', 'LAB-CG-020', '4847f4c61f99f38d12b1babbffcae05e', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(113, 2, 'LAB-CG-021', 'LAB-CG-021', 'f5fb953527ced61b99a59ae55045e783', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(114, 2, 'LAB-CG-022', 'LAB-CG-022', 'f5ce62e1fe2863fe96ae68d467c3990c', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(115, 2, 'LAB-CG-023', 'LAB-CG-023', '51608152c6ca78908cf4797985900928', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(116, 2, 'LAB-CG-024', 'LAB-CG-024', 'ac84fa060081a2078cabb4b5d1f9deaa', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(117, 2, 'LAB-CG-025', 'LAB-CG-025', '7ae8b915d0f6e8443dec1c24c9471302', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(118, 2, 'LAB-CG-026', 'LAB-CG-026', 'f8203e987e3f28884ff12ad43be0215c', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(119, 2, 'LAB-CG-027', 'LAB-CG-027', 'fe16c83c365011d47ae2367c4df23b21', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(120, 2, 'LAB-CG-028', 'LAB-CG-028', '8b7685a704379c1037e1bca30c48ffa8', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(121, 2, 'LAB-CG-029', 'LAB-CG-029', '454fcb1bee5c9a8ab041269f53dd27ba', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(122, 2, 'LAB-CG-030', 'LAB-CG-030', 'e33bed1f53878850855d930354f44696', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(123, 2, 'LAB-CG-031', 'LAB-CG-031', 'd592ac08146c0f7e1e35436919cf33fe', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(124, 2, 'LAB-CG-032', 'LAB-CG-032', 'de4a3a885e0abd6a29ecc617da137940', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(125, 2, 'LAB-CG-033', 'LAB-CG-033', 'fc40dfe0cd80d8b69e2303efa5fa2769', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(126, 2, 'LAB-CG-034', 'LAB-CG-034', '105e3fe4ce03269ae7623cff672f55d9', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(127, 2, 'LAB-CG-035', 'LAB-CG-035', '5d1b7eb640c7b4ba7ca383c5e38555c4', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(128, 2, 'LAB-CG-036', 'LAB-CG-036', '482b762894f18003ab2031ec4b1f3ed7', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(129, 2, 'LAB-CG-037', 'LAB-CG-037', 'fd56b10238d2a12d4a785bb670080b92', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(130, 2, 'LAB-CG-038', 'LAB-CG-038', '453d6910c301c133e885516a824f1524', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(131, 2, 'LAB-CG-039', 'LAB-CG-039', 'c19abb9a613c7d666ffb638ada51d980', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(132, 2, 'LAB-CG-040', 'LAB-CG-040', 'e56b8ed943625943f2e2b8e447994436', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(133, 2, 'LAB-CG-041', 'LAB-CG-041', 'cb9d7e2294915c7311457c18e8bcd650', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(134, 2, 'LAB-CG-042', 'LAB-CG-042', 'b453c1fe7a5b8c766679cfd1aa716da2', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(135, 2, 'LAB-CG-043', 'LAB-CG-043', 'fed39dedced6d80e8d5c9fc53dcac025', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(136, 2, 'LAB-CG-044', 'LAB-CG-044', '49578cc0cdc7d25a464211de618108ef', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(137, 2, 'LAB-CG-045', 'LAB-CG-045', '90a2c1b9ecd947598885204c2e06d2c8', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13'),
(138, 2, 'LAB-CG-046', 'LAB-CG-046', 'e7095c08ab53580b2f72a07fb35266dc', 'Cargador PC Compumax', 'Disponible', 'Cargador PC Antiguo', '2026-02-27 14:13:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grados`
--

DROP TABLE IF EXISTS `grados`;
CREATE TABLE `grados` (
  `id_grado` int(11) NOT NULL,
  `nombre` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grados`
--

INSERT INTO `grados` (`id_grado`, `nombre`) VALUES
(6, '10A'),
(7, '10B'),
(8, '11'),
(1, '6'),
(2, '7'),
(3, '8A'),
(4, '8B'),
(5, '9');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

DROP TABLE IF EXISTS `prestamos`;
CREATE TABLE `prestamos` (
  `id` int(11) NOT NULL,
  `id_tomador` int(11) NOT NULL,
  `id_elemento` int(11) NOT NULL,
  `id_operador` int(11) NOT NULL,
  `fecha_prestamo` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_devolucion` datetime DEFAULT NULL,
  `estado` enum('activo','devuelto') NOT NULL DEFAULT 'activo',
  `observacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `prestamos`
--
DROP TRIGGER IF EXISTS `trg_bitacora_devolucion`;
DELIMITER $$
CREATE TRIGGER `trg_bitacora_devolucion` AFTER UPDATE ON `prestamos` FOR EACH ROW BEGIN
    IF OLD.estado = 'activo' AND NEW.estado = 'devuelto' THEN
        INSERT INTO bitacora (
            id_elemento,
            id_usuario,
            accion,
            detalle,
            fecha
        ) VALUES (
            NEW.id_elemento,
            NEW.id_tomador,
            'devolucion',
            CONCAT(
                'Elemento devuelto. Operador ID ',
                NEW.id_operador
            ),
            NEW.fecha_devolucion
        );
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_bitacora_prestamo`;
DELIMITER $$
CREATE TRIGGER `trg_bitacora_prestamo` AFTER INSERT ON `prestamos` FOR EACH ROW BEGIN
    INSERT INTO bitacora (
        id_elemento,
        id_usuario,
        accion,
        detalle,
        fecha
    ) VALUES (
        NEW.id_elemento,
        NEW.id_tomador,
        'prestamo',
        CONCAT(
            'Préstamo registrado por operador ID ',
            NEW.id_operador
        ),
        NEW.fecha_prestamo
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas_aseo`
--

DROP TABLE IF EXISTS `tareas_aseo`;
CREATE TABLE `tareas_aseo` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_grado` int(11) NOT NULL,
  `actividad` enum('barrer','ordenar_mesas','ordenar_sillas','vaciar_canecas','trapear') NOT NULL,
  `fecha` date NOT NULL,
  `estado` enum('pendiente','completado','ausente') DEFAULT 'pendiente',
  `ciclo` int(11) DEFAULT 1,
  `orden` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tareas_aseo`
--

INSERT INTO `tareas_aseo` (`id`, `id_usuario`, `id_grado`, `actividad`, `fecha`, `estado`, `ciclo`, `orden`, `created_at`) VALUES
(1, 84, 5, 'barrer', '2026-04-06', 'completado', 1, 0, '2026-04-06 13:18:09'),
(2, 78, 5, 'barrer', '2026-04-06', 'completado', 1, 0, '2026-04-06 13:18:09'),
(3, 74, 5, 'ordenar_mesas', '2026-04-06', 'completado', 1, 0, '2026-04-06 13:18:09'),
(4, 77, 5, 'ordenar_mesas', '2026-04-06', 'completado', 1, 0, '2026-04-06 13:18:09'),
(5, 90, 5, 'ordenar_sillas', '2026-04-06', 'completado', 1, 0, '2026-04-06 13:18:10'),
(6, 80, 5, 'vaciar_canecas', '2026-04-06', 'completado', 1, 0, '2026-04-06 13:18:10'),
(7, 91, 5, 'trapear', '2026-04-06', 'completado', 1, 0, '2026-04-06 13:18:10'),
(8, 79, 5, 'trapear', '2026-04-06', 'completado', 1, 0, '2026-04-06 13:18:10'),
(9, 136, 7, 'barrer', '2026-04-06', 'completado', 1, 0, '2026-04-06 18:51:11'),
(10, 133, 7, 'barrer', '2026-04-06', 'pendiente', 1, 0, '2026-04-06 18:51:11'),
(11, 123, 7, 'ordenar_mesas', '2026-04-06', 'completado', 1, 0, '2026-04-06 18:51:11'),
(12, 127, 7, 'ordenar_mesas', '2026-04-06', 'completado', 1, 0, '2026-04-06 18:51:11'),
(13, 126, 7, 'ordenar_sillas', '2026-04-06', 'completado', 1, 0, '2026-04-06 18:51:11'),
(14, 122, 7, 'vaciar_canecas', '2026-04-06', 'completado', 1, 0, '2026-04-06 18:51:11'),
(15, 121, 7, 'trapear', '2026-04-06', 'completado', 1, 0, '2026-04-06 18:51:11'),
(16, 129, 7, 'trapear', '2026-04-06', 'completado', 1, 0, '2026-04-06 18:51:11'),
(17, 96, 6, 'barrer', '2026-04-07', 'pendiente', 1, 0, '2026-04-07 12:35:37'),
(18, 99, 6, 'barrer', '2026-04-07', 'pendiente', 1, 0, '2026-04-07 12:35:37'),
(19, 108, 6, 'ordenar_mesas', '2026-04-07', 'pendiente', 1, 0, '2026-04-07 12:35:37'),
(20, 109, 6, 'ordenar_mesas', '2026-04-07', 'pendiente', 1, 0, '2026-04-07 12:35:37'),
(21, 104, 6, 'ordenar_sillas', '2026-04-07', 'pendiente', 1, 0, '2026-04-07 12:35:37'),
(22, 105, 6, 'vaciar_canecas', '2026-04-07', 'pendiente', 1, 0, '2026-04-07 12:35:37'),
(23, 113, 6, 'trapear', '2026-04-07', 'pendiente', 1, 0, '2026-04-07 12:35:37'),
(24, 102, 6, 'trapear', '2026-04-07', 'pendiente', 1, 0, '2026-04-07 12:35:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `documento` varchar(50) DEFAULT NULL,
  `doc_hash` char(64) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `rol` enum('Admin','Docente','Estudiante') DEFAULT NULL,
  `id_grado` int(11) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `documento`, `doc_hash`, `password`, `rol`, `id_grado`, `activo`, `created_at`) VALUES
(1, 'WILVER ALEXANDER', 'PERALTA CASTILLO', '7784159', 'c9e00e48b4ef490596ed2baa2b0412c8968f7ee60825c657e7614a64ef5c54fe', 'c9e00e48b4ef490596ed2baa2b0412c8968f7ee60825c657e7614a64ef5c54fe', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(2, 'KAREN SOFIA', 'REA DELGADO', '7688541', '1aec327c16aceecee290a623c3613dd4a5800e967a59be5fdeb4abe52082b1ec', '1aec327c16aceecee290a623c3613dd4a5800e967a59be5fdeb4abe52082b1ec', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(3, 'ELIANGELIS VALENTINA', 'GONZALEZ PERALTA', '7091338', '64ee2d198c86914648754efa7994ee97ca4d94ecccfd2d8749e07b3151aa2edf', '64ee2d198c86914648754efa7994ee97ca4d94ecccfd2d8749e07b3151aa2edf', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(4, 'KELLY JOHANA', 'CORREDOR VEGA', '1129764050', 'e47066d41f7f64f0e3f0eda1d16087690fcdfc89f41fcaefabf05663f4ad3b58', 'e47066d41f7f64f0e3f0eda1d16087690fcdfc89f41fcaefabf05663f4ad3b58', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(5, 'ETHAN PHILIPPE', 'ROJAS QUECAN', '1072709492', '435bc8649da9df0377060d12d96df0cc7fcf894b865c45a5f7bd10c344c107e8', '435bc8649da9df0377060d12d96df0cc7fcf894b865c45a5f7bd10c344c107e8', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(6, 'JUAN DAVID', 'ACEVEDO ABELLA', '1072701081', 'e66fb42dd4d19bb43fd508a2336e9f36fa7f29c5bd8516e5b0399a5f4a20dd92', 'e66fb42dd4d19bb43fd508a2336e9f36fa7f29c5bd8516e5b0399a5f4a20dd92', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(7, 'WINDY VIVIANA', 'YEPES LOPEZ', '1069264856', 'e82b391b7bc3aa47d16cfd2d7fdec39ad403d80c5f8963eaa136b60be564112f', 'e82b391b7bc3aa47d16cfd2d7fdec39ad403d80c5f8963eaa136b60be564112f', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(8, 'ANDERSON STIVEN', 'DIAZ BOLIVAR', '1055333192', '82396817e345801adfbc7f93a1c0deb952f1df1f58bab6335d0211ab4cd02915', '82396817e345801adfbc7f93a1c0deb952f1df1f58bab6335d0211ab4cd02915', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(9, 'GABRIELA STEFANI', 'MARTINEZ CANARIA', '1055333155', 'f25bfbafa441d8d961e40bbf4f7199ea38ad37316e4326122eec0097466a11af', 'f25bfbafa441d8d961e40bbf4f7199ea38ad37316e4326122eec0097466a11af', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(10, 'TANIA YISEL', 'MARTINEZ CANARIA', '1055333154', 'a387c0dcc46f1703aa1c35dd8ba00bc5e5622ba5811a51fa6de73cbaf1c11ab7', 'a387c0dcc46f1703aa1c35dd8ba00bc5e5622ba5811a51fa6de73cbaf1c11ab7', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(11, 'JESUS ALEJANDRO', 'VIRACACHA PALACIOS', '1055333110', '81278b8d033e01477505a6992ea05b51cd933f8842f8671bad5423e34d4c1dec', '81278b8d033e01477505a6992ea05b51cd933f8842f8671bad5423e34d4c1dec', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(12, 'ANA VALERIA', 'MANCIPE ALBA', '1055332969', '35b00043339faad752b743c350d9faf30e246d3b8176170142c1592c398657ff', '35b00043339faad752b743c350d9faf30e246d3b8176170142c1592c398657ff', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(13, 'PAULA JULIETH', 'LEON VARGAS', '1054709279', '1c2fbb6341bc68dc496769892bd220988adabc51c871098e03ef4786dae6bf9f', '1c2fbb6341bc68dc496769892bd220988adabc51c871098e03ef4786dae6bf9f', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(14, 'MARIANA', 'CEPEDA CASTAÑO', '1054147145', 'aa4bb26f23634578555a7632f38f2e37e4f2f3a7069e1ef85b9a84b7cfeffaf7', 'aa4bb26f23634578555a7632f38f2e37e4f2f3a7069e1ef85b9a84b7cfeffaf7', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(15, 'DILAN', 'RODRIGUEZ QUINTERO', '1053613383', '85e72e7403d74882271287e071e2d59f9758ee164ae9ba186366c50e0ea98fbf', '85e72e7403d74882271287e071e2d59f9758ee164ae9ba186366c50e0ea98fbf', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(16, 'JENNIFFER SOFIA', 'VILLAMIL CANO', '1053612854', '0e9535cd399657d1cefc2f9a811c95f54b449fef6878d1d7b6cb54af148c532b', '0e9535cd399657d1cefc2f9a811c95f54b449fef6878d1d7b6cb54af148c532b', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(17, 'DANA MARIA', 'FONSECA CANARIA', '1053612416', '53db2d93199f011fd1e7472d98b8bd21366e55a074fd6729ffbbde2504f7f4b1', '53db2d93199f011fd1e7472d98b8bd21366e55a074fd6729ffbbde2504f7f4b1', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(18, 'ELIANA ISABEL', 'BELTRAN HERNANDEZ', '1053586648', 'e8fbc14e26fb56babbb00e1532c025639c3ee176454d06366190961f4bde494e', 'e8fbc14e26fb56babbb00e1532c025639c3ee176454d06366190961f4bde494e', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(19, 'SARAY ALEXANDRA', 'CASALLAS CASALLAS', '1053448803', '92fcf9f9459623b2800507215b238317f77d60cb0b68069a4636b46f4fbc4338', '92fcf9f9459623b2800507215b238317f77d60cb0b68069a4636b46f4fbc4338', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(20, 'MANUEL SANTIAGO', 'MORALES VARGAS', '1053448535', '84d140f7570df8b3773af07caaf6a483ccfdd1f6e09febe1ad179c8d7f935298', '84d140f7570df8b3773af07caaf6a483ccfdd1f6e09febe1ad179c8d7f935298', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(21, 'HELBERTH SANTIAGO', 'BAUTISTA TORRES', '1053447488', '383a7d8e145a8b9b017284946a0ec18ce8704344b321c0e7088ef47513e3a3e3', '383a7d8e145a8b9b017284946a0ec18ce8704344b321c0e7088ef47513e3a3e3', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(22, 'ALISON MARIAN', 'VARGAS HIGUERA', '1050099319', '6703536e0f17abd3069fd9743f54f15ebce00de8d6e80bb63de16224fa446e47', '6703536e0f17abd3069fd9743f54f15ebce00de8d6e80bb63de16224fa446e47', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(23, 'JORGE ANDRES', 'PINEDA LOPEZ', '1050098711', 'b7dd42cc128ef51121f5fd931fe3c9cae33d250e0ecba042c10ae18bafd4750b', 'b7dd42cc128ef51121f5fd931fe3c9cae33d250e0ecba042c10ae18bafd4750b', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(24, 'LIZETH MARIANA', 'LEGUIZAMO BERNAL', '1050098003', '67ff365dfdfcd2ae129a33722557f655316c3a7a6c90447d23a76fe2e35d9ef7', '67ff365dfdfcd2ae129a33722557f655316c3a7a6c90447d23a76fe2e35d9ef7', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(25, 'LISETH DAYANNA', 'MARTINEZ HERNANDEZ', '1049642599', '972bb90f9dbcadc1eb24ffe25ec912fb186be7b08d6eb0fee66eeafcf8681503', '972bb90f9dbcadc1eb24ffe25ec912fb186be7b08d6eb0fee66eeafcf8681503', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(26, 'VALERYN NICOL', 'OJEDA LOPEZ', '1031837393', 'ae439db987e4c094814cda23a51d7adda814c5601f0edebb2d1404f6d92ecf1c', 'ae439db987e4c094814cda23a51d7adda814c5601f0edebb2d1404f6d92ecf1c', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(27, 'CRISTIAN DANIEL', 'RINCON RICO', '1031828281', '3d87c39d66c9d6ebc172d39724b8d7b985fe32112d60f8d1a32e2f0b7221d5ca', '3d87c39d66c9d6ebc172d39724b8d7b985fe32112d60f8d1a32e2f0b7221d5ca', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(28, 'MARIA PAULA', 'MATEUS MILLAN', '1016961848', '9fb83fa17b1ffb06beb6801947763d64dd9cc887b3697254f41f989f4a06ee03', '9fb83fa17b1ffb06beb6801947763d64dd9cc887b3697254f41f989f4a06ee03', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(29, 'KEYRY ALEXANDRA', 'SIERRA GOMEZ', '1013143645', '5c46541e55a32cbf24913d87dfd4bafda299b116f91664074c90b5db8d21a4bc', '5c46541e55a32cbf24913d87dfd4bafda299b116f91664074c90b5db8d21a4bc', 'Estudiante', 2, 1, '2026-02-23 16:26:34'),
(30, 'LUNA KATERINE', 'SANCHEZ AMAYA', '1150437268', 'e3c3268db32bb9f38c94504151046f36a080214904588820d3b55033b8b82c6a', 'e3c3268db32bb9f38c94504151046f36a080214904588820d3b55033b8b82c6a', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(31, 'NICOLAS DAVID', 'PEREZ VILLAMIZAR', '1145326129', 'c022595f4cd2fafb9d9bcfb16d936641873054e674fe265504d65e97e39874f8', 'c022595f4cd2fafb9d9bcfb16d936641873054e674fe265504d65e97e39874f8', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(32, 'JUAN ESTEVAN', 'MUÑOZ SAAVEDRA', '1145325867', 'dc4b751c156ab8055aaa9c0f24029d9c83573a8921cf3e74ed01402d20e7175d', 'dc4b751c156ab8055aaa9c0f24029d9c83573a8921cf3e74ed01402d20e7175d', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(33, 'JEISON ARLEY', 'ARIZA VILLAMIL', '1101757654', '7c5c2869d5bca7d6b288de747f25f7a986f79bd0474f024fbf011b5523ae9bcf', '7c5c2869d5bca7d6b288de747f25f7a986f79bd0474f024fbf011b5523ae9bcf', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(34, 'JOHAN SEBASTIAN', 'AVELLANEDA MAYORGA', '1076742600', '34cb3ebcba1905d3de4636c6a829b967a6a7c98a7d11972a1390d3a442470828', '34cb3ebcba1905d3de4636c6a829b967a6a7c98a7d11972a1390d3a442470828', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(35, 'DANNA VALENTINA', 'MAYORGA LOPEZ', '1056930316', 'dbae236675ecba2bd035aa28cac9b0e43a3348e3ed4773e5250a3df2d3dbf2f2', 'dbae236675ecba2bd035aa28cac9b0e43a3348e3ed4773e5250a3df2d3dbf2f2', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(36, 'SARA VALENTINA', 'CASTRO PINZON', '1056573308', '3c636766e762e78f7537d9c3c95d4e15b5f8dde1b40167375bab8781baad74b7', '3c636766e762e78f7537d9c3c95d4e15b5f8dde1b40167375bab8781baad74b7', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(37, 'MARIANA', 'GONZALEZ ROJAS', '1056573274', 'a0d629ada594749e2c1575baad4785e7127c9b45c1cc5239772e64c68ddb579b', 'a0d629ada594749e2c1575baad4785e7127c9b45c1cc5239772e64c68ddb579b', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(38, 'MATIAS GERONIMO', 'VILLAZON LESMES', '1055333089', 'bd9229e18254f2834813d3861bce4de0bb3a4818fa927cee37a722b8f46d4ac1', 'bd9229e18254f2834813d3861bce4de0bb3a4818fa927cee37a722b8f46d4ac1', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(39, 'DILAN ESTIBEN', 'VILLAMIZAR ACUÑA', '1055332975', '884c5c8fa7d0323ac6307f94676c6dba515fb131816a64fe307e8373959deb31', '884c5c8fa7d0323ac6307f94676c6dba515fb131816a64fe307e8373959deb31', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(40, 'ANDRES GONZALO', 'GUZMAN GONZALEZ', '1055332921', '9c57be01af15da0ddce3d4bf11a2dc8b2e535850aec5d0f485e64f0385072edc', '9c57be01af15da0ddce3d4bf11a2dc8b2e535850aec5d0f485e64f0385072edc', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(41, 'EDWIN YAMID', 'JIMENEZ FERNANDEZ', '1054288616', '457abd9ecf97397224fdb69c60730d0d7fb77bec3f29ce800749abf191967a0d', '457abd9ecf97397224fdb69c60730d0d7fb77bec3f29ce800749abf191967a0d', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(42, 'SAMUEL', 'MONGUI CEPEDA', '1054147047', '902c401cefb6f4889b08bc3ad72d85a6d904f51fc0b495bcdcf12daac802f640', '902c401cefb6f4889b08bc3ad72d85a6d904f51fc0b495bcdcf12daac802f640', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(43, 'JOHAN SANTIAGO', 'GRANADOS OCHOA', '1053445983', '90acc75f683a31b1c47468a61e59ace6a557807fe4280a13140fb802695f0d38', '90acc75f683a31b1c47468a61e59ace6a557807fe4280a13140fb802695f0d38', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(44, 'KEWIN SAMUEL', 'AYALA INFANTE', '1052844177', 'd8f5499f3b97177ffe19b41d7c96a744a0d0857e76c879b3b5c9f956eb437fa9', 'd8f5499f3b97177ffe19b41d7c96a744a0d0857e76c879b3b5c9f956eb437fa9', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(45, 'ANDRES FELIPE', 'MARTINEZ BERNAL', '1052499236', '129f2be34cf8cd55b1137dfc3848ba24e145de5eb4a39b9912df414f7dfb7b57', '129f2be34cf8cd55b1137dfc3848ba24e145de5eb4a39b9912df414f7dfb7b57', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(46, 'STEFANNY ALEJANDRA', 'LEON BARINAS', '1051476291', 'e3d33ed4dd79694fe12bf5383624a1af9c86e0f2e7a1f0cf1059741e22abcebf', 'e3d33ed4dd79694fe12bf5383624a1af9c86e0f2e7a1f0cf1059741e22abcebf', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(47, 'SAMUEL ANDRES', 'ROBLES CHAPARRO', '1050614950', '58ba29adb8251492b0659d767b9b8e56949cce8db374dcc62cca13cb47515815', '58ba29adb8251492b0659d767b9b8e56949cce8db374dcc62cca13cb47515815', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(48, 'VALERIA', 'POSADA CALLEJAS', '1037045154', '6e8acf348709c79d5d7725bb91fb815758caf0543d57b0995bc00d5ca266ce59', '6e8acf348709c79d5d7725bb91fb815758caf0543d57b0995bc00d5ca266ce59', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(49, 'ANA MARIA', 'FONSECA SIERRA', '1031831802', '3085f679760b9327de73b496bd31ef9efe99474be7a1d73ded1dfcd43d2e714a', '3085f679760b9327de73b496bd31ef9efe99474be7a1d73ded1dfcd43d2e714a', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(50, 'SARA VALENTINA', 'SIERRA GOMEZ', '1013647900', 'dc3775e0fb5aa7d9b52bcc3af20071d1d8ce5cd4bfef46d8911a51eb31f496b6', 'dc3775e0fb5aa7d9b52bcc3af20071d1d8ce5cd4bfef46d8911a51eb31f496b6', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(51, 'VALERI MARGARITA', 'GOMEZ GRANADOS', '1013141033', '95efeb095dd9fa19837924c70fef0777a5146fef4ea41a6fec1feb0a118cfac5', '95efeb095dd9fa19837924c70fef0777a5146fef4ea41a6fec1feb0a118cfac5', 'Estudiante', 3, 1, '2026-02-23 16:26:34'),
(52, 'LAURA DANIELA', 'MOLINA CANTOR', '1056709077', 'c1c7bcda52e7df741c003aa3c4dd0844fdb7707a673f623cf9e1b69a6c8d7490', 'c1c7bcda52e7df741c003aa3c4dd0844fdb7707a673f623cf9e1b69a6c8d7490', 'Estudiante', 4, 1, '2026-02-23 16:26:34'),
(53, 'MIGUEL ANGEL', 'SANCHEZ DIAZ', '1055333073', '048abb507a7077f27e9922bf1e56338e375f1958334d805da469354c41d33f0b', '048abb507a7077f27e9922bf1e56338e375f1958334d805da469354c41d33f0b', 'Estudiante', 4, 1, '2026-02-23 16:26:34'),
(54, 'CAROL YULIETH', 'ROSSO GARCIA', '1055333061', 'f9f2af898acc7bd9efc11fc2f09d1ce6b682dc4915f2be57f7e153f547f31de4', 'f9f2af898acc7bd9efc11fc2f09d1ce6b682dc4915f2be57f7e153f547f31de4', 'Estudiante', 4, 1, '2026-02-23 16:26:34'),
(55, 'SARA NICOLL', 'ACUÑA RODRIGUEZ', '1055332947', 'e890d4e5f979475eecf81ab05f0582ed8d14249e66efe97b5763a0d444fac3a6', 'e890d4e5f979475eecf81ab05f0582ed8d14249e66efe97b5763a0d444fac3a6', 'Estudiante', 4, 1, '2026-02-23 16:26:34'),
(56, 'LAURA CATALINA', 'FIGUEREDO FONSECA', '1055332912', 'a97fda061f3df9291e868566e778c5a9b2ad43a335a05a8d6b69f58abdff1641', 'a97fda061f3df9291e868566e778c5a9b2ad43a335a05a8d6b69f58abdff1641', 'Estudiante', 4, 1, '2026-02-23 16:26:34'),
(57, 'DYDIER SAMUEL', 'SANDOVAL PEÑA', '1053611138', 'b1c04b2125679a30e4599a58482044113fa877aca95b49e1dee46106cf5f8f9e', 'b1c04b2125679a30e4599a58482044113fa877aca95b49e1dee46106cf5f8f9e', 'Estudiante', 4, 1, '2026-02-23 16:26:34'),
(58, 'JULIAN DAVID', 'SILVA SILVA', '1053609992', '9df84cab5b512edc89c8d672f4811dc2011ee3d7ae200bef8bbbb65cce7d0c35', '9df84cab5b512edc89c8d672f4811dc2011ee3d7ae200bef8bbbb65cce7d0c35', 'Estudiante', 4, 1, '2026-02-23 16:26:34'),
(59, 'JUAN JOSE', 'RIAÑO SAENZ', '1053448073', '886526465abaa58877edbdcc0373549b3d183a1226aa77c4100465fbff10e9b6', '886526465abaa58877edbdcc0373549b3d183a1226aa77c4100465fbff10e9b6', 'Estudiante', 4, 1, '2026-02-23 16:26:34'),
(60, 'ESTEFANY MARIANA', 'GRANADOS CORREDOR', '1053447157', 'eea4b88917be2b6f63f6cada5ce947dfaf8a75b50576114599d32479797c2a69', 'eea4b88917be2b6f63f6cada5ce947dfaf8a75b50576114599d32479797c2a69', 'Estudiante', 4, 1, '2026-02-23 16:26:34'),
(61, 'ADRIAN STEVEN', 'CETINA IBAÑEZ', '1053446936', '9c38b954b61d89e676121eaa40992ea90d5852e6b169ec6e40cc75318e991def', '9c38b954b61d89e676121eaa40992ea90d5852e6b169ec6e40cc75318e991def', 'Estudiante', 4, 1, '2026-02-23 16:26:34'),
(62, 'MAIKOL ANDREY', 'RODRIGUEZ CIPACON', '1053446241', '72c2550896f07c45bbadeef7e7ff99990a5e8a759ead43c323fab7c9c2b70c5a', '72c2550896f07c45bbadeef7e7ff99990a5e8a759ead43c323fab7c9c2b70c5a', 'Estudiante', 4, 1, '2026-02-23 16:26:34'),
(63, 'LAURA NATHALY', 'PEDRAZA PEREZ', '1052844146', '0e866797000957152412bba159be2f71186fac682815b007ac13b58c3685a4b2', '0e866797000957152412bba159be2f71186fac682815b007ac13b58c3685a4b2', 'Estudiante', 4, 1, '2026-02-23 16:26:34'),
(64, 'NEIDER GEOVANNY', 'MOLANO DIAZ', '1052842776', '48bb91f3b6600a02be482fda27cd87297b6f73c1b7eb974fdd5d22de653a11c1', '48bb91f3b6600a02be482fda27cd87297b6f73c1b7eb974fdd5d22de653a11c1', 'Estudiante', 4, 1, '2026-02-23 16:26:34'),
(65, 'NICOLE ESTEFANY', 'OLAYA SANCHEZ', '1050616499', 'd58658f66084aaf1854006848aca4e61d4179d517fbfbc658f64c547199a0f34', 'd58658f66084aaf1854006848aca4e61d4179d517fbfbc658f64c547199a0f34', 'Estudiante', 4, 1, '2026-02-23 16:26:34'),
(66, 'LAURA DAYANA', 'ACUÑA VARGAS', '1050613452', '99503172fba83e41d7144581a85c337843d37a135ec9552f4c7408f99322e690', '99503172fba83e41d7144581a85c337843d37a135ec9552f4c7408f99322e690', 'Estudiante', 4, 1, '2026-02-23 16:26:34'),
(67, 'SAMUEL ALEJANDRO', 'PAEZ JIMENEZ', '1050096852', '11eda3b330bfbde9a817933f07c0386f1fee4142c061aaba51d51bd3006c85db', '11eda3b330bfbde9a817933f07c0386f1fee4142c061aaba51d51bd3006c85db', 'Estudiante', 4, 1, '2026-02-23 16:26:34'),
(68, 'KEVIN DANIEL', 'MACANA RIVERA', '1050096781', 'de6a962e344d7463ff4258ce70492b9ba6c58568fb7f4da1d9cada436ae0bb1b', 'de6a962e344d7463ff4258ce70492b9ba6c58568fb7f4da1d9cada436ae0bb1b', 'Estudiante', 4, 1, '2026-02-23 16:26:34'),
(69, 'GERMAN', 'ESPINEL FONSECA', '1050095135', 'f3190c66fd915d758ae70b667b8a22dba0c63b7c00f4973194b8caf536329270', 'f3190c66fd915d758ae70b667b8a22dba0c63b7c00f4973194b8caf536329270', 'Estudiante', 4, 1, '2026-02-23 16:26:34'),
(70, 'ANDREA', 'AVILA LEON', '1049638362', '822f8ed7fa7efedf76a389b857de6ca322555a83714b2456a933deb35e6eea86', '822f8ed7fa7efedf76a389b857de6ca322555a83714b2456a933deb35e6eea86', 'Estudiante', 4, 1, '2026-02-23 16:26:34'),
(71, 'HEIDY VALENTINA', 'GARCIA CUBILLOS', '1033105805', 'df73e0576b060b5764d9ef9c166e575cb4dc50b6407fcbcce81ec7c5d29c1a41', 'df73e0576b060b5764d9ef9c166e575cb4dc50b6407fcbcce81ec7c5d29c1a41', 'Estudiante', 4, 1, '2026-02-23 16:26:34'),
(72, 'WILMARI ALEXANDRA', 'MARCANO PERALTA', '41334677266', '3fc095eacb4596166eeb78c3591704414e8104f188305ffaef956d96a6d7ab7f', '3fc095eacb4596166eeb78c3591704414e8104f188305ffaef956d96a6d7ab7f', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(73, 'LINDA CATALINA', 'MAYORGA PEREZ', '1220213067', '44af8f9dbc1fabcde433fce3a3abe1837994ea98460a95042451db66e9dcaffd', '44af8f9dbc1fabcde433fce3a3abe1837994ea98460a95042451db66e9dcaffd', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(74, 'MIGUEL ANGEL', 'RAMIREZ CARDOZO', '1150436432', 'e25b0bd7503a65fb10aa859a902f8367ba3fa2b250ff9cdaf3a778e4a90b35d1', 'e25b0bd7503a65fb10aa859a902f8367ba3fa2b250ff9cdaf3a778e4a90b35d1', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(75, 'ANGIE DANIELA', 'JIMENEZ FERNANDEZ', '1145425013', '2ed3ab06706b7b3c9f801b34f02e74cd0f76b7a71d17fc23c7bbd265d8c6153b', '2ed3ab06706b7b3c9f801b34f02e74cd0f76b7a71d17fc23c7bbd265d8c6153b', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(76, 'LUIS ESTEBAN', 'FONSECA CACERES', '1145324954', '8ca4c94e9d1281c38cc14fc02fbe10d43ca0882b90e56f9817168b759fc55937', '8ca4c94e9d1281c38cc14fc02fbe10d43ca0882b90e56f9817168b759fc55937', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(77, 'ADRIANA LUCIA', 'SOLER PARADA', '1057185558', 'd9fbafb8181605afba4129f9f67e37a4cc2f34a31bbd940d04263aff771565f0', 'd9fbafb8181605afba4129f9f67e37a4cc2f34a31bbd940d04263aff771565f0', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(78, 'CARLOS ALVEIRO', 'AMAYA SANCHEZ', '1056572951', '54f8b6056398e02a59b8f7699fc545794ee5bc4c0932419342dcdb7356c9f748', '54f8b6056398e02a59b8f7699fc545794ee5bc4c0932419342dcdb7356c9f748', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(79, 'MARTIN ESTEBAN', 'GONZALEZ HURTADO', '1055332924', '7dcde0b1ada6d5692cf0b8a130a0335319ec4280bae0fdd0a5ec666c6b7ea957', '7dcde0b1ada6d5692cf0b8a130a0335319ec4280bae0fdd0a5ec666c6b7ea957', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(80, 'DAVID GUILLERMO', 'NIÑO ESCOBAR', '1055332923', '909bedaf1d9966a8f87ba4c30dd8eb06685336ead6751ab418009a2f07aff621', '909bedaf1d9966a8f87ba4c30dd8eb06685336ead6751ab418009a2f07aff621', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(81, 'KAREN LICETH', 'RUEDA VARGAS', '1055128191', 'd413c2f12cd87dc6a22f42138dd9ff9d7d01a01af695b8ba62e002bc36f4023c', 'd413c2f12cd87dc6a22f42138dd9ff9d7d01a01af695b8ba62e002bc36f4023c', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(82, 'GERONIMO', 'CEPEDA CASTAÑO', '1053611367', '1fa0c1b9ab853c3b4470e0db128dc2cc310b0c5acf33f7edc2bd5fa66be251b6', '1fa0c1b9ab853c3b4470e0db128dc2cc310b0c5acf33f7edc2bd5fa66be251b6', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(83, 'NICOL JULIANA', 'CALDERON RIVERA', '1053611205', '3923dcdd538884c5bc21f1f23ea5a7afc2dc7bf9d9bc516e0eaefd7811d3c3bf', '3923dcdd538884c5bc21f1f23ea5a7afc2dc7bf9d9bc516e0eaefd7811d3c3bf', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(84, 'DAMARIS SOFIA', 'NUMPAQUE NUMPAQUE', '1053611098', 'a492a2073d8b161126b8d709cffb482a1f2769a229d8f1bcb5290764511404fe', 'a492a2073d8b161126b8d709cffb482a1f2769a229d8f1bcb5290764511404fe', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(85, 'JOHAN SANTIAGO', 'SANCHEZ RINCON', '1053610898', '9f82add6aef70475e4c2a79da902ad517db9d9f9374142915e4a0af5eb4b86b8', '9f82add6aef70475e4c2a79da902ad517db9d9f9374142915e4a0af5eb4b86b8', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(86, 'HELEN MARIANA', 'RODRIGUEZ CORONADO', '1053610742', 'a8f1af9d61321e5bca239e2d708d7b4a273fea3c8755fb9323289b3f9e0258cb', 'a8f1af9d61321e5bca239e2d708d7b4a273fea3c8755fb9323289b3f9e0258cb', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(87, 'KAREN DAYANA', 'HURTADO QUINTERO', '1053610701', '206178a1e79f3d1a4400833e6c0b304bfc7350ad4f475cae5f80ddf9e50f811a', '206178a1e79f3d1a4400833e6c0b304bfc7350ad4f475cae5f80ddf9e50f811a', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(88, 'YEFFERSON DAVID', 'OJEDA RIVERA', '1053446741', '05e2904c81a3903d891a7c8d560295fb9dc65e3452ff1fc4a184d8570b6d6de8', '05e2904c81a3903d891a7c8d560295fb9dc65e3452ff1fc4a184d8570b6d6de8', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(89, 'SHAILA NICOLL', 'DIAZ BOLIVAR', '1053446461', 'fef2a2419816a6b94c2b872dbeb131fff2d4d8c94e05997d8dc1c0eb006d37f4', 'fef2a2419816a6b94c2b872dbeb131fff2d4d8c94e05997d8dc1c0eb006d37f4', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(90, 'JONATHAN DAVID', 'PALACIO SALAMANCA', '1053446032', '88de94d824924f2f73ce5ae5419d6b161335427bf7e9d1d4295cdfc0419b15c3', '88de94d824924f2f73ce5ae5419d6b161335427bf7e9d1d4295cdfc0419b15c3', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(91, 'ANDRES ESTEBAN', 'AYALA LOPEZ', '1052843698', '96fae090722fe5a36fae3e29d411158028b382540fbf7fd1a313295c918d1284', '96fae090722fe5a36fae3e29d411158028b382540fbf7fd1a313295c918d1284', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(92, 'JOSE LUIS', 'FORERO HIGUERA', '1052842217', '9a956d0d8c8e102bfaf823cb16ca2453f8b4ad6611400847d588476ee4f28a50', '9a956d0d8c8e102bfaf823cb16ca2453f8b4ad6611400847d588476ee4f28a50', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(93, 'ANDRES JULIAN', 'FONSECA SIERRA', '1031827718', '5f66de7a5b85e90c6fdc6a6272190d872ae66a700adf8ae64fcaaba04d2a569b', '5f66de7a5b85e90c6fdc6a6272190d872ae66a700adf8ae64fcaaba04d2a569b', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(94, 'ANGELITH', 'FERNANDEZ ROPERO', '1028889621', 'cbc0074abedff01a99a4697d897bfd84c05a50fb6ef6f3b73b0c44f1a44c3c85', 'cbc0074abedff01a99a4697d897bfd84c05a50fb6ef6f3b73b0c44f1a44c3c85', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(95, 'FREDY ALEJANDRO', 'RODRIGUEZ RAMIREZ', '1025066860', 'c97c98ccba5b56e52f92d45c30c10152c8562ed279e8921c1d18b379c4859deb', 'c97c98ccba5b56e52f92d45c30c10152c8562ed279e8921c1d18b379c4859deb', 'Estudiante', 5, 1, '2026-02-23 16:26:34'),
(96, 'VALERI JULIANA', 'GONZALEZ SAGANOME', '1150436566', '3f2515026c9b2e493a44834ad6adb01950a2876fa399a11aba46685242fd06e5', '3f2515026c9b2e493a44834ad6adb01950a2876fa399a11aba46685242fd06e5', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(97, 'MIGUEL ANGEL', 'GONZALEZ SAGANOME', '1150436565', '5429d5858631cd33347e2a80ffcefce9ddd1862c6d01b850b31a39f0421f786f', '5429d5858631cd33347e2a80ffcefce9ddd1862c6d01b850b31a39f0421f786f', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(98, 'MARIA JOSE', 'RAMIREZ CARDOZO', '1150436433', '8c406dff772da4f4a59221e229058ed28204576258747dec524b4924c976fd31', '8c406dff772da4f4a59221e229058ed28204576258747dec524b4924c976fd31', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(99, 'BRAYAN ANDRES', 'GONZALEZ ALBA', '1091981301', 'e5e22dea63cf79792cb7dd9bdd7417e6c690221fc515683030c8135d3e46a7eb', 'e5e22dea63cf79792cb7dd9bdd7417e6c690221fc515683030c8135d3e46a7eb', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(100, 'SAMUEL', 'PACHECO JIMENEZ', '1073482827', 'fc62d201efbb2609f4b9f56f662d003692a47bf943ffe9e6e30a31dd97c1fc21', 'fc62d201efbb2609f4b9f56f662d003692a47bf943ffe9e6e30a31dd97c1fc21', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(101, 'DANNA SOFIA', 'RODRIGUEZ SOSA', '1070392977', 'd06241abfba6a9f3bdbd20e435ee23dbba08ad37f28d8493cf56e7400e9f013a', 'd06241abfba6a9f3bdbd20e435ee23dbba08ad37f28d8493cf56e7400e9f013a', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(102, 'DAVID SANTIAGO', 'PALACIOS PÁLACIOS', '1057584220', '2ba9ac9459eeb46793aefec5d4ee41ccf3b4c2dccf7e156846a8accb5ca0bcb4', '2ba9ac9459eeb46793aefec5d4ee41ccf3b4c2dccf7e156846a8accb5ca0bcb4', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(103, 'BRAYAN CAMILO', 'QUIROGA FIGUEREDO', '1055962775', '94405698038d4b8c434f9c97a1d2c25f9b8fd384539665795ae9268d2418df28', '94405698038d4b8c434f9c97a1d2c25f9b8fd384539665795ae9268d2418df28', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(104, 'JUAN SEBASTIAN', 'VARGAS DIAZ', '1054146962', '6d40ed11113b3021d4776695e9962c9f2132888cd186ea57a55e14497848b8ec', '6d40ed11113b3021d4776695e9962c9f2132888cd186ea57a55e14497848b8ec', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(105, 'SERGIO ALEJANDRO', 'GARAVITO ROBLES', '1053447978', 'b073971d9884bf780038fc11a6f044b6de58f63782a85a31959e56da121ce5f3', 'b073971d9884bf780038fc11a6f044b6de58f63782a85a31959e56da121ce5f3', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(106, 'LAURA MICHELL', 'SANCHEZ MEDINA', '1053446364', 'af554cec1ce2e3f00b0b34f8f3139de886b8aba75e0e78625685d5568dfdfdbc', 'af554cec1ce2e3f00b0b34f8f3139de886b8aba75e0e78625685d5568dfdfdbc', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(107, 'STEFANNY', 'RODRIGUEZ QUINTERO', '1052842669', 'e3d2fb8ec6545e2f12feb7ed684b9513380ed6e50fe57876c177c4cf7f487564', 'e3d2fb8ec6545e2f12feb7ed684b9513380ed6e50fe57876c177c4cf7f487564', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(108, 'DUVAN FERLEY', 'CORDOBA RUIZ', '1052394822', '9d50288d0efccf36f3b6993f4a2bd34b46be65d3403c369cfb1a335833922dfe', '9d50288d0efccf36f3b6993f4a2bd34b46be65d3403c369cfb1a335833922dfe', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(109, 'LADY YULIANA', 'NUMPAQUE FIGUEREDO', '1052394670', 'c9c46edfad3d47830eb402c6985f62a15e12bcc0f1f8f72a7d6a2631173d424e', 'c9c46edfad3d47830eb402c6985f62a15e12bcc0f1f8f72a7d6a2631173d424e', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(110, 'MARIA JOSE', 'MUÑOZ PACHECO', '1052394482', '6f9e26e35fdf8c24e99077da2a854873ed44985a9ff91a9670d4a396920e4284', '6f9e26e35fdf8c24e99077da2a854873ed44985a9ff91a9670d4a396920e4284', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(111, 'LAURA CATALINA', 'RODRIGUEZ BARINAS', '1051475616', 'd9863ac2c84135a52b2cfa7403f3f022a7dfc6ca703a1b06e8b26d1613021996', 'd9863ac2c84135a52b2cfa7403f3f022a7dfc6ca703a1b06e8b26d1613021996', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(112, 'ANDRES JULIAN', 'MOZO TORRES', '1051068186', 'b81fe6771b7b21f30ea56bfaadce9cb28f14f2986f5cd626ab656ccdba4a21ba', 'b81fe6771b7b21f30ea56bfaadce9cb28f14f2986f5cd626ab656ccdba4a21ba', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(113, 'MARICEL YULEIDY', 'GARAVITO SUAREZ', '1050096653', '9133e2aebc88850238a55e5e49217e3b548f60d0a3b2c5bdbdf7bdfb1dd4ef31', '9133e2aebc88850238a55e5e49217e3b548f60d0a3b2c5bdbdf7bdfb1dd4ef31', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(114, 'JAHIR ESTEBAN', 'GONZALEZ HERNANDEZ', '1029521993', '19ae1e8e398bf8d48e0f0835ee0a11e47779f63ec40fe1821d5db485089b8eae', '19ae1e8e398bf8d48e0f0835ee0a11e47779f63ec40fe1821d5db485089b8eae', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(115, 'DEIVID ALEXANDER', 'YAYA LOPEZ', '1029285903', '2b0321501aeb3488334ac67175065b6e52c01809664fed3f01dc2ba4097ef181', '2b0321501aeb3488334ac67175065b6e52c01809664fed3f01dc2ba4097ef181', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(116, 'JUAN CAMILO', 'ESPINDOLA SILVA', '1012389683', 'ca1cf15cd535629cd92beaa74311cd8c9845be5d94c82539f8d77ad3f6a5e197', 'ca1cf15cd535629cd92beaa74311cd8c9845be5d94c82539f8d77ad3f6a5e197', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(117, 'LAURA ISABELA', 'MATEUS MILLAN', '1011208043', 'cd87a5a8762bdcf95171484269f2146775a493596e7e67d766423e5cbed20abc', 'cd87a5a8762bdcf95171484269f2146775a493596e7e67d766423e5cbed20abc', 'Estudiante', 6, 1, '2026-02-23 16:26:34'),
(118, 'CRISTIAN HERNAN', 'NIÑO RIVERA', '1150435399', 'ed2ccdc2f58f20a429217a7c166155b1eb5dfe7e5d091534acf688be312f18cd', 'ed2ccdc2f58f20a429217a7c166155b1eb5dfe7e5d091534acf688be312f18cd', 'Estudiante', 7, 1, '2026-02-23 16:26:34'),
(119, 'SARA YULIET', 'ROBLEDO MATEUS', '1146884033', 'f2ef33b116229c4e40a8c13db0131dbb74fcb0170bba3b31e29d694b95d466f6', 'f2ef33b116229c4e40a8c13db0131dbb74fcb0170bba3b31e29d694b95d466f6', 'Estudiante', 7, 1, '2026-02-23 16:26:34'),
(120, 'KARLA ALEJANDRA', 'PEREZ OJEDA', '1057982143', 'e34f9e303b59e4aa230b26a625feabf6b1bb80771ba7710400beaea25656c478', 'e34f9e303b59e4aa230b26a625feabf6b1bb80771ba7710400beaea25656c478', 'Estudiante', 7, 1, '2026-02-23 16:26:34'),
(121, 'MIGUEL ANGEL', 'AYURE AGUILAR', '1056573174', '30cebb83b86a3656ed068e1c96b2b76c68d8e33af35566c3b7015f2ef0899a55', '30cebb83b86a3656ed068e1c96b2b76c68d8e33af35566c3b7015f2ef0899a55', 'Estudiante', 7, 1, '2026-02-23 16:26:34'),
(122, 'MARIA FERNANDA', 'PINEDA LOPEZ', '1056572950', 'dafe2ecf0541a0390b259e8d0590ceafa5bf54a808055f556d9d26f717657794', 'dafe2ecf0541a0390b259e8d0590ceafa5bf54a808055f556d9d26f717657794', 'Estudiante', 7, 1, '2026-02-23 16:26:34'),
(123, 'DIANA VALENTINA', 'FONSECA ROSSO', '1054146871', '506b9f3b9c506dd1fbc38801fd88e1a521dbfb6b146afa01a7a52f3247ad4f73', '506b9f3b9c506dd1fbc38801fd88e1a521dbfb6b146afa01a7a52f3247ad4f73', 'Estudiante', 7, 1, '2026-02-23 16:26:34'),
(124, 'SOLANYELY ALEXANDRA', 'CARDENAS ARGUELLO', '1053615022', 'a151f449635aacd18d0014d70447ad5a492e166ea6430d5a528c84df50df938a', 'a151f449635aacd18d0014d70447ad5a492e166ea6430d5a528c84df50df938a', 'Estudiante', 7, 1, '2026-02-23 16:26:34'),
(125, 'VALERI VALENTINA', 'AGUILAR GALINDO', '1053610197', '07516f36be7b2e9a129d7d4ec28783c9b269b5ef6dba6f8f7a4d0bd080adeacd', '07516f36be7b2e9a129d7d4ec28783c9b269b5ef6dba6f8f7a4d0bd080adeacd', 'Estudiante', 7, 1, '2026-02-23 16:26:34'),
(126, 'BRAYAN FELIPE', 'SANCHEZ DIAZ', '1053446407', '8702052850f0e160a0e9aaa27d551b10bc9f34fae7b89d68ca1e09ac417ca544', '8702052850f0e160a0e9aaa27d551b10bc9f34fae7b89d68ca1e09ac417ca544', 'Estudiante', 7, 1, '2026-02-23 16:26:34'),
(127, 'LUIS MIGUEL', 'FIGUEREDO FONSECA', '1053446228', '999b282ed2c184a6038aa3f4b90b8d1be5931831b0d1844d2471603ccf158eab', '999b282ed2c184a6038aa3f4b90b8d1be5931831b0d1844d2471603ccf158eab', 'Estudiante', 7, 1, '2026-02-23 16:26:34'),
(128, 'JEIDY JULIANA', 'GARCIA CANO', '1053445465', '234a9f118baf627ac5891938118d0faf2ed6b2a35382105d3068de52d6cc261a', '234a9f118baf627ac5891938118d0faf2ed6b2a35382105d3068de52d6cc261a', 'Estudiante', 7, 1, '2026-02-23 16:26:34'),
(129, 'JHOSTIN ANDREY', 'LOPEZ NIÑO', '1052843010', 'bf9f667d9e84cb0a9d0249edb457a9a6580daff1cbdab1331b53ba4ecfc2461a', 'bf9f667d9e84cb0a9d0249edb457a9a6580daff1cbdab1331b53ba4ecfc2461a', 'Estudiante', 7, 1, '2026-02-23 16:26:34'),
(130, 'STEVEN FERNANDO', 'JAQUE MENDOZA', '1052842713', '1c9a35b1cdf50333d2e1dd4f1c29d355d30e87a1e206c47ddfca713554fc4761', '1c9a35b1cdf50333d2e1dd4f1c29d355d30e87a1e206c47ddfca713554fc4761', 'Estudiante', 7, 1, '2026-02-23 16:26:34'),
(131, 'PAULA DANIELA', 'JIMENEZ RODRIGUEZ', '1052842146', '0b31231ac268710d538adadec37c2d1242cbeaf6aac87f79ae992513ecd53379', '0b31231ac268710d538adadec37c2d1242cbeaf6aac87f79ae992513ecd53379', 'Estudiante', 7, 1, '2026-02-23 16:26:34'),
(132, 'KEVIN ALEJANDRO', 'SANCHEZ MAYORGA', '1052842062', '04f837109ee1aaa391e5ae3360d294dd7ca27c9efcbbdfcbacd76fe15b5ea053', '04f837109ee1aaa391e5ae3360d294dd7ca27c9efcbbdfcbacd76fe15b5ea053', 'Estudiante', 7, 1, '2026-02-23 16:26:34'),
(133, 'EDUAR STEBAN', 'RODRIGUEZ DUARTE', '1052395661', 'd65c978e1f1258c87da458bce92e0c577b389a458adbd82371114df8e8e7d9a9', 'd65c978e1f1258c87da458bce92e0c577b389a458adbd82371114df8e8e7d9a9', 'Estudiante', 7, 1, '2026-02-23 16:26:34'),
(134, 'KAREN NATALIA', 'MATTA GUTIERREZ', '1050092956', 'd3a8927ccb0607630ebae3b1b85c70db467b61e7bcbce35e969fc445a92f55c4', 'd3a8927ccb0607630ebae3b1b85c70db467b61e7bcbce35e969fc445a92f55c4', 'Estudiante', 7, 1, '2026-02-23 16:26:34'),
(135, 'ANDERSON ANDAIR', 'NEIZA NEIZA', '1032943111', '6f61a10e98552b83dc234b78af7ae3f2576568035be37b61adcf3cd47f7d86c5', '6f61a10e98552b83dc234b78af7ae3f2576568035be37b61adcf3cd47f7d86c5', 'Estudiante', 7, 1, '2026-02-23 16:26:34'),
(136, 'JUAN ESTEBAN', 'JIMENEZ SAZA', '1029663728', 'b4771d9a1a822c8fae5bd207362430983e85bfa87092319c8d4b6fcbc2f62ada', 'b4771d9a1a822c8fae5bd207362430983e85bfa87092319c8d4b6fcbc2f62ada', 'Estudiante', 7, 1, '2026-02-23 16:26:34'),
(137, 'SEBASTIAN FELIPE', 'NAVARRETE BAUTISTA', '1150435927', 'ce421a2d0090020133578bc07a530b3c09e355bfa95d6601d641ddc11a554852', 'ce421a2d0090020133578bc07a530b3c09e355bfa95d6601d641ddc11a554852', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(138, 'MAIKOL SEBASTIAN', 'FIGUEREDO GARAVITO', '1150435867', '46f6ebb47ec3183a3cf1468273e0c976ea354857d3897855ecc6504b1575fb22', '46f6ebb47ec3183a3cf1468273e0c976ea354857d3897855ecc6504b1575fb22', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(139, 'SHARON SOFIA', 'PEREZ VILLAMIZAR', '1145324562', '71b07c1066e7606f258225f4ea123cb750b723637df8c45b8fdc6831791dbad8', '71b07c1066e7606f258225f4ea123cb750b723637df8c45b8fdc6831791dbad8', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(140, 'KEVIN ANDREY', 'SUAREZ CELY', '1145324334', 'db17f9b124a16ce1e4a6e66b4390e7ffaed41eead3f5bdfb278a4189cd671c9a', 'db17f9b124a16ce1e4a6e66b4390e7ffaed41eead3f5bdfb278a4189cd671c9a', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(141, 'JULIAN DAVID', 'AYALA NAVARRETE', '1145324292', '14cf23e9fbda33a1353f518afa98e05b9ea0680de2dafe51a660203c6360f4a5', '14cf23e9fbda33a1353f518afa98e05b9ea0680de2dafe51a660203c6360f4a5', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(142, 'JUAN STEVEN', 'SOTO RODRIGUEZ', '1145324205', '29c88e414a9de607a591e29a1f3f188018289a151d313b751ba2998e493685e2', '29c88e414a9de607a591e29a1f3f188018289a151d313b751ba2998e493685e2', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(143, 'ADRIAN', 'PACHECO JIMENEZ', '1073482826', '0ee748932e71ef17d51edf7a3e7848a79298917993c7e0ad7afc2fb837bbe392', '0ee748932e71ef17d51edf7a3e7848a79298917993c7e0ad7afc2fb837bbe392', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(144, 'EILEEN SOFIA', 'DURAN LOPEZ', '1056572957', '69e63eaa3e94738252ac63422b3947c1ac1fb21aad90957a587d4b9ed4246e1d', '69e63eaa3e94738252ac63422b3947c1ac1fb21aad90957a587d4b9ed4246e1d', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(145, 'YEIMY GERALDINE', 'AYALA RIVERA', '1055332715', 'f047fd01adb27a45b9b2c692d1775b4b676e7ef8913a96504f1f5efc3bfe19cd', 'f047fd01adb27a45b9b2c692d1775b4b676e7ef8913a96504f1f5efc3bfe19cd', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(146, 'JAVIER STIVEN', 'SANCHEZ DIAZ', '1055332621', '06d05a50f72ff797cbede8efeee6bab2a7561267b33e810e7e8adc41eec5b1ee', '06d05a50f72ff797cbede8efeee6bab2a7561267b33e810e7e8adc41eec5b1ee', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(147, 'LAURA SOFIA', 'SOSA AVILA', '1054373164', '4a09e2ae527a19ee5738f49ea7559e93e8ca51eca6018d58f95dc37347f73836', '4a09e2ae527a19ee5738f49ea7559e93e8ca51eca6018d58f95dc37347f73836', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(148, 'MARIA DEL TRANSITO', 'PARADA SANTAFE', '1054146375', '729f89e534ff41dc33eb4af13ae5325205f3a7423eddcab401e48857ab80f7bd', '729f89e534ff41dc33eb4af13ae5325205f3a7423eddcab401e48857ab80f7bd', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(149, 'JHULIANA VALENTINA', 'PEÑA GALLO', '1053446384', '7e52d482fde2895ad882dd9f41e96dd7d008bdc1a318e252d64d0d8b57a22b7f', '7e52d482fde2895ad882dd9f41e96dd7d008bdc1a318e252d64d0d8b57a22b7f', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(150, 'ANDRES SANTIAGO', 'NOVAL VELA', '1053446325', '97c34a06a6f2f24fc41ba285050fbae1caaf219d2611693d3b12bf2cf67c7ff2', '97c34a06a6f2f24fc41ba285050fbae1caaf219d2611693d3b12bf2cf67c7ff2', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(151, 'FERNEY STICK', 'PAMPLONA AYALA', '1053446248', '6e8cd39ae55cacd7861331cf60504a89319caab54ef335a87b92d681fef224ca', '6e8cd39ae55cacd7861331cf60504a89319caab54ef335a87b92d681fef224ca', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(152, 'EILY YULIANA', 'PULIDO PACHECO', '1053446132', 'badb0cec1cc0b44f63dcabbdfd1b3f9466f4e4d9e9925e5d10a09165566adcd3', 'badb0cec1cc0b44f63dcabbdfd1b3f9466f4e4d9e9925e5d10a09165566adcd3', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(153, 'MANUEL ARLEY', 'PANQUEVA ROMERO', '1052841664', '425a91a418992b0a7c03fdf331a97a536c959e71ac52fddb91633b87f24b4312', '425a91a418992b0a7c03fdf331a97a536c959e71ac52fddb91633b87f24b4312', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(154, 'JULIAN FELIPE', 'CEPEDA CEPEDA', '1052841572', '3e6f4b4a58d89686c1fd189b6f89403783b4c77d72faa5ce59880d8a50ad806e', '3e6f4b4a58d89686c1fd189b6f89403783b4c77d72faa5ce59880d8a50ad806e', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(155, 'CRISTIAN RICARDO', 'ROBLES GARCIA', '1052840639', '4fc3d2e5f1412e919490191449e7958ca10e1ad66ede7991d68d48d46d30461d', '4fc3d2e5f1412e919490191449e7958ca10e1ad66ede7991d68d48d46d30461d', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(156, 'YEIMI CATALINA', 'BAUTISTA BENAVIDES', '1052388251', 'bc2f613a954fd192024cb9e8db332ba3e75c49e5f19524ec1a4a1218ede7cf3a', 'bc2f613a954fd192024cb9e8db332ba3e75c49e5f19524ec1a4a1218ede7cf3a', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(157, 'WENDY YURANI', 'NUMPAQUE NUMPAQUE', '1050612800', 'b88752fb13093f498b3a56ed33d74cfcd0326ee8980acaba2ec4de2483cea743', 'b88752fb13093f498b3a56ed33d74cfcd0326ee8980acaba2ec4de2483cea743', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(158, 'CAROL DANIELA', 'CORREDOR MARTINEZ', '1050612283', 'a0dc87eafd0bb0e40969d76d1b63b9208e9651791a92862914b95df07ba873e5', 'a0dc87eafd0bb0e40969d76d1b63b9208e9651791a92862914b95df07ba873e5', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(159, 'MONICA PATRICIA', 'CIFUENTES VARGAS', '1050093271', '9a7a5225fb8973b57929a7d1d9d2ef86a4c0058f3f67bd2069bd8d4cc0a2dfb6', '9a7a5225fb8973b57929a7d1d9d2ef86a4c0058f3f67bd2069bd8d4cc0a2dfb6', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(160, 'ERIK SANTIAGO', 'LOPEZ VEGA', '1049624815', '1bf85fadf33aab99a5cb3f5cf9d7c67b317187bcddb1850d907ee398a096bff9', '1bf85fadf33aab99a5cb3f5cf9d7c67b317187bcddb1850d907ee398a096bff9', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(161, 'THOMAS', 'BAUTISTA CEPEDA', '1034663940', '017ef44b56926551680cda1ad4a461df436878011e62ddaa73dc5fbf48e4a16e', '017ef44b56926551680cda1ad4a461df436878011e62ddaa73dc5fbf48e4a16e', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(162, 'GABRIELA', 'ROMERO MUÑOZ', '1029284339', 'f204f2f718d338d96e7c9c01aa6aa3722a0d0dca9ac860b6279a5f9b8d859ad0', 'f204f2f718d338d96e7c9c01aa6aa3722a0d0dca9ac860b6279a5f9b8d859ad0', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(163, 'LAURA XIMENA', 'CASALLAS CASALLAS', '1028944779', 'abc8457259d6abc3eecc56a2529c634ca43adecc2f63cc85f47cea58232c48f0', 'abc8457259d6abc3eecc56a2529c634ca43adecc2f63cc85f47cea58232c48f0', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(164, 'CAROL STEFANNY', 'FONSECA MARTIN', '1023386705', 'a1e8de94e0c50fd38970155a08141a4d8d2233ea07e9404c8f9f6a46f2a982b0', 'a1e8de94e0c50fd38970155a08141a4d8d2233ea07e9404c8f9f6a46f2a982b0', 'Estudiante', 8, 1, '2026-02-23 16:26:34'),
(165, 'Ing. Jeferson', 'Fonseca Soto', '1049', '0c62cc42d6479a691f03083654ab6a7a84229ab156c948ba8d3b6c79ddd95536', '0c62cc42d6479a691f03083654ab6a7a84229ab156c948ba8d3b6c79ddd95536', 'Admin', NULL, 1, '2026-02-24 16:46:12');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_asistencia` (`id_usuario`,`fecha`,`id_grado`),
  ADD KEY `fk_asistencia_grado` (`id_grado`);

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bitacora_elemento` (`id_elemento`),
  ADD KEY `fk_bitacora_usuario` (`id_usuario`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `elementos`
--
ALTER TABLE `elementos`
  ADD PRIMARY KEY (`id_elemento`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD UNIQUE KEY `qr_token` (`qr_token`),
  ADD KEY `fk_elementos_categoria` (`id_categoria`);

--
-- Indices de la tabla `grados`
--
ALTER TABLE `grados`
  ADD PRIMARY KEY (`id_grado`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_prestamo_tomador` (`id_tomador`),
  ADD KEY `fk_prestamo_elemento` (`id_elemento`),
  ADD KEY `fk_prestamo_operador` (`id_operador`);

--
-- Indices de la tabla `tareas_aseo`
--
ALTER TABLE `tareas_aseo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`id_usuario`),
  ADD KEY `idx_grado` (`id_grado`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_ciclo` (`ciclo`),
  ADD KEY `idx_orden` (`orden`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `documento` (`documento`),
  ADD KEY `fk_usuario_grado` (`id_grado`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `elementos`
--
ALTER TABLE `elementos`
  MODIFY `id_elemento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT de la tabla `grados`
--
ALTER TABLE `grados`
  MODIFY `id_grado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tareas_aseo`
--
ALTER TABLE `tareas_aseo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD CONSTRAINT `asistencia_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_asistencia_grado` FOREIGN KEY (`id_grado`) REFERENCES `grados` (`id_grado`);

--
-- Filtros para la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD CONSTRAINT `fk_bitacora_elemento` FOREIGN KEY (`id_elemento`) REFERENCES `elementos` (`id_elemento`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bitacora_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `elementos`
--
ALTER TABLE `elementos`
  ADD CONSTRAINT `fk_elementos_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`);

--
-- Filtros para la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD CONSTRAINT `fk_prestamo_elemento` FOREIGN KEY (`id_elemento`) REFERENCES `elementos` (`id_elemento`),
  ADD CONSTRAINT `fk_prestamo_operador` FOREIGN KEY (`id_operador`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_prestamo_tomador` FOREIGN KEY (`id_tomador`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `tareas_aseo`
--
ALTER TABLE `tareas_aseo`
  ADD CONSTRAINT `fk_tareas_grado` FOREIGN KEY (`id_grado`) REFERENCES `grados` (`id_grado`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tareas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_grado` FOREIGN KEY (`id_grado`) REFERENCES `grados` (`id_grado`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
