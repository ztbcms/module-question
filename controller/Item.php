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
}