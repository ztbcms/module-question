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
            QuestionItemOptionModel::destroy(function ($query) use ($item_id) {
                $query->where('item_id', $item_id);
            });
        }
        //增加新的选项
        $saveData = [];
        foreach ($options as $option) {
            foreach($option as $data){
                if ($data['option_true'] === 'true' || $data['option_true'] == '1') {
                    $data['option_true'] = 1;
                }else{
                    $data['option_true'] = 0;
                }
                $saveData[] = [
                    'item_id'          => $item_id,
                    'option_value'     => $data['option_value'] ?? '',
                    'option_img'       => $data['option_img'] ?? '',
                    'option_fill_type' => $data['option_fill_type'] ?? '',
                    'option_type' => $data['option_type'] ?? '',
                    'option_true'      => $data['option_true'] ?? '0',
                    'reference_answer' => $data['reference_answer'] ?? '',
                ];
            }
//            if (isset($option['option_true']) === true) {
//                $option['option_true'] = 1;
//            }
//            $saveData[] = [
//                'item_id'          => $item_id,
//                'option_value'     => $option['option_value'] ?? '',
//                'option_img'       => $option['option_img'] ?? '',
//                'option_fill_type' => $option['option_fill_type'] ?? '',
//                'option_true'      => $option['option_true'] ?? 0,
//                'reference_answer' => $option['reference_answer'] ?? '',
//            ];
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

    //获取题目详情
    static function getDetails($item_id){
        $data = Db::name('question_item')->field('item_id, item_kind, item_type,content')->where('item_id', $item_id)->find();
        $data2 = Db::name('question_item_option')->where('item_id', $item_id)->select()->toArray();
        $data['item_kind'] = (string)$data['item_kind'];
        $data['item_type'] = (string)$data['item_type'];
        $radio_data = [];
        $checkbox_data = [];
        $pack = [];
        foreach ($data2 as $item) {
            if($item['option_type'] == '0'){
                $radio_data[] = $item;
            }else if($item['option_type'] == '1'){
                $checkbox_data[] = $item;
            }else{
                $pack[] = $item;
            }
        }
        $res = ['form'=>$data,'radio_data'=>$radio_data,'checkbox_data'=>$checkbox_data,'pack'=>$pack];
        return BaseService::createReturn(true,$res);

//            $lists = QuestionItemModel::where($where)
//                ->with(['item_options'])
//                ->append(['item_kind_text', 'item_type_text'])
//                ->order('item_id', 'DESC')
//                ->paginate(20);
    }
}