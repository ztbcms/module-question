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

class QuestionQuestionnaireModel extends Model
{
    use SoftDelete;

    protected $defaultSoftDelete = 0;
    protected $name = 'question_questionnaire';
    protected $pk = 'questionnaire_id';


    /**
     * 保存关联题目
     * @param $questionnaire
     * @param $item_ids
     * @return \think\Collection
     * @throws \Exception
     */
    static function saveQuestionnaireItems($questionnaire, $item_ids)
    {
        $questionnaire_id = $questionnaire->questionnaire_id;
        //删除已有的选项
        if ($questionnaire_id) {
            QuestionQuestionnaireItemModel::destroy(function ($query) use ($questionnaire_id)
            {
                $query->where('questionnaire_id', $questionnaire_id);
            });
        }
        //增加新的选项
        $saveData = [];
        foreach ($item_ids as $item_id) {
            $saveData[] = [
                'questionnaire_id' => $questionnaire_id,
                'item_id'          => $item_id ?? 0,
            ];
        }
        $questionnaire_item = new QuestionQuestionnaireItemModel();
        return $questionnaire_item->saveAll($saveData);
    }

    function itemList()
    {
        return $this->hasMany(QuestionQuestionnaireItemModel::class, 'questionnaire_id', 'questionnaire_id')
            ->with('bind_item')->hidden(['questionnaire_id', 'id']);
    }

    function getItemCountAttr($value, $data)
    {
        return QuestionQuestionnaireItemModel::where('questionnaire_id', $data['questionnaire_id'])
            ->count();
    }

    function getSubmitCountAttr()
    {
        return 0;
    }
}