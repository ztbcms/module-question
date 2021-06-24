<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2021/3/5
 * Time: 13:41.
 */

namespace app\question\controller;


use app\BaseController;
use app\question\model\QuestionQuestionnaireAnswerItemModel;
use app\question\model\QuestionQuestionnaireAnswerModel;
use app\question\model\QuestionQuestionnaireModel;
use app\question\model\QuestionExaminationAnswerModel;
use app\question\model\QuestionExaminationAnswerItemModel;

class Api extends BaseController
{

    /**
     * 问卷提交
     * @return \think\response\Json
     */
    function questionnaire_confirm()
    {
        $questionnaire_answer_id = request()->param('questionnaire_answer_id', 0);
        $questionnaire_answer = QuestionQuestionnaireAnswerModel::where('questionnaire_answer_id',
            $questionnaire_answer_id)->findOrEmpty();
        if ($questionnaire_answer->isEmpty()) {
            return self::makeJsonReturn(false, [], '找不到该回答');
        }
        if ($questionnaire_answer->status == QuestionQuestionnaireAnswerModel::STATUS_CONFIRM) {
            return self::makeJsonReturn(false, [], '该问卷已提交');
        }
        $questionnaire_answer->status = QuestionQuestionnaireAnswerModel::STATUS_CONFIRM;
        $questionnaire_answer->confirm_time = time();
        $questionnaire_answer->transaction(function () use ($questionnaire_answer)
        {
            //关联的答题记录也需要更新状态
            $questionnaire_answer->save();
            QuestionQuestionnaireAnswerItemModel::where('questionnaire_answer_id',
                $questionnaire_answer->questionnaire_answer_id)
                ->update([
                    'status' => QuestionQuestionnaireAnswerModel::STATUS_CONFIRM
                ]);
        });
        if ($questionnaire_answer->save()) {
            return self::makeJsonReturn(true, [], 'ok');
        } else {
            return self::makeJsonReturn(false, [], '');
        }
    }

    /**
     * 问卷回答
     * @return \think\response\Json
     */
    function questionnaire_answer()
    {
        $questionnaire_answer_id = request()->param('questionnaire_answer_id', 0);
        $questionnaire_id = request()->param('questionnaire_id', 0);
        $item_id = request()->param('item_id', 0);
        $option_values = request()->param('option_values', 0);
        $target = request()->param('target', 1);
        $target_type = 'user_id';

        $questionnaire_answer = QuestionQuestionnaireAnswerModel::where('questionnaire_answer_id',
            $questionnaire_answer_id)->findOrEmpty();
        if ($questionnaire_answer->isEmpty()) {
            $questionnaire_answer->questionnaire_id = $questionnaire_id;
            $questionnaire_answer->target = $target;
            $questionnaire_answer->target_type = $target_type;
            $questionnaire_answer->save();
        }
        try {
            QuestionQuestionnaireAnswerModel::saveItemAnswer($questionnaire_answer, $item_id, $option_values);
            return self::makeJsonReturn(true,
                ['questionnaire_answer_id' => $questionnaire_answer->questionnaire_answer_id], 'ok');
        } catch (\Throwable $exception) {
            return self::makeJsonReturn(false, [], $exception->getMessage());
        }
    }

    /**
     * 获取问卷信息
     * @return \think\response\Json
     */
    function get_questionnaire()
    {
        $questionnaire_id = request()->param('questionnaire_id', 0);
        $questionnaire = QuestionQuestionnaireModel::where('questionnaire_id', $questionnaire_id)
            ->with(['item_api_list'])
            ->findOrEmpty();
        if ($questionnaire->isEmpty()) {
            return self::makeJsonReturn(false, [], '找不到该记录');
        }
        return self::makeJsonReturn(true, ['questionnaire' => $questionnaire], 'ok');
    }

    /**
     * 答题提交
     * @return \think\response\Json
     */
    function examination_confirm()
    {
        $examination_answer_id = request()->param('examination_answer_id', 0);
        $examination_answer = QuestionExaminationAnswerModel::where('examination_answer_id',
            $examination_answer_id)->findOrEmpty();
        if ($examination_answer->isEmpty()) {
            return self::makeJsonReturn(false, [], '找不到该回答');
        }
        if ($examination_answer->status == QuestionExaminationAnswerModel::STATUS_CONFIRM) {
            return self::makeJsonReturn(false, [], '该答题已提交');
        }
        $examination_answer->status = QuestionExaminationAnswerModel::STATUS_CONFIRM;
        $examination_answer->confirm_time = time();
        $examination_answer->transaction(function () use ($examination_answer)
        {
            //关联的答题记录也需要更新状态
            $examination_answer->save();
            QuestionExaminationAnswerItemModel::where('examination_answer_id',
                $examination_answer->examination_answer_id)
                ->update([
                    'status' => QuestionExaminationAnswerModel::STATUS_CONFIRM
                ]);
        });
        if ($examination_answer->save()) {
            return self::makeJsonReturn(true, [], 'ok');
        } else {
            return self::makeJsonReturn(false, [], '');
        }
    }

    /**
     * 答题回答
     * @return \think\response\Json
     */
    function examination_answer()
    {
        $examination_answer_id = request()->param('examination_answer_id', 0);
        $examination_id = request()->param('examination_id', 0);
        $item_id = request()->param('item_id', 0);
        $option_values = request()->param('option_values', 0);
        $target = request()->param('target', 1);
        $target_type = 'user_id';
        $examination_answer = QuestionExaminationAnswerModel::where('examination_answer_id',
            $examination_answer_id)->findOrEmpty();
        if ($examination_answer->isEmpty()) {
            $examination_answer->examination_id = $examination_id;
            $examination_answer->target = $target;
            $examination_answer->target_type = $target_type;
            $examination_answer->save();
        }
        try {
            QuestionExaminationAnswerModel::saveItemAnswer($examination_answer, $item_id, $option_values);
            return self::makeJsonReturn(true,
                ['examination_answer_id' => $examination_answer->examination_answer_id], 'ok');
        } catch (\Throwable $exception) {
            return self::makeJsonReturn(false, [], $exception->getMessage());
        }
    }
}