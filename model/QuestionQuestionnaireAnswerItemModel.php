<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2021/2/24
 * Time: 14:41.
 */

namespace app\question\model;


use think\Model;
use think\model\concern\SoftDelete;

class QuestionQuestionnaireAnswerItemModel extends Model
{
    use SoftDelete;

    protected $defaultSoftDelete = 0;
    protected $name = 'question_questionnaire_answer_item';
    protected $pk = 'questionnaire_answer_item_id';


    function scopeAnalysis($query, $questionnaire_id, $item_id)
    {
        $query->where('questionnaire_id',
            $questionnaire_id)
            ->where('status', QuestionQuestionnaireAnswerModel::STATUS_CONFIRM)
            ->where('item_id', $item_id)->field('count("questionnaire_answer_item_id") as count,option_value')
            ->group('option_value');
    }
}