<?php


namespace app\question\model;


use think\facade\Db;
use think\Model;
use think\model\concern\SoftDelete;

class QuestionExaminationAnswerModel extends Model
{
    use SoftDelete;

    protected $defaultSoftDelete = 0;
    protected $name = 'question_examination_answer';
    protected $pk = 'examination_answer_id';

    const STATUS_CONFIRM = 1;

    /**
     * 记录题目选项
     * @param $examination_answer
     * @param $item_id
     * @param $option_values
     * @throws \Throwable
     */
    static function saveItemAnswer($examination_answer, $item_id, $option_values)
    {
        $option_values = explode(',', $option_values);
        //获取题目所有选项
        $item = QuestionItemModel::where('item_id', $item_id)->findOrEmpty();
        throw_if($item->isEmpty(), new \Exception('找不到该题目'));
        $examination_answer_id = $examination_answer->examination_answer_id;

        Db::startTrans();
        QuestionExaminationAnswerItemModel::destroy(function ($query) use ($examination_answer_id, $item_id) {
            $query->where('examination_answer_id', $examination_answer_id)->where('item_id', $item_id);
        });
        foreach ($option_values as $index => $option_value) {
            $option_value = trim($option_value);
            $is_fill = 1;
            $is_answer_correct = 0; //是否为正确答案
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
                //查询题目选项是否为正确答案 1为正确
                $option_value_true = QuestionItemOptionModel::where('item_id', $item_id)
                    ->where('option_value', $option_value)
                    ->field('option_true')
                    ->select()
                    ->toArray();
                //当选择题选择正确时赋值
                if ($option_value_true[0]['option_true'] == 1) {
                    $is_answer_correct = 1;
                } else {
                    $is_answer_correct = 0;
                }
                throw_if($option_value_is_exist == 0, new \Exception('选项不存在'));
            } else {
                $is_answer_correct = 1;
            }
            if ($option_value == '') {
                $is_answer_correct = 0;
            }
            $answer_item = new QuestionExaminationAnswerItemModel();
            $answer_item->examination_id = $examination_answer->examination_id;
            $answer_item->examination_answer_id = $examination_answer->examination_answer_id;
            $answer_item->item_id = $item_id;
            $answer_item->option_value = $option_value;
            $answer_item->is_fill = $is_fill;
            $answer_item->fill_number = $is_fill ? $index + 1 : 0;
            $answer_item->is_answer_correct = $is_answer_correct;
            $answer_item->save();
        }
        Db::commit();
    }

    /**
     * 关联问卷标题
     * @return \think\model\relation\HasOne
     */
    function bindExaminationTitle()
    {
        return $this->hasOne(QuestionExaminationModel::class, 'examination_id', 'examination_id')
            ->bind(['title']);
    }

    /**
     * 试卷题目数
     * @param $value
     * @param $data
     * @return int
     */
    function getItemCountAttr($value, $data)
    {
        return QuestionExaminationItemModel::where('examination_id', $data['examination_id'])
            ->count();
    }

    /**
     * 答题正确数 answer_correct getAnswerCorrectAttr
     * @return \think\model\relation\HasOne
     */
    function getAnswerCorrectAttr($value, $data)
    {
        //回答表关联题目表             用户提交的答案表
        $item_data = Db::name('question_examination_answer_item')
            ->alias('eai')
            ->where('eai.examination_answer_id', $data['examination_answer_id'])
            ->join('question_item_option io', 'eai.item_id = io.item_id', 'left')//题目选项表
            ->field('eai.*,io.option_type,io.option_true')
            ->select();
        //计算填空题有几个输入框
        $pick_count = $this::pickCount($value, $data);
        //计算用户提交了几个输入框
        $pick_data = $this::pickData($value, $data);

        //用户提交的试卷答案正确数
        $correct_number = 0;
        $checkbox_data = '';
        foreach ($item_data as $key => $val) {
            //回答是否正确1    题目选项是否为正确答案
            if ($val['is_answer_correct'] == $val['option_true']) {
                //当题目类型为复选框时，判断是否所有选项都对
                if ($val['option_type'] == 1) {
                    if ($val['is_answer_correct'] == $val['option_true'] && $val['is_answer_correct'] == 1) {
                        $checkbox_data .= '1';
                    } else {
                        $checkbox_data .= '0';
                    }
                    //当题目为单选框时
                } else {
                    if ($val['option_type'] == 0) {
                        $correct_number += 1;
                    }
                }
            }
            //多选题如果有一个选项选错了，那这道题就算错的
            $tmparray = explode('0', $checkbox_data);
            if (count($tmparray) < 1) {
                $correct_number += 1;
            }
        }
        if ($pick_count == $pick_data) {
            $correct_number += 1;
        }
        return $correct_number;
    }

    //计算填空题有几个输入框
    static function pickCount($value, $data)
    {
        return Db::name('question_examination_item')
            ->alias('ei')
            ->where('ei.examination_id', $data['examination_id'])
            ->join('question_item_option io', 'ei.item_id = io.item_id', 'left')//题目选项表
            ->where('io.option_type', 2)
            ->count();
    }

    //计算用户提交了几个输入框
    static function pickData($value, $data)
    {
        return Db::name('question_examination_answer_item')
            ->alias('eai')
            ->join('question_item_option io', 'eai.item_id = io.item_id', 'left')//题目选项表
            ->field('count(*)')
            ->where('eai.examination_answer_id', $data['examination_answer_id'])
            ->where('io.option_type', 2)
            ->group('eai.option_value')
            ->count();
    }

}