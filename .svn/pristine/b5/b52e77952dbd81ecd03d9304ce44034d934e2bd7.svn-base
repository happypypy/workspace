/*
 Navicat Premium Data Transfer

 Source Server         : 192.168.168.146
 Source Server Type    : MySQL
 Source Server Version : 100403
 Source Host           : 192.168.168.146:3306
 Source Schema         : cms

 Target Server Type    : MySQL
 Target Server Version : 100403
 File Encoding         : 65001

 Date: 03/06/2019 10:42:49
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cms_marketing_package
-- ----------------------------
DROP TABLE IF EXISTS `cms_marketing_package`;
CREATE TABLE `cms_marketing_package`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '营销ID',
  `marketing_package_code` varchar(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '营销包代号',
  `marketing_package_name` varchar(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '营销包名称',
  `marketing_package_desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '营销包描述',
  `module_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '模型ID',
  `is_use` tinyint(1) NOT NULL COMMENT '是否启用，0：禁用 1：启用',
  `idorder` int(10) NOT NULL COMMENT '排序号',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '营销包表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_marketing_package_log
-- ----------------------------
DROP TABLE IF EXISTS `cms_marketing_package_log`;
CREATE TABLE `cms_marketing_package_log`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `idaccount` int(10) NOT NULL COMMENT '操作人ID',
  `chrname` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '操作人昵称',
  `siteid` int(10) NOT NULL COMMENT '站点ID',
  `marketing_package_id` int(10) NOT NULL COMMENT '营销包id',
  `marketing_package_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '营销包名称',
  `state` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '状态，use: 开通，forbidden：停用',
  `ip` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'ip',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '营销包操作日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_marketing_package_record
-- ----------------------------
DROP TABLE IF EXISTS `cms_marketing_package_record`;
CREATE TABLE `cms_marketing_package_record`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `siteid` int(10) NOT NULL COMMENT '站点ID',
  `marketing_package_code` varchar(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '营销包代号',
  `marketing_package_id` int(10) NOT NULL COMMENT '营销包ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '营销包记录表' ROW_FORMAT = Dynamic;

ALTER TABLE `cms_module` ADD marketing_package_id INT(10) NOT NULL DEFAULT 0 COMMENT '营销包ID';
INSERT INTO `cms_extended_module`(`idmodule`, `codecatalog`, `chrname`, `chrcode`, `chrimgpath`, `intsn`, `operation`, `intflag`, `textremark`, `idsite`) VALUES (0, 'cmsmanage', '积分功能', 'integral', '/index.phpuploads/module/', 9, 'index', 2, '', 0);

SET FOREIGN_KEY_CHECKS = 1;
