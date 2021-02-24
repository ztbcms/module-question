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
