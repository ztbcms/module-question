<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2021/2/24
 * Time: 14:41.
 */

namespace app\question\model;


use think\Model;

class QuestionQuestionnaireItemModel extends Model
{
    protected $name = 'question_questionnaire_item';
    protected $pk = 'id';

    protected $updateTime = false;
    protected $createTime = false;

    public function bindItem()
    {
        return $this->hasOne(QuestionItemModel::class, 'item_id', 'item_id')
            ->withAttr('content', function ($value, $data)
            {
                return '#'.$data['item_id'].' '.$value;
            })
            ->append(['item_type_text'])
            ->bind(['content', 'item_type', 'item_type_text']);
    }

    public function bindApiItem()
    {
        return $this->hasOne(QuestionItemModel::class, 'item_id', 'item_id')
            ->with(['item_options'])
            ->append(['item_type_text'])
            ->bind(['content', 'item_type', 'item_type_text', 'item_options']);
    }
}