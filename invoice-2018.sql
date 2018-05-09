-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 05, 2018 at 03:57 PM
-- Server version: 5.7.19
-- PHP Version: 7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `invoice-2018`
--

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

DROP TABLE IF EXISTS `invoice`;
CREATE TABLE IF NOT EXISTS `invoice` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `repertory` int(11) UNSIGNED NOT NULL COMMENT 'ID du client',
  `quote` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID du devis (si transformation)',
  `ref` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'référence du devis',
  `startDate` date NOT NULL COMMENT 'Date de création',
  `endDate` date NOT NULL COMMENT 'Date de limite de paiement',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Status (Brouillons, Non payé, payé)',
  `publicnote` varchar(400) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Note public',
  `privatenote` varchar(400) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Note privé',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='M_INVOICE';

-- --------------------------------------------------------

--
-- Table structure for table `invoicepayment`
--

DROP TABLE IF EXISTS `invoicepayment`;
CREATE TABLE IF NOT EXISTS `invoicepayment` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice` int(11) UNSIGNED NOT NULL COMMENT 'ID de la facture',
  `method` tinyint(3) UNSIGNED NOT NULL COMMENT 'Method de payment',
  `date` datetime NOT NULL COMMENT 'Date du paiement',
  `amount` double(10,2) NOT NULL COMMENT 'Montant payé',
  `publicnote` varchar(400) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Note public',
  `privatenote` varchar(400) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Note privé',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='M_INVOICE_PAYMENT';

-- --------------------------------------------------------

--
-- Table structure for table `invoicerow`
--

DROP TABLE IF EXISTS `invoicerow`;
CREATE TABLE IF NOT EXISTS `invoicerow` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice` int(11) UNSIGNED NOT NULL COMMENT 'ID de la facture',
  `label` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Nom de l''article',
  `quantity` int(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Quantité de l''article',
  `tax` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Taxe de l''article',
  `unityprice` double(10,2) NOT NULL COMMENT 'Prix unitaire',
  `note` varchar(400) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Note ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='M_INVOICE_ROW';

-- --------------------------------------------------------

--
-- Table structure for table `quote`
--

DROP TABLE IF EXISTS `quote`;
CREATE TABLE IF NOT EXISTS `quote` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `repertory` int(11) UNSIGNED NOT NULL COMMENT 'ID du client',
  `ref` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'référence du devis',
  `creationDate` date NOT NULL COMMENT 'date de création',
  `dueDate` date NOT NULL COMMENT 'date d''échéance/validité',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'status (en cours, envoyer, transformer...)',
  `amount` double(10,2) NOT NULL DEFAULT '0.00' COMMENT 'montant total, si besoin.',
  `publicnote` varchar(400) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'note public (peu être afficher sur le devis)',
  `privatenote` varchar(400) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'note privé (pour l''administration)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ref` (`ref`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='M_QUOTE';

-- --------------------------------------------------------

--
-- Table structure for table `quoterow`
--

DROP TABLE IF EXISTS `quoterow`;
CREATE TABLE IF NOT EXISTS `quoterow` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quote` int(11) UNSIGNED NOT NULL COMMENT 'ID du devis',
  `label` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Nom de l''article',
  `quantity` int(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Quantité de l''article',
  `tax` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Taxe sur l''article',
  `unityprice` double(10,2) NOT NULL COMMENT 'Montant unitaire',
  `note` varchar(400) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Note',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='M_QUOTE_ROW';

-- --------------------------------------------------------

--
-- Table structure for table `tax`
--

DROP TABLE IF EXISTS `tax`;
CREATE TABLE IF NOT EXISTS `tax` (
  `id` tinyint(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  `amount` double UNSIGNED NOT NULL COMMENT 'Pourcentage de la taxe',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Taxes';

-- --------------------------------------------------------

--
-- Table structure for table `x_repertory`
--

DROP TABLE IF EXISTS `x_repertory`;
CREATE TABLE IF NOT EXISTS `x_repertory` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Nom du contact',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='M_REPERTORY';

-- --------------------------------------------------------

--
-- Table structure for table `x_stockarticle`
--

DROP TABLE IF EXISTS `x_stockarticle`;
CREATE TABLE IF NOT EXISTS `x_stockarticle` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `label` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Libéllé du produit',
  `sellingPriceDutyFree` decimal(10,2) DEFAULT NULL COMMENT 'Prix de vente HT',
  `buyingprice` decimal(10,2) DEFAULT NULL COMMENT 'Prix d''achat',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='M_STOCK_ARTICLE';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
