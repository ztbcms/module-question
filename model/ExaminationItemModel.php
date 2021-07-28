<?php


namespace app\question\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\facade\Db;
use app\common\service\BaseService;


class ExaminationItemModel extends Model
{
    protected $name = 'question_item';
    protected $pk = 'item_id';

    const ITEM_KIND_EXAMINATION = 1;

    const ITEM_TYPE_RADIO = 0;
    const ITEM_TYPE_CHECK = 1;
    const ITEM_TYPE_FILL = 2;

}