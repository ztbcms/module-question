<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2021/2/24
 * Time: 14:41.
 */

namespace app\question\model;


use think\facade\Db;
use think\Model;
use think\model\concern\SoftDelete;

class QuestionQuestionnaireAnswerModel extends Model
{
    use SoftDelete;

    protected $defaultSoftDelete = 0;
    protected $name = 'question_questionnaire_answer';
    protected $pk = 'questionnaire_answer_id';

    const STATUS_CONFIRM = 1;
    const STATUS_UNCONFIRMED = 0;

    /**
     * 记录题目选项
     * @param $questionnaire_answer
     * @param $item_id
     * @param $option_values
     * @throws \Throwable
     */
    static function saveItemAnswer($questionnaire_answer, $item_id, $option_values)
    {
        $option_values = explode(',', $option_values);
        //获取题目所有选项
        $item = QuestionItemModel::where('item_id', $item_id)->findOrEmpty();
        throw_if($item->isEmpty(), new \Exception('找不到该题目'));
        $questionnaire_answer_id = $questionnaire_answer->questionnaire_answer_id;

        Db::startTrans();
        QuestionQuestionnaireAnswerItemModel::destroy(function ($query) use ($questionnaire_answer_id, $item_id)
        {
            $query->where('questionnaire_answer_id', $questionnaire_answer_id)->where('item_id', $item_id);
        });
        foreach ($option_values as $index => $option_value) {
            $option_value = trim($option_value);
            $is_fill = 1;
            if ($item->item_type != QuestionItemModel::ITEM_TYPE_FILL) {
                $is_fill = 0;
                // 单选题提交多个答案
                throw_if($item->item_type == QuestionItemModel::ITEM_TYPE_RADIO && count($option_values) > 1,
                    new \Exception('单选题仅允许选择一个选项'));
                if ($option_value == '') {
                    continue;
                }
                //检查是否有该选项
                $option_value_is_exist = QuestionItemOptionModel::where('item_id', $item_id)
                    ->where('option_value', $option_value)
                    ->count();
                throw_if($option_value_is_exist == 0, new \Exception('选项不存在'));
            }
            $answer_item = new QuestionQuestionnaireAnswerItemModel();
            $answer_item->questionnaire_id = $questionnaire_answer->questionnaire_id;
            $answer_item->questionnaire_answer_id = $questionnaire_answer->questionnaire_answer_id;
            $answer_item->item_id = $item_id;
            $answer_item->option_value = $option_value;
            $answer_item->is_fill = $is_fill;
            $answer_item->fill_number = $is_fill ? $index + 1 : 0;
            $answer_item->save();
        }
        Db::commit();
    }

    function getConfirmTimeAttr($value)
    {
        if ($value == 0) {
            return "未提交";
        } else {
            return date("Y-m-d H:i:s", $value);
        }
    }

    /**
     * 关联问卷标题
     * @return \think\model\relation\HasOne
     */
    function bindQuestionnaireTitle()
    {
        return $this->hasOne(QuestionQuestionnaireModel::class, 'questionnaire_id', 'questionnaire_id')
            ->bind(['title']);
    }
}