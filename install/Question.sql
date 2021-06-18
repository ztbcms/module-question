/*
 Navicat Premium Data Transfer

 Source Server         : mysql
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : ztbcms

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 18/06/2021 17:47:07
*/

SET NAMES utf8mb4;
SET
FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cms_question_examination
-- ----------------------------
DROP TABLE IF EXISTS `cms_question_examination`;
CREATE TABLE `cms_question_examination`
(
    `examination_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`          varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '问卷标题',
    `description`    varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '问卷介绍',
    `number`         int(2) NULL DEFAULT NULL COMMENT '答题数量（1：全部，2部分）',
    `part_number`    int(11) NULL DEFAULT NULL COMMENT '部分数量',
    `create_time`    int(11) NULL DEFAULT 0 COMMENT '创建时间',
    `update_time`    int(11) NULL DEFAULT 0 COMMENT '更新时间',
    `delete_time`    int(11) NULL DEFAULT 0 COMMENT '删除时间',
    PRIMARY KEY (`examination_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_question_examination_answer
-- ----------------------------
DROP TABLE IF EXISTS `cms_question_examination_answer`;
CREATE TABLE `cms_question_examination_answer`
(
    `examination_answer_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `examination_id`        int(11) NULL DEFAULT NULL,
    `target`                varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '回答来源，例如用户的id',
    `target_type`           varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '回答来源标识，例如 user_id',
    `status`                tinyint(1) NULL DEFAULT 0 COMMENT '状态 0 未提交、1已提交',
    `confirm_time`          int(11) NULL DEFAULT 0 COMMENT '回答结束时间',
    `create_time`           int(11) NULL DEFAULT 0 COMMENT '创建时间',
    `update_time`           int(11) NULL DEFAULT 0 COMMENT '更新时间',
    `delete_time`           int(11) NULL DEFAULT 0 COMMENT '删除时间',
    PRIMARY KEY (`examination_answer_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '提交答案主表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_question_examination_answer_item
-- ----------------------------
DROP TABLE IF EXISTS `cms_question_examination_answer_item`;
CREATE TABLE `cms_question_examination_answer_item`
(
    `examination_answer_item_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `examination_answer_id`      int(11) NULL DEFAULT NULL,
    `examination_id`             int(11) NULL DEFAULT NULL,
    `item_id`                    int(11) NULL DEFAULT NULL COMMENT '题目id',
    `option_value`               varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '回答选项值',
    `is_fill`                    tinyint(1) NULL DEFAULT 0 COMMENT '是否填空题',
    `fill_number`                int(11) NULL DEFAULT 0 COMMENT '填空项码',
    `status`                     tinyint(1) NULL DEFAULT 0 COMMENT '是否提交，不提交不做统计',
    `is_answer_correct`          int(2) NULL DEFAULT NULL COMMENT '选择题回答是否正确(0不正确，1正确)',
    `create_time`                int(11) NULL DEFAULT 0 COMMENT '创建时间',
    `update_time`                int(11) NULL DEFAULT 0 COMMENT '更新时间',
    `delete_time`                int(11) NULL DEFAULT 0 COMMENT '删除时间',
    PRIMARY KEY (`examination_answer_item_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 59 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_question_examination_item
-- ----------------------------
DROP TABLE IF EXISTS `cms_question_examination_item`;
CREATE TABLE `cms_question_examination_item`
(
    `id`             int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `examination_id` int(11) NULL DEFAULT 0 COMMENT '关联问卷id',
    `item_id`        int(11) NULL DEFAULT 0 COMMENT '关联题目id',
    `number`         int(11) NULL DEFAULT 0 COMMENT '题号',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 57 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_question_item
-- ----------------------------
DROP TABLE IF EXISTS `cms_question_item`;
CREATE TABLE `cms_question_item`
(
    `item_id`     int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `content`     varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '题目内容',
    `item_kind`   tinyint(4) NULL DEFAULT 0 COMMENT '题目种类，默认是0问卷、1试题',
    `item_type`   tinyint(4) NULL DEFAULT 0 COMMENT '题目类型：0单选、1多选、2填空',
    `create_time` int(11) NULL DEFAULT 0 COMMENT '添加时间',
    `update_time` int(11) NULL DEFAULT 0 COMMENT '更新时间',
    `delete_time` int(11) NULL DEFAULT 0 COMMENT '删除时间',
    PRIMARY KEY (`item_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 58 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cmsb_question_item_option
-- ----------------------------
DROP TABLE IF EXISTS `cms_question_item_option`;
CREATE TABLE `cms_question_item_option`
(
    `item_option_id`   int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `item_id`          int(11) NULL DEFAULT 0 COMMENT '所属题目选项',
    `option_value`     varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '选项值',
    `option_img`       varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '选项关联的图片',
    `option_fill_type` tinyint(4) NULL DEFAULT 0 COMMENT '填空选项类型0文本，1数值',
    `option_true`      int(2) NULL DEFAULT NULL COMMENT '是否为正确答案（1是，0不是）',
    `reference_answer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '参考答案',
    `option_type`      int(2) NULL DEFAULT NULL COMMENT '选项类型',
    `create_time`      int(11) NULL DEFAULT 0 COMMENT '创建时间',
    `delete_time`      int(11) NULL DEFAULT 0 COMMENT '删除时间',
    PRIMARY KEY (`item_option_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 50 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_question_questionnaire
-- ----------------------------
DROP TABLE IF EXISTS `cms_question_questionnaire`;
CREATE TABLE `cms_question_questionnaire`
(
    `questionnaire_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`            varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '问卷标题',
    `description`      varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '问卷介绍',
    `create_time`      int(11) NULL DEFAULT 0 COMMENT '创建时间',
    `update_time`      int(11) NULL DEFAULT 0 COMMENT '更新时间',
    `delete_time`      int(11) NULL DEFAULT 0 COMMENT '删除时间',
    PRIMARY KEY (`questionnaire_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_question_questionnaire_answer
-- ----------------------------
DROP TABLE IF EXISTS `cms_question_questionnaire_answer`;
CREATE TABLE `cms_question_questionnaire_answer`
(
    `questionnaire_answer_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `questionnaire_id`        int(11) NULL DEFAULT NULL,
    `target`                  varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '回答来源，例如用户的id',
    `target_type`             varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '回答来源标识，例如 user_id',
    `status`                  tinyint(1) NULL DEFAULT 0 COMMENT '状态 0 未提交、1已提交',
    `confirm_time`            int(11) NULL DEFAULT 0 COMMENT '回答结束时间',
    `create_time`             int(11) NULL DEFAULT 0 COMMENT '创建时间',
    `update_time`             int(11) NULL DEFAULT 0 COMMENT '更新时间',
    `delete_time`             int(11) NULL DEFAULT 0 COMMENT '删除时间',
    PRIMARY KEY (`questionnaire_answer_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_question_questionnaire_answer_item
-- ----------------------------
DROP TABLE IF EXISTS `cms_question_questionnaire_answer_item`;
CREATE TABLE `cms_question_questionnaire_answer_item`
(
    `questionnaire_answer_item_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `questionnaire_answer_id`      int(11) NULL DEFAULT NULL,
    `questionnaire_id`             int(11) NULL DEFAULT NULL,
    `item_id`                      int(11) NULL DEFAULT NULL COMMENT '题目id',
    `option_value`                 varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '回答选项值',
    `is_fill`                      tinyint(1) NULL DEFAULT 0 COMMENT '是否填空题',
    `fill_number`                  int(11) NULL DEFAULT 0 COMMENT '填空项码',
    `status`                       tinyint(1) NULL DEFAULT 0 COMMENT '是否提交，不提交不做统计',
    `create_time`                  int(11) NULL DEFAULT 0 COMMENT '创建时间',
    `update_time`                  int(11) NULL DEFAULT 0 COMMENT '更新时间',
    `delete_time`                  int(11) NULL DEFAULT 0 COMMENT '删除时间',
    PRIMARY KEY (`questionnaire_answer_item_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cms_question_questionnaire_item
-- ----------------------------
DROP TABLE IF EXISTS `cms_question_questionnaire_item`;
CREATE TABLE `cms_question_questionnaire_item`
(
    `id`               int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `questionnaire_id` int(11) NULL DEFAULT 0 COMMENT '关联问卷id',
    `item_id`          int(11) NULL DEFAULT 0 COMMENT '关联题目id',
    `number`           int(11) NULL DEFAULT 0 COMMENT '题号',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

SET
FOREIGN_KEY_CHECKS = 1;
