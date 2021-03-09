-- ----------------------------
-- 题目表
-- ----------------------------
DROP TABLE IF EXISTS `cms_question_item`;
CREATE TABLE `cms_question_item` (
  `item_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(2048) DEFAULT '' COMMENT '题目内容',
  `item_kind` tinyint(4) DEFAULT '0' COMMENT '题目种类，默认是0问卷、1试题',
  `item_type` tinyint(4) DEFAULT '0' COMMENT '题目类型：0单选、1多选、2填空',
  `create_time` int(11) DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(11) DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- 题目表-选项
-- ----------------------------
DROP TABLE IF EXISTS `cms_question_item_option`;
CREATE TABLE `cms_question_item_option` (
  `item_option_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT '0' COMMENT '所属题目选项',
  `option_value` varchar(1024) DEFAULT '' COMMENT '选项值',
  `option_img` varchar(1024) DEFAULT '' COMMENT '选项关联的图片',
  `option_fill_type` tinyint(4) DEFAULT '0' COMMENT '填空选项类型0文本，1数值',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `delete_time` int(11) DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`item_option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ----------------------------
-- 问卷表
-- ----------------------------
DROP TABLE IF EXISTS `cms_question_questionnaire`;
CREATE TABLE `cms_question_questionnaire` (
  `questionnaire_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(256) DEFAULT '' COMMENT '问卷标题',
  `description` varchar(2048) DEFAULT '' COMMENT '问卷介绍',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(11) DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`questionnaire_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ----------------------------
-- 问卷表-题目
-- ----------------------------
DROP TABLE IF EXISTS `cms_question_questionnaire_item`;
CREATE TABLE `cms_question_questionnaire_item` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `questionnaire_id` int(11) DEFAULT '0' COMMENT '关联问卷id',
  `item_id` int(11) DEFAULT '0' COMMENT '关联题目id',
  `number` int(11) DEFAULT '0' COMMENT '题号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ----------------------------
-- 问卷表-回答
-- ----------------------------
DROP TABLE IF EXISTS `cms_question_questionnaire_answer`;
CREATE TABLE `cms_question_questionnaire_answer` (
  `questionnaire_answer_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `questionnaire_id` int(11) DEFAULT NULL,
  `target` varchar(32) DEFAULT '' COMMENT '回答来源，例如用户的id',
  `target_type` varchar(32) DEFAULT '' COMMENT '回答来源标识，例如 user_id',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态 0 未提交、1已提交',
  `confirm_time` int(11) DEFAULT '0' COMMENT '回答结束时间',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(11) DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`questionnaire_answer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ----------------------------
-- 问卷表-回答题目
-- ----------------------------
DROP TABLE IF EXISTS `cms_question_questionnaire_answer_item`;
CREATE TABLE `cms_question_questionnaire_answer_item` (
  `questionnaire_answer_item_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `questionnaire_answer_id` int(11) DEFAULT NULL,
  `questionnaire_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL COMMENT '题目id',
  `option_value` varchar(1024) DEFAULT '' COMMENT '回答选项值',
  `is_fill` tinyint(1) DEFAULT '0' COMMENT '是否填空题',
  `fill_number` int(11) DEFAULT '0' COMMENT '填空项码',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(11) DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`questionnaire_answer_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


