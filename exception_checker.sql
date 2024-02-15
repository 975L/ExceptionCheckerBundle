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
  `creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `un_exception_checker` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


/*
-- Well known tested links to be excluded. Un-comment if you wish to add them

LOCK TABLES `exception_checker` WRITE;
INSERT INTO `exception_checker` (url, kind) VALUES
('/.well-known*','excluded'),
('/admin*','excluded'),
('/cgi-bin*','excluded'),
('/HNAP1*','excluded'),
('/joomla*','excluded'),
('/google*','excluded'),
('/my-admin*','excluded'),
('/phpmyadmin*','excluded'),
('/wordpress*','excluded'),
('/wp*','excluded')
;
UNLOCK TABLES;
*/