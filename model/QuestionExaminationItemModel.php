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
            ->withAttr('content', function ($value, $data)
            {
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

    /**
     * 获取本题目的正确答案
     * @param $value
     * @param $data
     * @return string
     */
    function getRightKeyAttr($value, $data): string
    {
        $item_id = $data['item_id'] ?? 0;
        $item_type = $data['item_type'] ?? QuestionItemModel::ITEM_TYPE_RADIO;
        if ($item_type == QuestionItemModel::ITEM_TYPE_FILL) {
            //填空题
            $option_values = QuestionItemOptionModel::where('item_id', $item_id)
                ->column('reference_answer');
        } else {
            $option_values = QuestionItemOptionModel::where('item_id', $item_id)
                ->where('option_true', QuestionItemOptionModel::OPTION_TRUE)
                ->column('option_value');
        }
        return implode(',', $option_values);
    }

    /**
     * 获取题目正确率
     * @param $value
     * @param $data
     * @return string
     */
    function getAccuracyAttr($value, $data): string
    {
        $true_count = QuestionExaminationAnswerItemModel::where('item_id', $data['item_id'])
            ->where('is_answer_correct', QuestionExaminationAnswerItemModel::ANSWER_CORRECT_TRUE)
            ->where('status', QuestionExaminationAnswerItemModel::STATUS_CONFIRM)
            ->count();
        $total_count = QuestionExaminationAnswerItemModel::where('item_id', $data['item_id'])
            ->where('status', QuestionExaminationAnswerItemModel::STATUS_CONFIRM)
            ->count();
        if ($total_count > 0) {
            return (sprintf('%0.4f', $true_count / $total_count) * 100).'%';
        } else {
            return "0%";
        }
    }

    function getAnswerCountAttr($value, $data): string
    {
        $true_count = QuestionExaminationAnswerItemModel::where('item_id', $data['item_id'])
            ->where('is_answer_correct', QuestionExaminationAnswerItemModel::ANSWER_CORRECT_TRUE)
            ->where('status', QuestionExaminationAnswerItemModel::STATUS_CONFIRM)
            ->count();
        $total_count = QuestionExaminationAnswerItemModel::where('item_id', $data['item_id'])
            ->where('status', QuestionExaminationAnswerItemModel::STATUS_CONFIRM)
            ->count();
        return $true_count.'/'.$total_count;
    }

}