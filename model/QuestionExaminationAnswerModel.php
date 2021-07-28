<?php


namespace app\question\model;


use think\facade\Db;
use think\Model;
use think\model\concern\SoftDelete;
use think\model\relation\HasOne;

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
        //获取题目所有选项
        $item = QuestionItemModel::where('item_id', $item_id)
            ->where('item_kind', QuestionItemModel::ITEM_KIND_QUESTION)
            ->findOrEmpty();
        throw_if($item->isEmpty(), new \Exception('找不到该题目'));

        $option_values = explode(',', $option_values);
        $examination_answer_id = $examination_answer->examination_answer_id;
        Db::startTrans();
        QuestionExaminationAnswerItemModel::destroy(function ($query) use ($examination_answer_id, $item_id)
        {
            $query->where('examination_answer_id', $examination_answer_id)->where('item_id', $item_id);
        });
        foreach ($option_values as $index => $option_value) {
            $option_value = trim($option_value);
            $is_fill = QuestionExaminationAnswerItemModel::ANSWER_CORRECT_TRUE;
            $is_answer_correct = QuestionExaminationAnswerItemModel::ANSWER_CORRECT_FALSE; //是否为正确答案
            if ($item->item_type != QuestionItemModel::ITEM_TYPE_FILL) {
                $is_fill = 0;
                //检查是否有该选项
                $option = QuestionItemOptionModel::where('item_id', $item_id)
                    ->where('option_value', trim($option_value))
                    ->findOrEmpty();
                if (!$option->isEmpty() && $option->option_true == QuestionItemOptionModel::OPTION_TRUE) {
                    $is_answer_correct = 1;
                }
            } else {
                //填空题，不为空则正确
                $is_answer_correct = $option_value != '' ? QuestionExaminationAnswerItemModel::ANSWER_CORRECT_TRUE : QuestionExaminationAnswerItemModel::ANSWER_CORRECT_FALSE;
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
     * @return HasOne
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
    function getItemCountAttr($value, $data): int
    {
        $examination_answer_id = $data['examination_answer_id'] ?? 0;
        //获取按题目分组所有答题总数
        return QuestionExaminationAnswerItemModel::where('examination_answer_id', $examination_answer_id)
            ->group('item_id')->count();
    }

    /**
     * 答题正确数
     * @param $value
     * @param $data
     * @return int
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\db\exception\DataNotFoundException
     */
    function getAnswerCorrectAttr($value, $data): int
    {
        $examination_answer_id = $data['examination_answer_id'] ?? 0;
        //获取按题目分组所有答题总数
        $total_count = QuestionExaminationAnswerItemModel::where('examination_answer_id', $examination_answer_id)
            ->group('item_id')->count();

        //获取按题目分组所有答错
        $error_count = QuestionExaminationAnswerItemModel::where('examination_answer_id', $examination_answer_id)
            ->where('is_answer_correct', QuestionExaminationAnswerItemModel::ANSWER_CORRECT_FALSE)
            ->group('item_id')->count();
        return ($total_count - $error_count) >= 0 ? $total_count - $error_count : 0;
    }

    /**
     * 获取正确数和答题数比例
     * @return string
     */
    function getProportionAttr($value, $data): string
    {
        $examination_answer_id = $data['examination_answer_id'] ?? 0;
        //获取按题目分组所有答题总数
        $total_count = QuestionExaminationAnswerResultModel::where('examination_answer_id',
            $examination_answer_id)
            ->count();

        //获取按题目分组所有答错
        $true_count = QuestionExaminationAnswerResultModel::where('examination_answer_id',
            $examination_answer_id)
            ->where('is_answer_correct', QuestionExaminationAnswerItemModel::ANSWER_CORRECT_TRUE)->count();
        return $true_count.'/'.$total_count;
    }

    function getConfirmTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }
}