<?php
/**
 * Created by PhpStorm.
 * User: 顺
 * Date: 2019/4/21
 * Time: 15:33
 */

namespace app\admin\common\model;

use think\Model;
class User extends Model
{
    protected $pk = 'id';
    protected $table = 'zh_user';
}