<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2021/2/25
 * Time: 09:35.
 */

namespace app\question\controller;


use app\common\controller\AdminController;
use app\question\model\QuestionItemModel;
use app\question\model\QuestionQuestionnaireModel;
use think\facade\View;

class Questionnaire extends AdminController
{
    /**
     * 问卷列表
     * @return string|\think\response\Json
     * @throws \think\db\exception\DbException
     */
    function index()
    {
        if (request()->isAjax()) {
            $data = QuestionQuestionnaireModel::where([])
                ->append(['item_count', 'submit_count'])
                ->order('questionnaire_id', 'DESC')
                ->paginate(20);
            return self::makeJsonReturn(true, $data, 'ok');
        }
        return View::fetch('index');
    }

    /**
     * 问卷新增和编辑
     * @return string|\think\response\Json
     */
    function edit()
    {

        if (request()->isPost()) {
            $questionnaire_id = request()->post('questionnaire_id', 0);
            $title = request()->post('title');
            $item_ids = request()->post('item_ids', []);
            $questionnaire = QuestionQuestionnaireModel::where('questionnaire_id', $questionnaire_id)
                ->findOrEmpty();
            $questionnaire->title = $title;
            $res = $questionnaire->transaction(function () use ($questionnaire, $item_ids)
            {
                $res = $questionnaire->save();
                return $res && QuestionQuestionnaireModel::saveQuestionnaireItems($questionnaire, $item_ids);
            });
            if ($res) {
                return self::makeJsonReturn(true, [], 'ok');
            } else {
                return self::makeJsonReturn(true, [], '操作失败');
            }
        } elseif (request()->isAjax()) {
            $questionQuestionnaire = QuestionQuestionnaireModel::where('questionnaire_id',
                request()->get('questionnaire_id'))
                ->with('item_list')
                ->findOrEmpty();
            return self::makeJsonReturn(true, ['detail' => $questionQuestionnaire], 'ok');
        }
        return View::fetch('edit');
    }

    /**
     * 获取问卷题目类型
     * @return \think\response\Json
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\db\exception\DataNotFoundException
     */
    function getItemList()
    {
        $query = request()->param('query');
        //删除了 #
        $query = str_replace('#', '', $query);
        $items = QuestionItemModel::where('item_kind', QuestionItemModel::ITEM_KIND_QUESTIONNAIRE)
            ->where(function ($q) use ($query)
            {
                $q->where('item_id', 'like', "%{$query}%")
                    ->whereOr('content', 'like', "%{$query}%");
            })
            ->field(['item_id', 'content', 'item_type'])
            ->append(['item_type_text'])
            ->withAttr('content', function ($value, $data)
            {
                return '#'.$data['item_id'].' '.$value;
            })
            ->order('item_id', 'ASC')
            ->limit(0, 100)
            ->select();
        return self::makeJsonReturn(true, ['items' => $items], 'ok');
    }

    /**
     * 问卷删除
     * @return \think\response\Json
     */
    function delete()
    {
        $questionnaire_id = request()->post('questionnaire_id');
        $questionnaire = QuestionQuestionnaireModel::where('questionnaire_id', $questionnaire_id)
            ->findOrEmpty();
        if ($questionnaire->isEmpty()) {
            return self::makeJsonReturn(false, [], '未找到该记录');
        }
        if ($questionnaire->delete()) {
            return self::makeJsonReturn(true, [], '删除成功');
        } else {
            return self::makeJsonReturn(false, [], '操作失败');
        }
    }
}