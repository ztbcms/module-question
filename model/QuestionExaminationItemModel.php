<?php


namespace app\question\model;

use think\Model;

class QuestionExaminationItemModel extends Model
{
    protected $name = 'question_examination_item';
    protected $pk = 'id';

    protected $updateTime = false;
    protected $createTime = false;

    /**
     * 试卷下每个题目的列表
     * @return \think\model\relation\HasOne
     */
    public function bindItem()
    {
        return $this->hasOne(QuestionItemModel::class, 'item_id', 'item_id')
            ->withAttr('content', function ($value, $data) {
                return '#'.$data['item_id'].' '.$value;
            })
            ->append(['item_type_text'])
            ->bind(['content', 'item_type', 'item_type_text']);
    }

    /**
     * 试卷下每个题目的列表
     * @return \think\model\relation\HasOne
     */
    public function bindApiItem()
    {
        return $this->hasOne(QuestionItemModel::class, 'item_id', 'item_id')
            ->with(['item_options'])
            ->append(['item_type_text'])
            ->bind(['content', 'item_type', 'item_type_text', 'item_options']);
    }

}