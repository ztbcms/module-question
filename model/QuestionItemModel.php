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

class QuestionItemModel extends Model
{
    use SoftDelete;

    protected $defaultSoftDelete = 0;
    protected $name = 'question_item';
    protected $pk = 'item_id';

    const ITEM_KIND_QUESTIONNAIRE = 0;
    const ITEM_KIND_QUESTION = 1;

    const ITEM_TYPE_RADIO = 0;
    const ITEM_TYPE_CHECK = 1;
    const ITEM_TYPE_FILL = 2;

    /**
     * 保存项目选项
     * @param $question_item
     * @param $options
     * @return \think\Collection
     * @throws \Exception
     */
    static function saveOptions($question_item, $options)
    {
        $item_id = $question_item->item_id;
        //删除已有的选项
        if ($item_id) {
            QuestionItemOptionModel::destroy(function ($query) use ($item_id)
            {
                $query->where('item_id', $item_id);
            });
        }
        //增加新的选项
        $saveData = [];

        foreach ($options as $option) {
            $saveData[] = [
                'item_id'          => $item_id,
                'option_value'     => $option['option_value'] ?? '',
                'option_img'       => $option['option_img'] ?? '',
                'option_fill_type' => $option['option_fill_type'] ?? '',
                'option_true' => $option['option_true'] == true ?'1':'',
                'reference_answer' => $option['reference_answer'] ?? '',
            ];
        }
        $question_item_option = new QuestionItemOptionModel();
        return $question_item_option->saveAll($saveData);
    }

    public function itemOptions()
    {
        return $this->hasMany(QuestionItemOptionModel::class, 'item_id', 'item_id')
            ->field(['item_id', 'option_value', 'option_img', 'option_fill_type']);
    }

    function getItemKindTextAttr($value, $data)
    {
        if ($data['item_kind'] == self::ITEM_KIND_QUESTIONNAIRE) {
            return "问卷";
        }
        if ($data['item_kind'] == self::ITEM_KIND_QUESTION) {
            return "试卷";
        }
    }

    function getItemTypeTextAttr($value, $data)
    {
        if ($data['item_type'] == self::ITEM_TYPE_RADIO) {
            return "单选题";
        }
        if ($data['item_type'] == self::ITEM_TYPE_CHECK) {
            return "多选题";
        }
        if ($data['item_type'] == self::ITEM_TYPE_FILL) {
            return "填空题";
        }

        return $value;
    }

    static function checkDelete($item_id)
    {
        if (QuestionQuestionnaireItemModel::where('item_id', $item_id)->count() > 0) {
            return false;
        }
        return true;
    }
}