/*
SQLyog Community v13.1.5  (64 bit)
MySQL - 10.4.6-MariaDB : Database - wa_termux
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`wa` /*!40100 DEFAULT CHARACTER SET latin1 */;

/*Table structure for table `tbl_chats` */

DROP TABLE IF EXISTS `tbl_chats`;

CREATE TABLE `tbl_chats` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_user` varchar(128) DEFAULT NULL,
  `id_chat` varchar(128) DEFAULT NULL,
  `dest` varchar(50) DEFAULT NULL,
  `sendtype` varchar(20) DEFAULT NULL,
  `ack` smallint(6) DEFAULT NULL,
  `comm` varchar(2) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `submitted` datetime DEFAULT NULL,
  `processed` datetime DEFAULT NULL,
  `sent` datetime DEFAULT NULL,
  `received` datetime DEFAULT NULL,
  `flag_proc` varchar(1) DEFAULT NULL,
  `appr_id` varchar(50) DEFAULT NULL,
  `appr_stat` varchar(10) DEFAULT NULL,
  `chatid` varchar(15) DEFAULT NULL,
  `filename` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tbl_chat_log` (`sendtype`,`comm`,`submitted`),
  KEY `dest` (`dest`),
  KEY `submitted` (`submitted`),
  KEY `ack` (`ack`),
  KEY `sendtype` (`sendtype`),
  KEY `id_chat` (`id_chat`),
  KEY `id_user` (`id_user`),
  KEY `comm` (`comm`)
) ENGINE=InnoDB AUTO_INCREMENT=10713 DEFAULT CHARSET=latin1;

/*Data for the table `tbl_chats` */
/*Table structure for table `tbl_response` */

DROP TABLE IF EXISTS `tbl_response`;

CREATE TABLE `tbl_response` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parameter` varchar(50) DEFAULT NULL,
  `response` longtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

/*Data for the table `tbl_response` */

insert  into `tbl_response`(`id`,`parameter`,`response`) values (1,'terima kasih','Sama Sama');
insert  into `tbl_response`(`id`,`parameter`,`response`) values (2,'selamat malam','Selamat Malam Juga');
insert  into `tbl_response`(`id`,`parameter`,`response`) values (3,'selamat pagi','Selamat Pagi Juga');
insert  into `tbl_response`(`id`,`parameter`,`response`) values (4,'hai','Hai, apa yang bisa dibantu');
insert  into `tbl_response`(`id`,`parameter`,`response`) values (5,'moring','Morning too');
insert  into `tbl_response`(`id`,`parameter`,`response`) values (6,'apa khabar','halo , Apa kahabar, bagaimana saya bisa bantu');
insert  into `tbl_response`(`id`,`parameter`,`response`) values (7,'menu','List Menu\r\n---------------------------------------\r\n/listtask  => List task yang di assign\r\n/appr =>  untuk approve task \r\n/restart  => untuk restart bot\r\n/info   => info system\r\n--------------------------------------\r\nSilahkan ketik menu anda');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
