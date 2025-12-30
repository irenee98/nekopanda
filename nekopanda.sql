-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 02, 2025 at 07:47 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nekopanda`
--

-- --------------------------------------------------------

--
-- Table structure for table `citas`
--

CREATE TABLE `citas` (
  `idCita` int AUTO_INCREMENT PRIMARY KEY,
  `idUser` int NOT NULL,
  `fecha_cita` date NOT NULL,
  `motivo` text,
  FOREIGN KEY (`idUser`) REFERENCES users_data(`idUser`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `citas`
--

INSERT INTO `citas` (`idCita`, `idUser`, `fecha_cita`, `motivo`) VALUES
(1, 2, '2025-10-12', 'Recoger funko pop'),
(2, 2, '2025-10-16', 'Preguntar por promoción'),
(3, 3, '2026-06-27', 'Regalo cumpleaños');

-- --------------------------------------------------------

--
-- Table structure for table `noticias`
--

CREATE TABLE `noticias` (
  `idNoticia` int AUTO_INCREMENT PRIMARY KEY,
  `titulo` varchar(100) NOT NULL UNIQUE,
  `imagen` mediumblob NOT NULL,
  `texto` text NOT NULL,
  `fecha` date NOT NULL,
  `idUser` int NOT NULL,
  FOREIGN KEY (`idUser`) REFERENCES users_data(`idUser`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

--
-- Dumping data for table `noticias`
--

INSERT INTO `noticias` (`idNoticia`, `titulo`, `imagen`, `texto`, `fecha`, `idUser`) VALUES
(1, 'Nuevos zeronis', 0x696d675f363864356431366130646163342e6a7067, 'La nueva colección de zeronis versión ángeles ya llegó a nosotros. ¡Ven antes de que se acabe!', '2025-09-26', 1),
(2, 'Descuento en Funkopop', 0x696d675f363864396430306330306562662e6a7067, 'Durante la última semana de octubre debido a que se acerca Halloween, tendremos un descuento especial en todos los funko pops que hay de Monster High.', '2025-10-25', 1),
(3, 'Evento espacial de Sanrio', 0x696d675f363864656261316137646334352e6a7067, 'Durante el evento del viernes que viene, colaboraremos con Sanrio para traeros algunos productos y merch de la línea como camisetas, funko pops, stickers, fundas de móviles... Si eres fan no te pierdes este evento y si traes a un amigo habrá un regalito espacial gratis.', '2025-10-02', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users_data`
--

CREATE TABLE `users_data` (
  `idUser` int AUTO_INCREMENT PRIMARY KEY,
  `nombre` varchar(30) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL UNIQUE,
  `telefono` varchar(9) NOT NULL,
  `fecha_nac` date NOT NULL,
  `direccion` text,
  `sexo` enum('Hombre','Mujer','Sin especificar') DEFAULT 'Sin especificar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_data`
--

INSERT INTO `users_data` (`idUser`, `nombre`, `apellidos`, `email`, `telefono`, `fecha_nac`, `direccion`, `sexo`) VALUES
(1, 'Irene', 'Rojas', 'irenerogas@gmail.com', '647925745', '1999-06-22', 'C/ Antonio Machado, 5', ''),
(2, 'Matthew', 'Seok', 'matthew02@gmail.com', '634503684', '2002-05-28', 'C/ Santa Catalina, 9', 'Hombre'),
(3, 'Ricky', 'Shen', 'rickyboss@gmail.com', '637591422', '2004-06-28', 'C/ Santa Monica, 6', 'Hombre');

-- --------------------------------------------------------

--
-- Table structure for table `users_login`
--

CREATE TABLE `users_login` (
  `idLogin` int AUTO_INCREMENT PRIMARY KEY,
  `idUser` int NOT NULL UNIQUE,
  `usuario` varchar(30) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','user') NOT NULL DEFAULT 'user',
  FOREIGN KEY (`idUser`) REFERENCES users_data(`idUser`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_login`
--

INSERT INTO `users_login` (`idLogin`, `idUser`, `usuario`, `password`, `rol`) VALUES
(1, 1, 'admin', '$2y$10$diosxf/ygInjJL0aRUd4iOVLZKsE3.YEShAEPW7emCs/IepFKHPkK', 'admin'),
(2, 2, 'matthew02', '$2y$10$iH9tdx7KQHYMC7oCIuFOdOtXUd28tE22xpzkLc3xGJ8l.f2dNxrLS', 'user'),
(3, 3, 'ricky04', '$2y$10$ASA/b/aJ4aNLA0swVIKN4uP9agWHzJbuB1euRtjizK9jaakcTke8i', 'user');

--
-- Indexes for dumped tables
--
--
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
