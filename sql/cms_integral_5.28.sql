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

 Date: 28/05/2019 10:51:48
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cms_integral_rule_config
-- ----------------------------
DROP TABLE IF EXISTS `cms_integral_rule_config`;
CREATE TABLE `cms_integral_rule_config`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `idsite` int(10) NOT NULL COMMENT '机构ID',
  `is_sign` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否开启签到，0:关闭 1:开启',
  `signin_integral` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '签到积分规则',
  `is_signup` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否开启报名，0:关闭 1:开启',
  `sign_rule` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '签到规则',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '积f分规则配置表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cms_integral_rule_config
-- ----------------------------
INSERT INTO `cms_integral_rule_config` VALUES (3, 8, 1, '', 0, '');
INSERT INTO `cms_integral_rule_config` VALUES (4, 7, 1, '[\"5\",\"10\",\"15\"]', 0, '<p>1、第一次签到获得5点积分；</p><p>2、连续签到：每日递增5积分，最多20积分；</p><p>3、如果中间中断了连续签到，则积分重新从5分开始递增。</p>');

-- ----------------------------
-- Table structure for cms_member_sign_record
-- ----------------------------
DROP TABLE IF EXISTS `cms_member_sign_record`;
CREATE TABLE `cms_member_sign_record`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `member_id` int(10) NOT NULL COMMENT '会员ID',
  `sign_integral` int(10) NOT NULL COMMENT '签到积分',
  `sign_day` int(10) NOT NULL COMMENT '签到天数',
  `create_time` int(10) NOT NULL COMMENT '签到时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 41 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '会员 - 签记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cms_member_sign_record
-- ----------------------------
INSERT INTO `cms_member_sign_record` VALUES (1, 51, 5, 1, 1557812725);
INSERT INTO `cms_member_sign_record` VALUES (6, 51, 10, 2, 1557899125);
INSERT INTO `cms_member_sign_record` VALUES (7, 51, 15, 3, 1557985525);
INSERT INTO `cms_member_sign_record` VALUES (10, 51, 15, 4, 1558071925);
INSERT INTO `cms_member_sign_record` VALUES (11, 51, 15, 5, 1558158325);
INSERT INTO `cms_member_sign_record` VALUES (12, 51, 15, 6, 1558244725);
INSERT INTO `cms_member_sign_record` VALUES (13, 51, 15, 7, 1558331125);
INSERT INTO `cms_member_sign_record` VALUES (14, 51, 15, 8, 1558417525);
INSERT INTO `cms_member_sign_record` VALUES (15, 51, 15, 9, 1558503925);
INSERT INTO `cms_member_sign_record` VALUES (16, 51, 15, 10, 1558590325);
INSERT INTO `cms_member_sign_record` VALUES (17, 51, 15, 11, 1557812725);
INSERT INTO `cms_member_sign_record` VALUES (18, 51, 15, 12, 1557812725);
INSERT INTO `cms_member_sign_record` VALUES (27, 51, 5, 1, 1558921743);
INSERT INTO `cms_member_sign_record` VALUES (38, 51, 5, 1, 1558921743);
INSERT INTO `cms_member_sign_record` VALUES (39, 51, 10, 2, 1558921743);
INSERT INTO `cms_member_sign_record` VALUES (40, 51, 15, 3, 1559008202);

ALTER TABLE cms_member ADD continue_sign INT(10) NOT NULL DEFAULT 0 COMMENT '连续签到天数';

SET FOREIGN_KEY_CHECKS = 1;
