<?php


namespace app\question\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\model\relation\HasOne;

class QuestionExaminationAnswerResultModel extends Model
{

    use SoftDelete;

    protected $defaultSoftDelete = 0;
    protected $name = 'question_examination_answer_result';
    protected $pk = 'examination_answer_result_id';

    const STATUS_NOT_ANSWER = 0;
    const STATUS_ANSWERED = 1;
    //填空题未改判
    const STATUS_NOT_CONFIRM = 2;

    const ANSWER_CORRECT_TRUE = 1;
    const ANSWER_CORRECT_FALSE = 0;

    public function item(): HasOne
    {
        return $this->hasOne(QuestionItemModel::class, 'item_id', 'item_id');
    }

    //评判试卷
    static function gradeExam($examination_answer_id)
    {
        //找出回答结果
        $examination_answer = QuestionExaminationAnswerModel::where('examination_answer_id',
            $examination_answer_id)->findOrEmpty();
        //找出试卷所有题目
        $item_ids = QuestionExaminationItemModel::where('examination_id',
            $examination_answer->examination_id)->column('score', 'item_id');
        foreach ($item_ids as $item_id => $score) {
            //查看该题目
            $item = QuestionItemModel::where('item_id', $item_id)
                ->findOrEmpty();

            $result_item = self::where('examination_answer_id', $examination_answer_id)->where('item_id',
                $item_id)->findOrEmpty();
            $result_item->examination_answer_id = $examination_answer_id;
            $result_item->examination_id = $examination_answer->examination_id;
            $result_item->item_id = $item_id;
            //单选
            if ($item->item_type === QuestionItemModel::ITEM_TYPE_RADIO) {
                self::gradeRadio($result_item, $examination_answer_id, $item_id, $score);
            }
            //多选
            if ($item->item_type === QuestionItemModel::ITEM_TYPE_CHECK) {
                self::gradeCheck($result_item, $examination_answer_id, $item_id, $score);
            }
            //填空
            if ($item->item_type === QuestionItemModel::ITEM_TYPE_FILL) {
                $option_values = QuestionExaminationAnswerItemModel::where('examination_answer_id',
                    $examination_answer_id)
                    ->where('item_id', $item_id)->column('option_value');

                $result_item->option_values = implode(',', $option_values);
                $result_item->status = self::STATUS_NOT_CONFIRM;
                $result_item->is_answer_correct = QuestionExaminationAnswerItemModel::ANSWER_CORRECT_TRUE;
                $result_item->score = 0;
                $result_item->save();
            }
        }
    }

    /**
     * 评判多选题
     * @param  Model  $result_item
     * @param  int  $examination_answer_id
     * @param  int  $item_id
     * @param  int  $score
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    static function gradeCheck(Model $result_item, int $examination_answer_id, int $item_id, int $score)
    {
        //回答记录
        $answer_items = QuestionExaminationAnswerItemModel::where('examination_answer_id',
            $examination_answer_id)
            ->where('item_id', $item_id)->select();
        //获取正确答案的个数
        $true_count = QuestionItemOptionModel::where('item_id', $item_id)->where('option_true',
            QuestionItemOptionModel::OPTION_TRUE)->count();
        $is_answer_correct = QuestionExaminationAnswerItemModel::ANSWER_CORRECT_TRUE;
        if ($true_count != count($answer_items)) {
            //回答数量不等于正确答案的数量，都是错误
            $is_answer_correct = QuestionExaminationAnswerItemModel::ANSWER_CORRECT_TRUE;
        }
        //回答记录
        $option_values = [];
        //有一个答案错误，就是错误
        foreach ($answer_items as $answer_item) {
            if ($answer_item->is_answer_correct == QuestionExaminationAnswerItemModel::ANSWER_CORRECT_FALSE) {
                $is_answer_correct = QuestionExaminationAnswerItemModel::ANSWER_CORRECT_FALSE;
            }
            $option_values[] = $answer_item->option_value;
        }

        $result_item->option_values = implode(',', $option_values);
        $result_item->status = $answer_items->isEmpty() ? self::STATUS_NOT_ANSWER : self::STATUS_ANSWERED;
        $result_item->is_answer_correct = $is_answer_correct;
        $result_item->score = $is_answer_correct == QuestionExaminationAnswerItemModel::ANSWER_CORRECT_TRUE ? $score : 0;
        $result_item->save();
    }

    /**
     * 评判单选题
     * @param  Model  $result_item
     * @param  int  $examination_answer_id
     * @param  int  $item_id
     * @param  int  $score
     */
    static function gradeRadio(Model $result_item, int $examination_answer_id, int $item_id, int $score)
    {
        //拿到答题记录
        $answer_item = QuestionExaminationAnswerItemModel::where('examination_answer_id',
            $examination_answer_id)
            ->where('item_id', $item_id)->findOrEmpty();

        $result_item->score = 0;
        if ($answer_item->isEmpty()) {
            //没有作答
            $result_item->option_values = "";
            $result_item->status = self::STATUS_NOT_ANSWER;
            $result_item->is_answer_correct = self::ANSWER_CORRECT_FALSE;
        } else {
            $result_item->option_values = $answer_item->option_value;
            $result_item->status = self::STATUS_ANSWERED;
            if ($answer_item->is_answer_correct == QuestionExaminationAnswerItemModel::ANSWER_CORRECT_TRUE) {
                //回答正确
                $result_item->is_answer_correct = self::ANSWER_CORRECT_TRUE;
                $result_item->score = $score;
            } else {
                $result_item->is_answer_correct = self::ANSWER_CORRECT_FALSE;
            }
        }
        $result_item->save();
    }
}