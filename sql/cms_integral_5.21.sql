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

 Date: 21/05/2019 15:19:41
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cms_integral_mall_exchange_record
-- ----------------------------
DROP TABLE IF EXISTS `cms_integral_mall_exchange_record`;
CREATE TABLE `cms_integral_mall_exchange_record`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `member_id` int(10) NOT NULL COMMENT '会员ID',
  `goods_id` int(10) NOT NULL COMMENT '兑换的商品ID',
  `order_no` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单编号',
  `exchange_number` int(10) NOT NULL COMMENT '兑换商品数量',
  `integral` int(10) NOT NULL COMMENT '兑换所花费积分数量',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态，0:待处理 1:已处理 2:已取消',
  `consignee_name` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '收货人姓名',
  `consignee_phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '收货人电话',
  `consignee_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '收货人地址',
  `courier_company` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '快递公司',
  `courier_number` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '快递单号',
  `order_remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单备注',
  `create_time` int(10) NOT NULL COMMENT '兑换时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '积分商城 - 兑换记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cms_integral_mall_exchange_record
-- ----------------------------
INSERT INTO `cms_integral_mall_exchange_record` VALUES (1, 1, 10, '86024304951', 1, 100, 0, '陈杰', '18588278880', '这是一个测试地址', '圆通速递', '123456', '这是备注信息', 1558320876);
INSERT INTO `cms_integral_mall_exchange_record` VALUES (2, 51, 10, '86024304952', 1, 100, 0, '陈杰', '18588278880', '这是一个测试地址', 'AOL奥通快递', '5555555', '这是备注信息', 1558320876);
INSERT INTO `cms_integral_mall_exchange_record` VALUES (3, 52, 10, '86024304953', 1, 100, 0, '陈杰', '18588278880', '这是一个测试地址', '', '', '这是备注信息', 1558320876);
INSERT INTO `cms_integral_mall_exchange_record` VALUES (4, 54, 10, '86024304954', 1, 100, 0, '陈杰', '18588278880', '这是一个测试地址', '', '', '这是备注信息', 1558320876);
INSERT INTO `cms_integral_mall_exchange_record` VALUES (5, 55, 10, '86024304955', 1, 100, 0, '陈杰', '18588278880', '这是一个测试地址', '', '', '这是备注信息', 1558320876);

-- ----------------------------
-- Table structure for cms_integral_mall_goods
-- ----------------------------
DROP TABLE IF EXISTS `cms_integral_mall_goods`;
CREATE TABLE `cms_integral_mall_goods`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `siteid` int(10) NOT NULL COMMENT '机构ID',
  `goods_name` varchar(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品名称',
  `goods_thumb` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品主图',
  `goods_picture` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '商品图片列表',
  `goods_content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品内容',
  `suitable_age_start` int(10) NOT NULL COMMENT '适合开始年龄',
  `suitable_age_end` int(10) NOT NULL COMMENT '适合结束年龄',
  `integral` int(10) NOT NULL COMMENT '所需积分',
  `goods_number` int(10) NOT NULL COMMENT '商品数量',
  `exchange_number` int(10) NULL DEFAULT 0 COMMENT '已兑换数量',
  `is_display` tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否显示，0:否 1:是',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '积分商城 - 商品表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cms_integral_mall_goods
-- ----------------------------
INSERT INTO `cms_integral_mall_goods` VALUES (1, 8, '测试商品', 'http://www.txy.com/static/images/userimg.jpg', '', '这个是测试内容', 3, 12, 1000, 9999, 0, 1, 1557994409, 0);
INSERT INTO `cms_integral_mall_goods` VALUES (2, 8, '测试商品2', 'http://www.txy.com/static/images/userimg.jpg', '', '这个是测试内容', 3, 12, 1000, 9999, 0, 1, 1557994409, 0);
INSERT INTO `cms_integral_mall_goods` VALUES (3, 8, '测试商品3', 'http://www.txy.com/static/images/userimg.jpg', '', '这个是测试内容', 3, 12, 1000, 9999, 0, 1, 1557994409, 0);
INSERT INTO `cms_integral_mall_goods` VALUES (4, 8, '测试商品4', 'http://www.txy.com/static/images/userimg.jpg', '', '这个是测试内容', 3, 12, 1000, 9999, 0, 1, 1557994409, 0);
INSERT INTO `cms_integral_mall_goods` VALUES (5, 8, '测试商品5', 'http://www.txy.com/static/images/userimg.jpg', '', '这个是测试内容', 3, 12, 1000, 9999, 0, 1, 1557994409, 0);
INSERT INTO `cms_integral_mall_goods` VALUES (6, 8, '测试商品6', 'http://www.txy.com/static/images/userimg.jpg', '', '这个是测试内容', 3, 12, 1000, 9999, 0, 1, 1557994409, 0);
INSERT INTO `cms_integral_mall_goods` VALUES (7, 8, '测试商品7', 'http://www.txy.com/static/images/userimg.jpg', '', '这个是测试内容', 3, 12, 1000, 9999, 0, 1, 1557994409, 0);
INSERT INTO `cms_integral_mall_goods` VALUES (8, 8, '测试商品8', 'http://www.txy.com/static/images/userimg.jpg', '', '这个是测试内容', 3, 12, 1000, 9999, 0, 1, 1557994409, 0);
INSERT INTO `cms_integral_mall_goods` VALUES (9, 8, '积分商城', '/public/uploads/8/admin/2019/05-17/c986b7954c3c1908abc1e329814dc6d4.png', NULL, '', 1, 10, 1111, 9999, 0, 1, 1558065674, 1558074448);
INSERT INTO `cms_integral_mall_goods` VALUES (10, 7, '测试', '/public/uploads/8/admin/2019/05-17/c986b7954c3c1908abc1e329814dc6d4.png', NULL, '&lt;p&gt;测试商品详情&lt;br/&gt;&lt;/p&gt;', 1, 10, 1111, 1, 0, 1, 1558314713, 1558407519);
INSERT INTO `cms_integral_mall_goods` VALUES (11, 7, '测试', '/public/uploads/8/admin/2019/05-17/c986b7954c3c1908abc1e329814dc6d4.png', NULL, '&lt;p&gt;积分商城&lt;br/&gt;&lt;/p&gt;', 1, 10, 100, 1, 0, 1, 1558407945, 0);

