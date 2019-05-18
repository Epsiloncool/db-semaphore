SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for dbsem_locks
-- ----------------------------
DROP TABLE IF EXISTS `dbsem_locks`;
CREATE TABLE `dbsem_locks` (
  `ident` varchar(255) NOT NULL DEFAULT '',
  `process_id` varchar(255) DEFAULT '',
  `expire_dt` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ident`),
  UNIQUE KEY `ident` (`ident`) USING BTREE,
  KEY `process_id` (`process_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dbsem_locks
-- ----------------------------
SET FOREIGN_KEY_CHECKS=1;
