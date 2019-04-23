<?php
/**
 * Created by PhpStorm.
 * User: 顺
 * Date: 2019/4/23
 * Time: 10:25
 */

namespace app\admin\common\model;
use think\Model;

class Cate extends Model
{
    protected $pk = 'id';
    protected $table = 'zh_article_category';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
}