-- ----------------------------
-- Table structure for cms_integral_rule_config
-- ----------------------------
DROP TABLE IF EXISTS `cms_integral_rule_config`;
CREATE TABLE `cms_integral_rule_config`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `idsite` int(10) NOT NULL COMMENT '机构ID',
  `is_sign` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否开启签到，0:关闭 1:开启',
  `first_day` int(10) NOT NULL DEFAULT 0 COMMENT '连续签到一天',
  `second_day` int(10) NOT NULL COMMENT '连续签到二天',
  `third_day` int(10) NOT NULL COMMENT '连续签到三天',
  `is_signup` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否开启报名，0:关闭 1:开启',
  `sign_rule` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '签到规则',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '积f分规则配置表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cms_integral_rule_config
-- ----------------------------
INSERT INTO `cms_integral_rule_config` VALUES (3, 8, 0, 5, 10, 15, 0, NULL);
INSERT INTO `cms_integral_rule_config` VALUES (4, 7, 0, 5, 10, 15, 0, NULL);

-- ----------------------------
-- Table structure for cms_member_integral_category
-- ----------------------------
DROP TABLE IF EXISTS `cms_member_integral_category`;
CREATE TABLE `cms_member_integral_category`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '分类名称',
  `display_order` int(10) NOT NULL DEFAULT 0 COMMENT '显示排序',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '会员 - 积分类别表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cms_member_integral_category
-- ----------------------------
INSERT INTO `cms_member_integral_category` VALUES (1, '签到', 0);
INSERT INTO `cms_member_integral_category` VALUES (2, '活动报名', 0);
INSERT INTO `cms_member_integral_category` VALUES (3, '评论', 0);
INSERT INTO `cms_member_integral_category` VALUES (4, '活动退款', 0);
INSERT INTO `cms_member_integral_category` VALUES (5, '积分商城兑换', 0);
INSERT INTO `cms_member_integral_category` VALUES (6, '赠送积分', 0);

-- ----------------------------
-- Table structure for cms_member_integral_record
-- ----------------------------
DROP TABLE IF EXISTS `cms_member_integral_record`;
CREATE TABLE `cms_member_integral_record`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `member_id` int(10) NOT NULL COMMENT '会员ID',
  `category_id` int(10) NOT NULL COMMENT '积分分类ID',
  `integral` int(10) NOT NULL COMMENT '积分',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '会员 - 积分记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cms_member_integral_record
-- ----------------------------
INSERT INTO `cms_member_integral_record` VALUES (1, 1, 1, 123, 1547792094);
INSERT INTO `cms_member_integral_record` VALUES (2, 37, 1, 456, 1550470494);
INSERT INTO `cms_member_integral_record` VALUES (3, 38, 1, 10, 1552889694);
INSERT INTO `cms_member_integral_record` VALUES (4, 39, 2, 10, 1555222494);
INSERT INTO `cms_member_integral_record` VALUES (5, 1, 3, 10, 1555308894);
INSERT INTO `cms_member_integral_record` VALUES (6, 1, 4, 10, 1557801230);
INSERT INTO `cms_member_integral_record` VALUES (7, 51, 5, 10, 1557887630);
INSERT INTO `cms_member_integral_record` VALUES (8, 51, 1, 888, 1526610854);
INSERT INTO `cms_member_integral_record` VALUES (9, 51, 5, 365, 1526610854);
INSERT INTO `cms_member_integral_record` VALUES (10, 51, 1, 999, 1495074854);
INSERT INTO `cms_member_integral_record` VALUES (11, 51, 5, 100, 1495074854);
INSERT INTO `cms_member_integral_record` VALUES (12, 518, 6, 10, 1558421589);
INSERT INTO `cms_member_integral_record` VALUES (13, 515, 6, 100, 1558421673);
INSERT INTO `cms_member_integral_record` VALUES (14, 515, 6, 50, 1558421681);

ALTER TABLE cms_member ADD integral INT(10) NOT NULL DEFAULT 0 COMMENT '积分';

SET FOREIGN_KEY_CHECKS = 1;
