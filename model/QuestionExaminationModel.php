<?php


namespace app\question\model;


use think\Model;
use think\model\concern\SoftDelete;

class QuestionExaminationModel extends Model
{
    protected $name = 'question_examination';
    protected $pk = 'examination_id';

    /**
     * 保存关联题目
     * @param $examination
     * @param $item_ids
     * @return \think\Collection
     * @throws \Exception
     */
    static function saveExaminationItems($examination, $item_ids)
    {
        $examination_id = $examination->examination_id;
        //删除已有的选项
        if ($examination_id) {
            QuestionExaminationItemModel::destroy(function ($query) use ($examination_id)
            {
                $query->where('examination_id', $examination_id);
            });
        }
        //增加新的选项
        $saveData = [];
        foreach ($item_ids as $number => $item_id) {
            $saveData[] = [
                'examination_id' => $examination_id,
                'item_id'          => $item_id ?? 0,
                'number'           => $number + 1
            ];
        }
        $examination_item = new QuestionExaminationItemModel();
        return $examination_item->saveAll($saveData);
    }
    function itemList()
    {
        return $this->hasMany(QuestionExaminationItemModel::class, 'examination_id', 'examination_id')
            ->order('number', 'ASC')
            ->with('bind_item')->hidden(['examination_id', 'id']);
    }


    function getItemCountAttr($value, $data)
    {
        return QuestionExaminationItemModel::where('examination_id', $data['examination_id'])
            ->count();
    }

    function getSubmitCountAttr($value, $data)
    {
        return QuestionExaminationAnswerModel::where('examination_id', $data['examination_id'])
            ->where('status', QuestionExaminationAnswerModel::STATUS_CONFIRM)
            ->count();
    }
}