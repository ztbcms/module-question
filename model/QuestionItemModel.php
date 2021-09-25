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
use think\facade\Db;
use app\common\service\BaseService;

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
            if (!empty($option)) {
                foreach ($option as $data) {
                    if ($data['option_true'] === 'true' || $data['option_true'] == '1') {
                        $data['option_true'] = 1;
                    } else {
                        $data['option_true'] = 0;
                    }
                    $saveData[] = [
                        'item_id'          => $item_id,
                        'option_value'     => $data['option_value'] ?? '',
                        'option_img'       => $data['option_img'] ?? '',
                        'option_fill_type' => $data['option_fill_type'] ?? '',
                        'option_type'      => $data['option_type'] ?? '',
                        'option_true'      => $data['option_true'] ?? '0',
                        'reference_answer' => $data['reference_answer'] ?? '',
                    ];
                }
            }

        }
        $question_item_option = new QuestionItemOptionModel();
        return $question_item_option->saveAll($saveData);
    }

    /**
     * 项目选项
     * @return \think\model\relation\HasMany
     */
    public function itemOptions()
    {
        return $this->hasMany(QuestionItemOptionModel::class, 'item_id', 'item_id')
            ->field(['item_id', 'option_value', 'option_img', 'option_fill_type']);
    }

    /**
     * 判断类型是问卷/试卷
     * @param $value
     * @param $data
     * @return string
     */
    function getItemKindTextAttr($value, $data)
    {
        if ($data['item_kind'] == self::ITEM_KIND_QUESTIONNAIRE) {
            return "问卷";
        }
        if ($data['item_kind'] == self::ITEM_KIND_QUESTION) {
            return "试卷";
        }
    }

    /**
     * 分析题目类型
     * @param $value
     * @param $data
     * @return string
     */
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

    /**
     * 判断是否有问卷或答题用到这一题目
     * @param $item_id
     * @return bool
     */
    static function checkDelete($item_id)
    {
        if (QuestionQuestionnaireItemModel::where('item_id', $item_id)->count() > 0) {
            return false;
        }
        return true;
    }

    /**
     * 获取题目详情
     * @param $item_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    static function getDetails($item_id): array
    {
        $question_item = QuestionItemModel::where('item_id', $item_id)
            ->field(['item_id', 'item_kind', 'item_type', 'content'])
            ->findOrEmpty()->toArray();
        $item_options = QuestionItemOptionModel::where('item_id', $item_id)->select()->toArray();
        $question_item['item_kind'] = (string) $question_item['item_kind'] ?? 0;
        $question_item['item_type'] = (string) $question_item['item_type'] ?? 0;

        $radio_data = [];
        $checkbox_data = [];
        $pack = [];
        foreach ($item_options as $option) {
            if ($option['option_type'] == '0') {
                $radio_data[] = $option;
            } else {
                if ($option['option_type'] == '1') {
                    $checkbox_data[] = $option;
                } else {
                    $pack[] = $option;
                }
            }
        }
        return [
            'form' => $question_item, 'radio_data' => $radio_data, 'checkbox_data' => $checkbox_data, 'pack' => $pack
        ];
    }
}