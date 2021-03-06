<?php


namespace app\question\model;

use think\db\concern\WhereQuery;
use think\Model;
use think\model\concern\SoftDelete;

class QuestionExaminationAnswerItemModel extends Model
{

    use SoftDelete;

    protected $defaultSoftDelete = 0;
    protected $name = 'question_examination_answer_item';
    protected $pk = 'examination_answer_item_id';

    const STATUS_CONFIRM = 1;
    const STATUS_UN_CONFIRM= 0;
    const ANSWER_CORRECT_TRUE = 1;
    const ANSWER_CORRECT_FALSE = 0;

    /**
     * 用户提交的选项答案
     * @param $query
     * @param $examination_id
     * @param $item_id
     */
    function scopeAnalysis($query, $examination_id, $item_id)
    {
        $query->where('examination_id',
            $examination_id)
            ->where('status', QuestionExaminationAnswerModel::STATUS_CONFIRM)
            ->where('item_id', $item_id)->field('count("examination_answer_item_id") as count,option_value')
            ->group('option_value');
    }


}