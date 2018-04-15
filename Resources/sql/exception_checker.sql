/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

SET FOREIGN_KEY_CHECKS=0;

-- --------------------------------------
-- Table structure for exception_checker
-- --------------------------------------
-- DROP TABLE IF EXISTS `exception_checker`;
CREATE TABLE `exception_checker` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `kind` varchar(24) NOT NULL,
  `redirect_kind` varchar(24) DEFAULT NULL,
  `redirect_data` varchar(255) DEFAULT NULL,
  `creation` datetime NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `un_exception_checker` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


/*
Well known tested links to be excluded. Un-comment if you wish to add them

LOCK TABLES `exception_checker` WRITE;
INSERT INTO `exception_checker` (url, kind, redirect_kind, redirect_data, creation) VALUES
('/.well-known*','excluded',NULL,NULL,'2018-04-15 15:15:15'),
('/admin*','excluded',NULL,NULL,'2018-04-15 15:15:15'),
('/cgi-bin*','excluded',NULL,NULL,'2018-04-15 15:15:15'),
('/HNAP1*','excluded',NULL,NULL,'2018-04-15 15:15:15'),
('/joomla*','excluded',NULL,NULL,'2018-04-15 15:15:15'),
('/google*','excluded',NULL,NULL,'2018-04-15 15:15:15'),
('/my-admin*','excluded',NULL,NULL,'2018-04-15 15:15:15'),
('/phpmyadmin*','excluded',NULL,NULL,'2018-04-15 15:15:15'),
('/wordpress*','excluded',NULL,NULL,'2018-04-15 15:15:15'),
('/wp*','excluded',NULL,NULL,'2018-04-15 15:15:15')
;
UNLOCK TABLES;
*/



