-- MySQL dump 10.13  Distrib 8.0.44, for Linux (x86_64)
--
-- Host: 172.18.0.101    Database: molletexpress
-- ------------------------------------------------------
-- Server version	11.7.2-MariaDB-ubu2404-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `albaran`
--

DROP TABLE IF EXISTS `albaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `albaran` (
                           `id` int(10) NOT NULL AUTO_INCREMENT,
                           `client` int(10) DEFAULT NULL,
                           `presupuesto` int(10) DEFAULT NULL,
                           `pedido` int(10) DEFAULT NULL,
                           `albaran` int(10) DEFAULT NULL,
                           `factura` int(10) DEFAULT NULL,
                           `serie` varchar(255) NOT NULL DEFAULT 'P',
                           `reference` int(10) NOT NULL DEFAULT 0,
                           `number` varchar(255) NOT NULL DEFAULT '',
                           `date` date NOT NULL DEFAULT curdate(),
                           `observaciones` longtext DEFAULT NULL,
                           `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
                           `tax` varchar(255) NOT NULL DEFAULT '21',
                           `total` decimal(12,2) NOT NULL DEFAULT 0.00,
                           `pagado` decimal(12,2) NOT NULL DEFAULT 0.00,
                           `balance` decimal(12,2) NOT NULL DEFAULT 0.00,
                           `comments` longtext DEFAULT NULL,
                           `date_created` datetime NOT NULL DEFAULT current_timestamp(),
                           `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
                           `orden` int(10) DEFAULT NULL,
                           `pagada` tinyint(1) NOT NULL DEFAULT 0,
                           `num_pedido` int(10) DEFAULT 0,
                           `bloqueada` tinyint(1) NOT NULL DEFAULT 1,
                           `importe_bruto` decimal(12,2) NOT NULL DEFAULT 0.00,
                           `discount` decimal(5,2) NOT NULL DEFAULT 0.00,
                           `discount_pp` decimal(5,2) NOT NULL DEFAULT 0.00,
                           `imp_discount` decimal(5,2) NOT NULL DEFAULT 0.00,
                           `imp_discount_pp` decimal(5,2) NOT NULL DEFAULT 0.00,
                           PRIMARY KEY (`id`),
                           KEY `IDX_2E6A49C2C7440455` (`client`),
                           KEY `IDX_2E6A49C21B6368D3` (`presupuesto`),
                           KEY `IDX_2E6A49C2C4EC16CE` (`pedido`),
                           KEY `IDX_2E6A49C22E6A49C2` (`albaran`),
                           KEY `IDX_2E6A49C2F9EBA009` (`factura`),
                           KEY `IDX_2E6A49C2E128CFD7` (`orden`),
                           CONSTRAINT `FK_2E6A49C21B6368D3` FOREIGN KEY (`presupuesto`) REFERENCES `presupuesto` (`id`),
                           CONSTRAINT `FK_2E6A49C22E6A49C2` FOREIGN KEY (`albaran`) REFERENCES `albaran` (`id`),
                           CONSTRAINT `FK_2E6A49C2C4EC16CE` FOREIGN KEY (`pedido`) REFERENCES `pedido` (`id`),
                           CONSTRAINT `FK_2E6A49C2C7440455` FOREIGN KEY (`client`) REFERENCES `client` (`id`),
                           CONSTRAINT `FK_2E6A49C2E128CFD7` FOREIGN KEY (`orden`) REFERENCES `orden_cobro` (`id`) ON DELETE SET NULL,
                           CONSTRAINT `FK_2E6A49C2F9EBA009` FOREIGN KEY (`factura`) REFERENCES `factura` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29246 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `albaran_linea`
--

DROP TABLE IF EXISTS `albaran_linea`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `albaran_linea` (
                                 `id` int(10) NOT NULL AUTO_INCREMENT,
                                 `presupuesto` int(10) DEFAULT NULL,
                                 `pedido` int(10) DEFAULT NULL,
                                 `albaran` int(10) DEFAULT NULL,
                                 `factura` int(10) DEFAULT NULL,
                                 `reference` varchar(255) NOT NULL DEFAULT '',
                                 `unidad_medida` varchar(255) NOT NULL DEFAULT '',
                                 `description` longtext NOT NULL,
                                 `quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
                                 `quantity_certificada` decimal(10,0) NOT NULL DEFAULT 0,
                                 `price` decimal(12,2) NOT NULL DEFAULT 0.00,
                                 `tax` decimal(10,0) NOT NULL DEFAULT 0,
                                 `total` decimal(12,2) NOT NULL DEFAULT 0.00,
                                 `date_created` datetime NOT NULL DEFAULT current_timestamp(),
                                 `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
                                 `discount` decimal(10,0) NOT NULL DEFAULT 0,
                                 `albaran_linea` int(10) DEFAULT NULL,
                                 `factura_linea` int(10) DEFAULT NULL,
                                 PRIMARY KEY (`id`),
                                 KEY `IDX_590BE8481B6368D3` (`presupuesto`),
                                 KEY `IDX_590BE848C4EC16CE` (`pedido`),
                                 KEY `IDX_590BE8482E6A49C2` (`albaran`),
                                 KEY `IDX_590BE848F9EBA009` (`factura`),
                                 KEY `IDX_590BE848590BE848` (`albaran_linea`),
                                 KEY `IDX_590BE848DAC3517A` (`factura_linea`),
                                 CONSTRAINT `FK_590BE8481B6368D3` FOREIGN KEY (`presupuesto`) REFERENCES `presupuesto` (`id`),
                                 CONSTRAINT `FK_590BE8482E6A49C2` FOREIGN KEY (`albaran`) REFERENCES `albaran` (`id`),
                                 CONSTRAINT `FK_590BE848590BE848` FOREIGN KEY (`albaran_linea`) REFERENCES `albaran_linea` (`id`),
                                 CONSTRAINT `FK_590BE848C4EC16CE` FOREIGN KEY (`pedido`) REFERENCES `pedido` (`id`),
                                 CONSTRAINT `FK_590BE848DAC3517A` FOREIGN KEY (`factura_linea`) REFERENCES `factura_linea` (`id`),
                                 CONSTRAINT `FK_590BE848F9EBA009` FOREIGN KEY (`factura`) REFERENCES `factura` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28026 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `albaran_linea_certificada`
--

DROP TABLE IF EXISTS `albaran_linea_certificada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `albaran_linea_certificada` (
                                             `id` int(10) NOT NULL AUTO_INCREMENT,
                                             `linea` int(10) DEFAULT NULL,
                                             `presupuesto` int(10) DEFAULT NULL,
                                             `pedido` int(10) DEFAULT NULL,
                                             `albaran` int(10) DEFAULT NULL,
                                             `factura` int(10) DEFAULT NULL,
                                             `quantity` decimal(10,0) NOT NULL DEFAULT 0,
                                             `date_created` datetime NOT NULL DEFAULT current_timestamp(),
                                             `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
                                             PRIMARY KEY (`id`),
                                             KEY `IDX_BF0631CABCB8FDDE` (`linea`),
                                             KEY `IDX_BF0631CA1B6368D3` (`presupuesto`),
                                             KEY `IDX_BF0631CAC4EC16CE` (`pedido`),
                                             KEY `IDX_BF0631CA2E6A49C2` (`albaran`),
                                             KEY `IDX_BF0631CAF9EBA009` (`factura`),
                                             CONSTRAINT `FK_BF0631CA1B6368D3` FOREIGN KEY (`presupuesto`) REFERENCES `presupuesto` (`id`),
                                             CONSTRAINT `FK_BF0631CA2E6A49C2` FOREIGN KEY (`albaran`) REFERENCES `albaran` (`id`),
                                             CONSTRAINT `FK_BF0631CABCB8FDDE` FOREIGN KEY (`linea`) REFERENCES `albaran_linea` (`id`),
                                             CONSTRAINT `FK_BF0631CAC4EC16CE` FOREIGN KEY (`pedido`) REFERENCES `pedido` (`id`),
                                             CONSTRAINT `FK_BF0631CAF9EBA009` FOREIGN KEY (`factura`) REFERENCES `factura` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `alert`
--

DROP TABLE IF EXISTS `alert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alert` (
                         `id` int(10) NOT NULL AUTO_INCREMENT,
                         `user_id` int(10) DEFAULT NULL,
                         `message` varchar(400) DEFAULT NULL,
                         `active` tinyint(1) NOT NULL DEFAULT 1,
                         `date_alert` datetime DEFAULT NULL,
                         `date_created` datetime DEFAULT NULL,
                         `date_modified` datetime DEFAULT NULL,
                         PRIMARY KEY (`id`),
                         KEY `IDX_17FD46C1A76ED395` (`user_id`),
                         CONSTRAINT `FK_17FD46C1A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client`
--

DROP TABLE IF EXISTS `client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client` (
                          `id` int(10) NOT NULL AUTO_INCREMENT,
                          `user_id` int(10) DEFAULT NULL,
                          `name` varchar(255) NOT NULL DEFAULT '',
                          `name_comercial` varchar(255) NOT NULL DEFAULT '',
                          `nif` varchar(255) NOT NULL DEFAULT '',
                          `email` varchar(255) NOT NULL DEFAULT '',
                          `direccion` varchar(255) NOT NULL DEFAULT '',
                          `localidad` varchar(255) NOT NULL DEFAULT '',
                          `postal_code` varchar(255) NOT NULL DEFAULT '',
                          `provincia` varchar(255) NOT NULL DEFAULT '',
                          `active` tinyint(1) NOT NULL DEFAULT 1,
                          `date_created` datetime NOT NULL DEFAULT current_timestamp(),
                          `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
                          `forma_pago` varchar(255) NOT NULL DEFAULT '',
                          `dias_pago` int(10) NOT NULL DEFAULT 0,
                          `banco` varchar(255) NOT NULL DEFAULT '',
                          `iban` varchar(255) NOT NULL DEFAULT '',
                          `telefono` varchar(255) NOT NULL DEFAULT '',
                          `movil` varchar(255) NOT NULL DEFAULT '',
                          `num_abonado` int(10) NOT NULL,
                          `descuento` int(10) DEFAULT NULL,
                          `dia_fijo_pago` int(10) DEFAULT 0,
                          `num_pedido` varchar(255) DEFAULT NULL,
                          PRIMARY KEY (`id`),
                          KEY `IDX_C7440455A76ED395` (`user_id`),
                          CONSTRAINT `FK_C7440455A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `departament`
--

DROP TABLE IF EXISTS `departament`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departament` (
                               `id` int(10) NOT NULL AUTO_INCREMENT,
                               `name` varchar(50) NOT NULL,
                               `active` tinyint(1) NOT NULL DEFAULT 1,
                               `date_created` datetime DEFAULT NULL,
                               `date_modified` datetime DEFAULT NULL,
                               PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employee`
--

DROP TABLE IF EXISTS `employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee` (
                            `id` int(10) NOT NULL AUTO_INCREMENT,
                            `user_id` int(10) DEFAULT NULL,
                            `name` varchar(255) NOT NULL DEFAULT '',
                            `dni` varchar(255) NOT NULL DEFAULT '',
                            `salario_base` decimal(10,2) NOT NULL DEFAULT 0.00,
                            `coste_fijo` decimal(10,2) DEFAULT 0.00,
                            `despido_30_dias` decimal(10,2) NOT NULL DEFAULT 0.00,
                            `salario_mensual_nominal` decimal(10,2) NOT NULL DEFAULT 0.00,
                            `plus_extra` decimal(10,2) NOT NULL DEFAULT 0.00,
                            `precio_hora` decimal(10,2) NOT NULL DEFAULT 0.00,
                            `precio_hora_extra` decimal(10,2) NOT NULL DEFAULT 0.00,
                            `precio_hora_festivo` decimal(10,2) NOT NULL DEFAULT 0.00,
                            `plus_guardias` decimal(10,2) NOT NULL DEFAULT 0.00,
                            `adelantos` decimal(10,2) NOT NULL DEFAULT 0.00,
                            `retenciones` decimal(10,2) NOT NULL DEFAULT 0.00,
                            `active` tinyint(1) NOT NULL DEFAULT 1,
                            `date_created` datetime NOT NULL DEFAULT current_timestamp(),
                            `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
                            PRIMARY KEY (`id`),
                            KEY `IDX_5D9F75A1A76ED395` (`user_id`),
                            CONSTRAINT `FK_5D9F75A1A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `factura`
--

DROP TABLE IF EXISTS `factura`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `factura` (
                           `id` int(10) NOT NULL AUTO_INCREMENT,
                           `client` int(10) DEFAULT NULL,
                           `presupuesto` int(10) DEFAULT NULL,
                           `pedido` int(10) DEFAULT NULL,
                           `albaran` int(10) DEFAULT NULL,
                           `factura` int(10) DEFAULT NULL,
                           `serie` varchar(255) NOT NULL DEFAULT 'P',
                           `reference` int(10) NOT NULL DEFAULT 0,
                           `number` varchar(255) NOT NULL DEFAULT '',
                           `date` date NOT NULL DEFAULT curdate(),
                           `observaciones` longtext DEFAULT NULL,
                           `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
                           `tax` varchar(255) NOT NULL DEFAULT '21',
                           `total` decimal(12,2) NOT NULL DEFAULT 0.00,
                           `pagado` decimal(12,2) NOT NULL DEFAULT 0.00,
                           `balance` decimal(12,2) NOT NULL DEFAULT 0.00,
                           `comments` longtext DEFAULT NULL,
                           `date_created` datetime NOT NULL DEFAULT current_timestamp(),
                           `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
                           `orden` int(10) DEFAULT NULL,
                           `pagada` tinyint(1) NOT NULL DEFAULT 0,
                           `num_pedido` int(10) DEFAULT 0,
                           `bloqueada` tinyint(1) NOT NULL DEFAULT 1,
                           `importe_bruto` decimal(12,2) NOT NULL DEFAULT 0.00,
                           `discount` decimal(5,2) NOT NULL DEFAULT 0.00,
                           `discount_pp` decimal(5,2) NOT NULL DEFAULT 0.00,
                           `imp_discount` decimal(5,2) NOT NULL DEFAULT 0.00,
                           `imp_discount_pp` decimal(5,2) NOT NULL DEFAULT 0.00,
                           PRIMARY KEY (`id`),
                           KEY `IDX_F9EBA009C7440455` (`client`),
                           KEY `IDX_F9EBA0091B6368D3` (`presupuesto`),
                           KEY `IDX_F9EBA009C4EC16CE` (`pedido`),
                           KEY `IDX_F9EBA0092E6A49C2` (`albaran`),
                           KEY `IDX_F9EBA009F9EBA009` (`factura`),
                           KEY `IDX_F9EBA009E128CFD7` (`orden`),
                           CONSTRAINT `FK_F9EBA0091B6368D3` FOREIGN KEY (`presupuesto`) REFERENCES `presupuesto` (`id`),
                           CONSTRAINT `FK_F9EBA0092E6A49C2` FOREIGN KEY (`albaran`) REFERENCES `albaran` (`id`),
                           CONSTRAINT `FK_F9EBA009C4EC16CE` FOREIGN KEY (`pedido`) REFERENCES `pedido` (`id`),
                           CONSTRAINT `FK_F9EBA009C7440455` FOREIGN KEY (`client`) REFERENCES `client` (`id`),
                           CONSTRAINT `FK_F9EBA009E128CFD7` FOREIGN KEY (`orden`) REFERENCES `orden_cobro` (`id`) ON DELETE SET NULL,
                           CONSTRAINT `FK_F9EBA009F9EBA009` FOREIGN KEY (`factura`) REFERENCES `factura` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3452 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `factura_linea`
--

DROP TABLE IF EXISTS `factura_linea`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `factura_linea` (
                                 `id` int(10) NOT NULL AUTO_INCREMENT,
                                 `presupuesto` int(10) DEFAULT NULL,
                                 `pedido` int(10) DEFAULT NULL,
                                 `albaran` int(10) DEFAULT NULL,
                                 `factura` int(10) DEFAULT NULL,
                                 `reference` varchar(255) NOT NULL DEFAULT '',
                                 `unidad_medida` varchar(255) NOT NULL DEFAULT '',
                                 `description` longtext NOT NULL,
                                 `quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
                                 `quantity_certificada` decimal(10,0) NOT NULL DEFAULT 0,
                                 `price` decimal(12,2) NOT NULL DEFAULT 0.00,
                                 `tax` decimal(10,0) NOT NULL DEFAULT 0,
                                 `total` decimal(12,2) NOT NULL DEFAULT 0.00,
                                 `date_created` datetime NOT NULL DEFAULT current_timestamp(),
                                 `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
                                 `discount` decimal(10,0) NOT NULL DEFAULT 0,
                                 `albaran_linea` int(10) DEFAULT NULL,
                                 `factura_linea` int(10) DEFAULT NULL,
                                 PRIMARY KEY (`id`),
                                 KEY `IDX_DAC3517A1B6368D3` (`presupuesto`),
                                 KEY `IDX_DAC3517AC4EC16CE` (`pedido`),
                                 KEY `IDX_DAC3517A2E6A49C2` (`albaran`),
                                 KEY `IDX_DAC3517AF9EBA009` (`factura`),
                                 KEY `IDX_DAC3517A590BE848` (`albaran_linea`),
                                 KEY `IDX_DAC3517ADAC3517A` (`factura_linea`),
                                 CONSTRAINT `FK_DAC3517A1B6368D3` FOREIGN KEY (`presupuesto`) REFERENCES `presupuesto` (`id`),
                                 CONSTRAINT `FK_DAC3517A2E6A49C2` FOREIGN KEY (`albaran`) REFERENCES `albaran` (`id`),
                                 CONSTRAINT `FK_DAC3517A590BE848` FOREIGN KEY (`albaran_linea`) REFERENCES `albaran_linea` (`id`),
                                 CONSTRAINT `FK_DAC3517AC4EC16CE` FOREIGN KEY (`pedido`) REFERENCES `pedido` (`id`),
                                 CONSTRAINT `FK_DAC3517ADAC3517A` FOREIGN KEY (`factura_linea`) REFERENCES `factura_linea` (`id`),
                                 CONSTRAINT `FK_DAC3517AF9EBA009` FOREIGN KEY (`factura`) REFERENCES `factura` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30826 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `factura_linea_certificada`
--

DROP TABLE IF EXISTS `factura_linea_certificada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `factura_linea_certificada` (
                                             `id` int(10) NOT NULL AUTO_INCREMENT,
                                             `linea` int(10) DEFAULT NULL,
                                             `presupuesto` int(10) DEFAULT NULL,
                                             `pedido` int(10) DEFAULT NULL,
                                             `albaran` int(10) DEFAULT NULL,
                                             `factura` int(10) DEFAULT NULL,
                                             `quantity` decimal(10,0) NOT NULL DEFAULT 0,
                                             `date_created` datetime NOT NULL DEFAULT current_timestamp(),
                                             `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
                                             PRIMARY KEY (`id`),
                                             KEY `IDX_5762AB88BCB8FDDE` (`linea`),
                                             KEY `IDX_5762AB881B6368D3` (`presupuesto`),
                                             KEY `IDX_5762AB88C4EC16CE` (`pedido`),
                                             KEY `IDX_5762AB882E6A49C2` (`albaran`),
                                             KEY `IDX_5762AB88F9EBA009` (`factura`),
                                             CONSTRAINT `FK_5762AB881B6368D3` FOREIGN KEY (`presupuesto`) REFERENCES `presupuesto` (`id`),
                                             CONSTRAINT `FK_5762AB882E6A49C2` FOREIGN KEY (`albaran`) REFERENCES `albaran` (`id`),
                                             CONSTRAINT `FK_5762AB88BCB8FDDE` FOREIGN KEY (`linea`) REFERENCES `factura_linea` (`id`),
                                             CONSTRAINT `FK_5762AB88C4EC16CE` FOREIGN KEY (`pedido`) REFERENCES `pedido` (`id`),
                                             CONSTRAINT `FK_5762AB88F9EBA009` FOREIGN KEY (`factura`) REFERENCES `factura` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orden_cobro`
--

DROP TABLE IF EXISTS `orden_cobro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orden_cobro` (
                               `id` int(10) NOT NULL AUTO_INCREMENT,
                               `client` int(10) DEFAULT NULL,
                               `reference` varchar(255) NOT NULL DEFAULT '',
                               `date` date DEFAULT NULL,
                               `active` tinyint(1) NOT NULL DEFAULT 0,
                               `date_created` datetime NOT NULL DEFAULT current_timestamp(),
                               `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
                               `pagada` tinyint(1) NOT NULL DEFAULT 0,
                               PRIMARY KEY (`id`),
                               KEY `IDX_D342E761C7440455` (`client`),
                               CONSTRAINT `FK_D342E761C7440455` FOREIGN KEY (`client`) REFERENCES `client` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=891 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pedido`
--

DROP TABLE IF EXISTS `pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedido` (
                          `id` int(10) NOT NULL AUTO_INCREMENT,
                          `client` int(10) DEFAULT NULL,
                          `presupuesto` int(10) DEFAULT NULL,
                          `pedido` int(10) DEFAULT NULL,
                          `albaran` int(10) DEFAULT NULL,
                          `factura` int(10) DEFAULT NULL,
                          `serie` varchar(255) NOT NULL DEFAULT 'P',
                          `reference` int(10) NOT NULL DEFAULT 0,
                          `number` varchar(255) NOT NULL DEFAULT '',
                          `date` date NOT NULL DEFAULT curdate(),
                          `observaciones` longtext DEFAULT NULL,
                          `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
                          `tax` varchar(255) NOT NULL DEFAULT '21',
                          `total` decimal(12,2) NOT NULL DEFAULT 0.00,
                          `pagado` decimal(12,2) NOT NULL DEFAULT 0.00,
                          `balance` decimal(12,2) NOT NULL DEFAULT 0.00,
                          `comments` longtext DEFAULT NULL,
                          `date_created` datetime NOT NULL DEFAULT current_timestamp(),
                          `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
                          `orden` int(10) DEFAULT NULL,
                          `pagada` tinyint(1) NOT NULL DEFAULT 0,
                          `num_pedido` int(10) DEFAULT 0,
                          `bloqueada` tinyint(1) NOT NULL DEFAULT 1,
                          `importe_bruto` decimal(12,2) NOT NULL DEFAULT 0.00,
                          `discount` decimal(5,2) NOT NULL DEFAULT 0.00,
                          `discount_pp` decimal(5,2) NOT NULL DEFAULT 0.00,
                          `imp_discount` decimal(5,2) NOT NULL DEFAULT 0.00,
                          `imp_discount_pp` decimal(5,2) NOT NULL DEFAULT 0.00,
                          PRIMARY KEY (`id`),
                          KEY `IDX_C4EC16CEC7440455` (`client`),
                          KEY `IDX_C4EC16CE1B6368D3` (`presupuesto`),
                          KEY `IDX_C4EC16CEC4EC16CE` (`pedido`),
                          KEY `IDX_C4EC16CE2E6A49C2` (`albaran`),
                          KEY `IDX_C4EC16CEF9EBA009` (`factura`),
                          KEY `IDX_C4EC16CEE128CFD7` (`orden`),
                          CONSTRAINT `FK_C4EC16CE1B6368D3` FOREIGN KEY (`presupuesto`) REFERENCES `presupuesto` (`id`),
                          CONSTRAINT `FK_C4EC16CE2E6A49C2` FOREIGN KEY (`albaran`) REFERENCES `albaran` (`id`),
                          CONSTRAINT `FK_C4EC16CEC4EC16CE` FOREIGN KEY (`pedido`) REFERENCES `pedido` (`id`),
                          CONSTRAINT `FK_C4EC16CEC7440455` FOREIGN KEY (`client`) REFERENCES `client` (`id`),
                          CONSTRAINT `FK_C4EC16CEE128CFD7` FOREIGN KEY (`orden`) REFERENCES `orden_cobro` (`id`) ON DELETE SET NULL,
                          CONSTRAINT `FK_C4EC16CEF9EBA009` FOREIGN KEY (`factura`) REFERENCES `factura` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pedido_linea`
--

DROP TABLE IF EXISTS `pedido_linea`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedido_linea` (
                                `id` int(10) NOT NULL AUTO_INCREMENT,
                                `presupuesto` int(10) DEFAULT NULL,
                                `pedido` int(10) DEFAULT NULL,
                                `albaran` int(10) DEFAULT NULL,
                                `factura` int(10) DEFAULT NULL,
                                `reference` varchar(255) NOT NULL DEFAULT '',
                                `unidad_medida` varchar(255) NOT NULL DEFAULT '',
                                `description` longtext NOT NULL,
                                `quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
                                `quantity_certificada` decimal(10,0) NOT NULL DEFAULT 0,
                                `price` decimal(12,2) NOT NULL DEFAULT 0.00,
                                `tax` decimal(10,0) NOT NULL DEFAULT 0,
                                `total` decimal(12,2) NOT NULL DEFAULT 0.00,
                                `date_created` datetime NOT NULL DEFAULT current_timestamp(),
                                `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
                                `discount` decimal(10,0) NOT NULL DEFAULT 0,
                                `albaran_linea` int(10) DEFAULT NULL,
                                `factura_linea` int(10) DEFAULT NULL,
                                PRIMARY KEY (`id`),
                                KEY `IDX_F8CDCCB81B6368D3` (`presupuesto`),
                                KEY `IDX_F8CDCCB8C4EC16CE` (`pedido`),
                                KEY `IDX_F8CDCCB82E6A49C2` (`albaran`),
                                KEY `IDX_F8CDCCB8F9EBA009` (`factura`),
                                KEY `IDX_F8CDCCB8590BE848` (`albaran_linea`),
                                KEY `IDX_F8CDCCB8DAC3517A` (`factura_linea`),
                                CONSTRAINT `FK_F8CDCCB81B6368D3` FOREIGN KEY (`presupuesto`) REFERENCES `presupuesto` (`id`),
                                CONSTRAINT `FK_F8CDCCB82E6A49C2` FOREIGN KEY (`albaran`) REFERENCES `albaran` (`id`),
                                CONSTRAINT `FK_F8CDCCB8590BE848` FOREIGN KEY (`albaran_linea`) REFERENCES `albaran_linea` (`id`),
                                CONSTRAINT `FK_F8CDCCB8C4EC16CE` FOREIGN KEY (`pedido`) REFERENCES `pedido` (`id`),
                                CONSTRAINT `FK_F8CDCCB8DAC3517A` FOREIGN KEY (`factura_linea`) REFERENCES `factura_linea` (`id`),
                                CONSTRAINT `FK_F8CDCCB8F9EBA009` FOREIGN KEY (`factura`) REFERENCES `factura` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pedido_linea_certificada`
--

DROP TABLE IF EXISTS `pedido_linea_certificada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedido_linea_certificada` (
                                            `id` int(10) NOT NULL AUTO_INCREMENT,
                                            `linea` int(10) DEFAULT NULL,
                                            `presupuesto` int(10) DEFAULT NULL,
                                            `pedido` int(10) DEFAULT NULL,
                                            `albaran` int(10) DEFAULT NULL,
                                            `factura` int(10) DEFAULT NULL,
                                            `quantity` decimal(10,0) NOT NULL DEFAULT 0,
                                            `date_created` datetime NOT NULL DEFAULT current_timestamp(),
                                            `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
                                            PRIMARY KEY (`id`),
                                            KEY `IDX_4BB0CBC2BCB8FDDE` (`linea`),
                                            KEY `IDX_4BB0CBC21B6368D3` (`presupuesto`),
                                            KEY `IDX_4BB0CBC2C4EC16CE` (`pedido`),
                                            KEY `IDX_4BB0CBC22E6A49C2` (`albaran`),
                                            KEY `IDX_4BB0CBC2F9EBA009` (`factura`),
                                            CONSTRAINT `FK_4BB0CBC21B6368D3` FOREIGN KEY (`presupuesto`) REFERENCES `presupuesto` (`id`),
                                            CONSTRAINT `FK_4BB0CBC22E6A49C2` FOREIGN KEY (`albaran`) REFERENCES `albaran` (`id`),
                                            CONSTRAINT `FK_4BB0CBC2BCB8FDDE` FOREIGN KEY (`linea`) REFERENCES `pedido_linea` (`id`),
                                            CONSTRAINT `FK_4BB0CBC2C4EC16CE` FOREIGN KEY (`pedido`) REFERENCES `pedido` (`id`),
                                            CONSTRAINT `FK_4BB0CBC2F9EBA009` FOREIGN KEY (`factura`) REFERENCES `factura` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `presupuesto`
--

DROP TABLE IF EXISTS `presupuesto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuesto` (
                               `id` int(10) NOT NULL AUTO_INCREMENT,
                               `client` int(10) DEFAULT NULL,
                               `presupuesto` int(10) DEFAULT NULL,
                               `pedido` int(10) DEFAULT NULL,
                               `albaran` int(10) DEFAULT NULL,
                               `factura` int(10) DEFAULT NULL,
                               `serie` varchar(255) NOT NULL DEFAULT 'P',
                               `reference` int(10) NOT NULL DEFAULT 0,
                               `number` varchar(255) NOT NULL DEFAULT '',
                               `date` date NOT NULL DEFAULT curdate(),
                               `observaciones` longtext DEFAULT NULL,
                               `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
                               `tax` varchar(255) NOT NULL DEFAULT '21',
                               `total` decimal(12,2) NOT NULL DEFAULT 0.00,
                               `pagado` decimal(12,2) NOT NULL DEFAULT 0.00,
                               `balance` decimal(12,2) NOT NULL DEFAULT 0.00,
                               `comments` longtext DEFAULT NULL,
                               `date_created` datetime NOT NULL DEFAULT current_timestamp(),
                               `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
                               `orden` int(10) DEFAULT NULL,
                               `pagada` tinyint(1) NOT NULL DEFAULT 0,
                               `num_pedido` int(10) DEFAULT 0,
                               `bloqueada` tinyint(1) NOT NULL DEFAULT 1,
                               `importe_bruto` decimal(12,2) NOT NULL DEFAULT 0.00,
                               `discount` decimal(5,2) NOT NULL DEFAULT 0.00,
                               `discount_pp` decimal(5,2) NOT NULL DEFAULT 0.00,
                               `imp_discount` decimal(5,2) NOT NULL DEFAULT 0.00,
                               `imp_discount_pp` decimal(5,2) NOT NULL DEFAULT 0.00,
                               PRIMARY KEY (`id`),
                               KEY `IDX_1B6368D3C7440455` (`client`),
                               KEY `IDX_1B6368D31B6368D3` (`presupuesto`),
                               KEY `IDX_1B6368D3C4EC16CE` (`pedido`),
                               KEY `IDX_1B6368D32E6A49C2` (`albaran`),
                               KEY `IDX_1B6368D3F9EBA009` (`factura`),
                               KEY `IDX_1B6368D3E128CFD7` (`orden`),
                               CONSTRAINT `FK_1B6368D31B6368D3` FOREIGN KEY (`presupuesto`) REFERENCES `presupuesto` (`id`),
                               CONSTRAINT `FK_1B6368D32E6A49C2` FOREIGN KEY (`albaran`) REFERENCES `albaran` (`id`),
                               CONSTRAINT `FK_1B6368D3C4EC16CE` FOREIGN KEY (`pedido`) REFERENCES `pedido` (`id`),
                               CONSTRAINT `FK_1B6368D3C7440455` FOREIGN KEY (`client`) REFERENCES `client` (`id`),
                               CONSTRAINT `FK_1B6368D3E128CFD7` FOREIGN KEY (`orden`) REFERENCES `orden_cobro` (`id`) ON DELETE SET NULL,
                               CONSTRAINT `FK_1B6368D3F9EBA009` FOREIGN KEY (`factura`) REFERENCES `factura` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `presupuesto_linea`
--

DROP TABLE IF EXISTS `presupuesto_linea`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuesto_linea` (
                                     `id` int(10) NOT NULL AUTO_INCREMENT,
                                     `presupuesto` int(10) DEFAULT NULL,
                                     `pedido` int(10) DEFAULT NULL,
                                     `albaran` int(10) DEFAULT NULL,
                                     `factura` int(10) DEFAULT NULL,
                                     `reference` varchar(255) NOT NULL DEFAULT '',
                                     `unidad_medida` varchar(255) NOT NULL DEFAULT '',
                                     `description` longtext NOT NULL,
                                     `quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
                                     `quantity_certificada` decimal(10,0) NOT NULL DEFAULT 0,
                                     `price` decimal(12,2) NOT NULL DEFAULT 0.00,
                                     `tax` decimal(10,0) NOT NULL DEFAULT 0,
                                     `total` decimal(12,2) NOT NULL DEFAULT 0.00,
                                     `date_created` datetime NOT NULL DEFAULT current_timestamp(),
                                     `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
                                     `discount` decimal(10,0) NOT NULL DEFAULT 0,
                                     `albaran_linea` int(10) DEFAULT NULL,
                                     `factura_linea` int(10) DEFAULT NULL,
                                     PRIMARY KEY (`id`),
                                     KEY `IDX_328857331B6368D3` (`presupuesto`),
                                     KEY `IDX_32885733C4EC16CE` (`pedido`),
                                     KEY `IDX_328857332E6A49C2` (`albaran`),
                                     KEY `IDX_32885733F9EBA009` (`factura`),
                                     KEY `IDX_32885733590BE848` (`albaran_linea`),
                                     KEY `IDX_32885733DAC3517A` (`factura_linea`),
                                     CONSTRAINT `FK_328857331B6368D3` FOREIGN KEY (`presupuesto`) REFERENCES `presupuesto` (`id`),
                                     CONSTRAINT `FK_328857332E6A49C2` FOREIGN KEY (`albaran`) REFERENCES `albaran` (`id`),
                                     CONSTRAINT `FK_32885733590BE848` FOREIGN KEY (`albaran_linea`) REFERENCES `albaran_linea` (`id`),
                                     CONSTRAINT `FK_32885733C4EC16CE` FOREIGN KEY (`pedido`) REFERENCES `pedido` (`id`),
                                     CONSTRAINT `FK_32885733DAC3517A` FOREIGN KEY (`factura_linea`) REFERENCES `factura_linea` (`id`),
                                     CONSTRAINT `FK_32885733F9EBA009` FOREIGN KEY (`factura`) REFERENCES `factura` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `presupuesto_linea_certificada`
--

DROP TABLE IF EXISTS `presupuesto_linea_certificada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuesto_linea_certificada` (
                                                 `id` int(10) NOT NULL AUTO_INCREMENT,
                                                 `linea` int(10) DEFAULT NULL,
                                                 `presupuesto` int(10) DEFAULT NULL,
                                                 `pedido` int(10) DEFAULT NULL,
                                                 `albaran` int(10) DEFAULT NULL,
                                                 `factura` int(10) DEFAULT NULL,
                                                 `quantity` decimal(10,0) NOT NULL DEFAULT 0,
                                                 `date_created` datetime NOT NULL DEFAULT current_timestamp(),
                                                 `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
                                                 PRIMARY KEY (`id`),
                                                 KEY `IDX_F5A9330EBCB8FDDE` (`linea`),
                                                 KEY `IDX_F5A9330E1B6368D3` (`presupuesto`),
                                                 KEY `IDX_F5A9330EC4EC16CE` (`pedido`),
                                                 KEY `IDX_F5A9330E2E6A49C2` (`albaran`),
                                                 KEY `IDX_F5A9330EF9EBA009` (`factura`),
                                                 CONSTRAINT `FK_F5A9330E1B6368D3` FOREIGN KEY (`presupuesto`) REFERENCES `presupuesto` (`id`),
                                                 CONSTRAINT `FK_F5A9330E2E6A49C2` FOREIGN KEY (`albaran`) REFERENCES `albaran` (`id`),
                                                 CONSTRAINT `FK_F5A9330EBCB8FDDE` FOREIGN KEY (`linea`) REFERENCES `presupuesto_linea` (`id`),
                                                 CONSTRAINT `FK_F5A9330EC4EC16CE` FOREIGN KEY (`pedido`) REFERENCES `pedido` (`id`),
                                                 CONSTRAINT `FK_F5A9330EF9EBA009` FOREIGN KEY (`factura`) REFERENCES `factura` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `producto`
--

DROP TABLE IF EXISTS `producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `producto` (
                            `id` int(10) NOT NULL AUTO_INCREMENT,
                            `name` varchar(255) NOT NULL DEFAULT '',
                            `reference` varchar(255) NOT NULL DEFAULT '',
                            `cost` decimal(10,2) NOT NULL DEFAULT 0.00,
                            `price` decimal(10,2) NOT NULL DEFAULT 0.00,
                            `tax` varchar(255) NOT NULL DEFAULT '21',
                            `unidad_medida` varchar(255) NOT NULL DEFAULT '',
                            `active` tinyint(1) NOT NULL DEFAULT 1,
                            `date_created` datetime NOT NULL DEFAULT current_timestamp(),
                            `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
                            PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profile` (
                           `id` int(10) NOT NULL AUTO_INCREMENT,
                           `parent_id` int(10) DEFAULT NULL,
                           `name` varchar(50) NOT NULL,
                           `reference` varchar(50) NOT NULL,
                           `active` tinyint(1) NOT NULL DEFAULT 1,
                           `date_created` datetime DEFAULT NULL,
                           `date_modified` datetime DEFAULT NULL,
                           PRIMARY KEY (`id`),
                           UNIQUE KEY `UNIQ_8157AA0F727ACA70` (`parent_id`),
                           CONSTRAINT `FK_8157AA0F727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `profile` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `profile_action`
--

DROP TABLE IF EXISTS `profile_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profile_action` (
                                  `id` int(10) NOT NULL AUTO_INCREMENT,
                                  `group_id` int(10) DEFAULT NULL,
                                  `name` varchar(50) NOT NULL,
                                  `date_created` datetime DEFAULT NULL,
                                  `date_modified` datetime DEFAULT NULL,
                                  PRIMARY KEY (`id`),
                                  UNIQUE KEY `name_group` (`name`,`group_id`),
                                  KEY `IDX_2FE6EBF5FE54D947` (`group_id`),
                                  CONSTRAINT `FK_2FE6EBF5FE54D947` FOREIGN KEY (`group_id`) REFERENCES `profile_action_group` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `profile_action_group`
--

DROP TABLE IF EXISTS `profile_action_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profile_action_group` (
                                        `id` int(10) NOT NULL AUTO_INCREMENT,
                                        `name` varchar(50) NOT NULL,
                                        `date_created` datetime DEFAULT NULL,
                                        `date_modified` datetime DEFAULT NULL,
                                        PRIMARY KEY (`id`),
                                        UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `profile_action_rel`
--

DROP TABLE IF EXISTS `profile_action_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profile_action_rel` (
                                      `id` int(10) NOT NULL AUTO_INCREMENT,
                                      `profile_id` int(10) DEFAULT NULL,
                                      `action_id` int(10) DEFAULT NULL,
                                      `date_created` datetime DEFAULT NULL,
                                      `date_modified` datetime DEFAULT NULL,
                                      PRIMARY KEY (`id`),
                                      UNIQUE KEY `profile_action` (`profile_id`,`action_id`),
                                      KEY `IDX_E2B7EA0CCFA12B8` (`profile_id`),
                                      KEY `IDX_E2B7EA09D32F035` (`action_id`),
                                      CONSTRAINT `FK_E2B7EA09D32F035` FOREIGN KEY (`action_id`) REFERENCES `profile_action` (`id`),
                                      CONSTRAINT `FK_E2B7EA0CCFA12B8` FOREIGN KEY (`profile_id`) REFERENCES `profile` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
                        `id` int(10) NOT NULL AUTO_INCREMENT,
                        `profile_id` int(10) DEFAULT NULL,
                        `type_id` int(10) DEFAULT NULL,
                        `nif` varchar(20) NOT NULL,
                        `firstname` varchar(100) NOT NULL,
                        `lastname` varchar(100) NOT NULL,
                        `ss_num` varchar(20) NOT NULL,
                        `ccc_num` varchar(29) NOT NULL,
                        `contract` int(10) DEFAULT NULL,
                        `hours` int(10) NOT NULL DEFAULT 0,
                        `phone` varchar(20) DEFAULT NULL,
                        `mobile` varchar(20) DEFAULT NULL,
                        `email` varchar(100) NOT NULL,
                        `username` varchar(50) NOT NULL,
                        `password` varchar(64) NOT NULL,
                        `token` varchar(64) NOT NULL,
                        `date_start` datetime DEFAULT NULL,
                        `date_end` datetime DEFAULT NULL,
                        `date_created` datetime DEFAULT NULL,
                        `date_modified` datetime DEFAULT NULL,
                        `active` tinyint(1) NOT NULL DEFAULT 1,
                        PRIMARY KEY (`id`),
                        UNIQUE KEY `username` (`username`),
                        UNIQUE KEY `email` (`email`),
                        KEY `IDX_8D93D649CCFA12B8` (`profile_id`),
                        KEY `IDX_8D93D649C54C8C93` (`type_id`),
                        KEY `token` (`token`),
                        KEY `password` (`password`),
                        KEY `username_password` (`username`,`password`),
                        KEY `email_password` (`email`,`password`),
                        CONSTRAINT `FK_8D93D649C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `user_type` (`id`),
                        CONSTRAINT `FK_8D93D649CCFA12B8` FOREIGN KEY (`profile_id`) REFERENCES `profile` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_departament`
--

DROP TABLE IF EXISTS `user_departament`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_departament` (
                                    `id` int(10) NOT NULL AUTO_INCREMENT,
                                    `departament_id` int(10) DEFAULT NULL,
                                    `user_id` int(10) DEFAULT NULL,
                                    `date_created` datetime DEFAULT NULL,
                                    `date_modified` datetime DEFAULT NULL,
                                    PRIMARY KEY (`id`),
                                    UNIQUE KEY `user_departament` (`departament_id`,`user_id`),
                                    KEY `IDX_9DED343648B3EEE4` (`departament_id`),
                                    KEY `IDX_9DED3436A76ED395` (`user_id`),
                                    CONSTRAINT `FK_9DED343648B3EEE4` FOREIGN KEY (`departament_id`) REFERENCES `departament` (`id`),
                                    CONSTRAINT `FK_9DED3436A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_hours`
--

DROP TABLE IF EXISTS `user_hours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_hours` (
                              `id` int(10) NOT NULL AUTO_INCREMENT,
                              `user_id` int(10) DEFAULT NULL,
                              `date_start` datetime DEFAULT NULL,
                              `date_end` datetime DEFAULT NULL,
                              PRIMARY KEY (`id`),
                              KEY `IDX_68D846D5A76ED395` (`user_id`),
                              CONSTRAINT `FK_68D846D5A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_type`
--

DROP TABLE IF EXISTS `user_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_type` (
                             `id` int(10) NOT NULL AUTO_INCREMENT,
                             `reference` varchar(50) NOT NULL,
                             `name` varchar(50) NOT NULL,
                             `date_created` datetime DEFAULT NULL,
                             `date_modified` datetime DEFAULT NULL,
                             PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-03 13:36:00
