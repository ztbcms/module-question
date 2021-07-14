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

class QuestionItemOptionModel extends Model
{
    use SoftDelete;

    protected $defaultSoftDelete = 0;
    protected $name = 'question_item_option';
    protected $pk = 'item_option_id';
    protected $updateTime = false;

    const OPTION_TRUE = 1;
    const OPTION_FALSE = 0;
}