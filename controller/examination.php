<?php
/**
 * Created by PhpStorm.
 * User: xyh
 * Date: 2021/6/16
 */

namespace app\question\controller;

use app\common\controller\AdminController;
use app\question\model\QuestionItemOptionModel;
use think\facade\View;
use app\question\model\QuestionExaminationModel;
use app\question\model\ExaminationItemModel;
use app\question\model\QuestionExaminationAnswerModel;
use app\question\model\QuestionExaminationItemModel;
use app\question\model\QuestionExaminationAnswerItemModel;
use app\question\model\QuestionItemModel;
use think\response\Json;


class examination extends AdminController
{
    /**
     * @return string|Json
     */
    function index()
    {
        if (request()->isAjax()) {
            $where = [];
            $keyword = request()->param('keyword', '');
            if ($keyword != '') {
                $where[] = ['title', 'like', "%{$keyword}%"];
            }
            $lists = QuestionExaminationModel::where($where)
                ->append(['item_count', 'submit_count'])
                ->order('examination_id', 'DESC')
                ->paginate(20);
            return self::makeJsonReturn(true, $lists, 'ok');
        }
        return View::fetch('index');
    }

    /**
     * 问卷新增和编辑
     * @return string|Json
     */
    function edit()
    {

        if (request()->isPost()) {
            $examination_id = request()->post('examination_id', 0);
            $title = request()->post('title');
            $description = request()->post('description');
            $number = request()->post('number');
            $type = request()->post('type', 0);
            $item_ids = request()->post('item_ids', []);
            $examination = QuestionExaminationModel::where('examination_id', $examination_id)
                ->findOrEmpty();
            $examination->title = $title;
            $examination->description = $description;
            $examination->number = $number;
            $examination->type = $type;
            $res = $examination->transaction(function () use ($examination, $item_ids)
            {
                $res = $examination->save();
                return $res && QuestionExaminationModel::saveExaminationItems($examination, $item_ids);
            });
            if ($res) {
                return self::makeJsonReturn(true, [], 'ok');
            } else {
                return self::makeJsonReturn(true, [], '操作失败');
            }
        } elseif (request()->isAjax()) {
            $questionExamination = QuestionExaminationModel::where('examination_id',
                request()->get('examination_id'))
                ->with('item_list')
                ->findOrEmpty();
            return self::makeJsonReturn(true, ['detail' => $questionExamination], 'ok');
        }
        return View::fetch('edit');
    }

    /**
     * 获取答题题目类型
     * @return Json
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\db\exception\DataNotFoundException
     */
    function getItemList(): Json
    {
        $query = request()->param('query');
        //删除了 #
        $query = str_replace('#', '', $query);
        $items = ExaminationItemModel::where('item_kind', ExaminationItemModel::ITEM_KIND_EXAMINATION)
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
     * 答题删除
     * @return Json
     */
    function delete(): Json
    {
        $examination_id = request()->post('examination_id');
        $examination = QuestionExaminationModel::where('examination_id', $examination_id)
            ->findOrEmpty();
        if ($examination->isEmpty()) {
            return self::makeJsonReturn(false, [], '未找到该记录');
        }
        if ($examination->delete()) {
            return self::makeJsonReturn(true, [], '删除成功');
        } else {
            return self::makeJsonReturn(false, [], '操作失败');
        }
    }

    /**
     * 答题提交记录
     * @return string|Json
     * @throws \think\db\exception\DbException
     */
    function answer_records()
    {
        $examination_id = request()->param('examination_id', 0);
        $examination = QuestionExaminationModel::where('examination_id', $examination_id)->findOrEmpty();
        if (request()->isAjax()) {
            $where = [];
            $search_where = request()->param('keyword', '');
            if ($search_where != '') {
                $where[] = ['target', 'like', "%{$search_where}%"];
            }
            $data = QuestionExaminationAnswerModel::where('examination_id', $examination_id)
                ->where($where)
                ->append(['proportion'])
                ->where('status', QuestionExaminationAnswerModel::STATUS_CONFIRM)
                ->order('create_time', 'DESC')
                ->paginate(20);
            return self::makeJsonReturn(true, ['lists' => $data, 'examination' => $examination], 'ok');
        }
        return View::fetch('answer_records');
    }

    /**
     * 具体答题的回答情况
     * @return string|Json
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\db\exception\DataNotFoundException
     */
    function answer_records_detail()
    {
        $examination_answer_id = request()->param('examination_answer_id', 0);
        if (request()->isAjax()) {
            $examination_answer = QuestionExaminationAnswerModel::where('examination_answer_id',
                $examination_answer_id)->with('bindExaminationTitle')->findOrEmpty();

            $examination_id = $examination_answer->examination_id;
            $examination_items = QuestionExaminationItemModel::where('examination_id', $examination_id)
                ->append(['option_values', 'option_values_analysis', 'right_key'])
                ->with(['bind_item'])
                ->withAttr('option_values_analysis', function ($value, $data) use ($examination_id)
                {
                    $option_values_analysis = QuestionExaminationAnswerItemModel::where('examination_id',
                        $examination_id)
                        ->where('item_id',
                            $data['item_id'])->field('count("examination_answer_item_id") as count,option_value')
                        ->group('option_value')->select();
                    $total = 0;
                    foreach ($option_values_analysis as $analysis) {
                        $total += $analysis["count"];
                    }
                    return [
                        'list'  => $option_values_analysis,
                        'total' => $total
                    ];
                })
                ->withAttr('option_values', function ($value, $data) use ($examination_answer_id)
                {
                    $option_values = QuestionExaminationAnswerItemModel::where('examination_answer_id',
                        $examination_answer_id)->where('item_id', $data['item_id'])->column('option_value');
                    return implode(',', $option_values);
                })
                ->order('number', 'ASC')
                ->select();
            return self::makeJsonReturn(true,
                ['examination_answer' => $examination_answer, 'examination_items' => $examination_items],
                'ok');
        }
        return View::fetch('answer_records_detail');
    }

    /**
     * 答题分析
     * @return string|Json
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\db\exception\DataNotFoundException
     */
    function analysis()
    {
        if (request()->isAjax()) {
            $examination_id = request()->param('examination_id', 0);
            $examination = QuestionExaminationModel::where('examination_id', $examination_id)->findOrEmpty();
            $examination_items = QuestionExaminationItemModel::where('examination_id', $examination_id)
                ->append(['option_values_analysis', 'right_key', 'answer_count', 'accuracy'])
                ->with(['bind_item'])
                ->order('number', 'ASC')
                ->select();
            return self::makeJsonReturn(true,
                [
                    'examination_items' => $examination_items,
                    'examination'       => $examination
                ],
                'ok');
        }
        return View::fetch('analysis');
    }

}