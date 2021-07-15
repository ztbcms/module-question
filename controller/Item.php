<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2021/2/23
 * Time: 15:06.
 */

namespace app\question\controller;

use app\common\controller\AdminController;
use app\question\model\QuestionItemModel;
use think\facade\Request;
use think\facade\View;

class Item extends AdminController
{
    /**
     * 题目管理主页
     * @return string|\think\response\Json
     * @throws \think\db\exception\DbException
     */
    function index()
    {
        if (request()->isAjax()) {
            $where = [];
            $keyword = request()->param('keyword', '');
            $item_kind = request()->param('item_kind', '');
            $item_type = request()->param('item_type', '');
            if ($keyword != '') {
                $where[] = ['content', 'like', "%{$keyword}%"];
            }
            if ($item_kind !== '') {
                $where[] = ['item_kind', '=', $item_kind];
            }
            if ($item_type !== '') {
                $where[] = ['item_type', '=', $item_type];
            }
            $lists = QuestionItemModel::where($where)
                ->with(['item_options'])
                ->append(['item_kind_text', 'item_type_text'])
                ->order('item_id', 'DESC')
                ->paginate(20);
            return self::makeJsonReturn(true, $lists, 'ok');
        }
        return View::fetch('index');
    }

    /**
     * 新增题目以及编辑
     * @return \think\response\Json
     */
    function edit()
    {
        $item_id = request()->post('item_id', 0);
        $content = request()->post('content');
        $item_type = request()->post('item_type', 0);
        $item_kind = request()->post('item_kind', 0);
        $options = request()->post('options', []);
        $question_item = QuestionItemModel::where('item_id', $item_id)->findOrEmpty();
        $question_item->content = $content;
        $question_item->item_type = $item_type;
        $question_item->item_kind = $item_kind;
        $res = $question_item->transaction(function () use ($question_item, $options)
        {
            $res = $question_item->save();
            return $res && QuestionItemModel::saveOptions($question_item, $options);
        });
        if ($res) {
            return self::makeJsonReturn(true, [], 'ok');
        } else {
            return self::makeJsonReturn(true, [], '操作失败');
        }
    }

    /**
     * 删除题目
     * @return \think\response\Json
     */
    function delete()
    {
        $item_id = request()->post('item_id');

        if (!QuestionItemModel::checkDelete($item_id)) {
            return self::makeJsonReturn(false, [], '有关联记录，不能删除');
        }
        $questionItem = QuestionItemModel::where('item_id', $item_id)
            ->findOrEmpty();
        if ($questionItem->isEmpty()) {
            return self::makeJsonReturn(false, [], '未找到该记录');
        }
        if ($questionItem->delete()) {
            return self::makeJsonReturn(true, [], '删除成功');
        } else {
            return self::makeJsonReturn(false, [], '操作失败');
        }
    }


    /**
     * 新增/编辑选项
     * @return \think\response\Json|\think\response\View
     */
    function addQuestion()
    {
        $_action = input('_action');
        $item_kind = input('item_kind', 0);
        //获取题目详情
        if (Request::isGet() && $_action == 'getDetail') {
            $item_id = Request::param('item_id', '', 'trim');
            $res = QuestionItemModel::getDetails($item_id);
            return json($res);
        }
        return view('edit_item', ['item_kind' => $item_kind]);
    }


}