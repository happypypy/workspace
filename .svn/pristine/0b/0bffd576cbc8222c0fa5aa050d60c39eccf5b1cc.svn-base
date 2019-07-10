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

 Date: 10/06/2019 15:14:51
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

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
) ENGINE = InnoDB AUTO_INCREMENT = 44 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '会员 - 签记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_member_integral_record
-- ----------------------------
DROP TABLE IF EXISTS `cms_member_integral_record`;
CREATE TABLE `cms_member_integral_record`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `member_id` int(10) NOT NULL COMMENT '会员ID',
  `category_id` int(10) NOT NULL COMMENT '积分分类ID',
  `order_id` int(10) NOT NULL DEFAULT 0 COMMENT '订单ID',
  `integral` int(10) NOT NULL COMMENT '积分',
  `integral_rmark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `is_freeze` tinyint(1) NOT NULL DEFAULT 0 COMMENT '积分是否冻结,0:无需要冻结,1:冻结，2:已解冻',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 43 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '会员 - 积分记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_marketing_package_record
-- ----------------------------
DROP TABLE IF EXISTS `cms_marketing_package_record`;
CREATE TABLE `cms_marketing_package_record`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `siteid` int(10) NOT NULL COMMENT '站点ID',
  `marketing_package_code` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `marketing_package_id` int(10) NOT NULL COMMENT '营销包ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 37 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '营销包记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_marketing_package_log
-- ----------------------------
DROP TABLE IF EXISTS `cms_marketing_package_log`;
CREATE TABLE `cms_marketing_package_log`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `idaccount` int(10) NOT NULL COMMENT '操作人ID',
  `chrname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `siteid` int(10) NOT NULL COMMENT '站点ID',
  `marketing_package_id` int(10) NOT NULL COMMENT '营销包id',
  `marketing_package_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `state` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `ip` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 40 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '营销包操作日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_marketing_package
-- ----------------------------
DROP TABLE IF EXISTS `cms_marketing_package`;
CREATE TABLE `cms_marketing_package`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '营销ID',
  `marketing_package_code` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `marketing_package_name` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `marketing_package_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `module_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `is_use` tinyint(1) NOT NULL COMMENT '是否启用，0：禁用 1：启用',
  `idorder` int(10) NOT NULL COMMENT '排序号',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '营销包表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_integral_rule_config
-- ----------------------------
DROP TABLE IF EXISTS `cms_integral_rule_config`;
CREATE TABLE `cms_integral_rule_config`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `idsite` int(10) NOT NULL COMMENT '机构ID',
  `is_sign` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否开启签到，0:关闭 1:开启',
  `signin_integral` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `is_signup` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否开启报名，0:关闭 1:开启',
  `sign_rule` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '签到规则',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '积f分规则配置表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_integral_mall_goods
-- ----------------------------
DROP TABLE IF EXISTS `cms_integral_mall_goods`;
CREATE TABLE `cms_integral_mall_goods`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `siteid` int(10) NOT NULL COMMENT '机构ID',
  `account_id` int(10) NOT NULL DEFAULT 0 COMMENT '账号ID',
  `goods_name` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `goods_thumb` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `goods_picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `goods_content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商品内容',
  `suitable_age_start` int(10) NOT NULL COMMENT '适合开始年龄',
  `suitable_age_end` int(10) NOT NULL COMMENT '适合结束年龄',
  `integral` int(10) NOT NULL COMMENT '所需积分',
  `goods_number` int(10) NOT NULL COMMENT '商品数量',
  `exchange_number` int(10) NULL DEFAULT 0 COMMENT '已兑换数量',
  `is_display` tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否显示，0:否 1:是',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '积分商城 - 商品表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_integral_mall_exchange_record
-- ----------------------------
DROP TABLE IF EXISTS `cms_integral_mall_exchange_record`;
CREATE TABLE `cms_integral_mall_exchange_record`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `member_id` int(10) NOT NULL COMMENT '会员ID',
  `goods_id` int(10) NOT NULL COMMENT '兑换的商品ID',
  `order_no` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `exchange_number` int(10) NOT NULL COMMENT '兑换商品数量',
  `integral` int(10) NOT NULL COMMENT '兑换所花费积分数量',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态，0:待处理 1:已处理 2:已取消',
  `consignee_name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `consignee_phone` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `consignee_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `courier_company` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `courier_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `order_remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `create_time` int(10) NOT NULL COMMENT '兑换时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 28 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '积分商城 - 兑换记录表' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------
-- Table structure for cms_member_integral_category
-- ----------------------------
DROP TABLE IF EXISTS `cms_member_integral_category`;
CREATE TABLE `cms_member_integral_category`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `display_order` int(10) NOT NULL DEFAULT 0 COMMENT '显示排序',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '会员 - 积分类别表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cms_member_integral_category
-- ----------------------------
INSERT INTO `cms_member_integral_category` VALUES (1, '签到', 0);
INSERT INTO `cms_member_integral_category` VALUES (2, '活动报名', 0);
INSERT INTO `cms_member_integral_category` VALUES (3, '评论', 0);
INSERT INTO `cms_member_integral_category` VALUES (4, '活动退款', 0);
INSERT INTO `cms_member_integral_category` VALUES (5, '积分商城兑换', 0);
INSERT INTO `cms_member_integral_category` VALUES (6, '赠送积分', 0);
