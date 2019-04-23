<?php
/**
 * Created by PhpStorm.
 * User: 顺
 * Date: 2019/4/23
 * Time: 22:16
 */

namespace app\common\model;

use think\Model;
class Comment extends Model
{
    protected $pk = 'id';
    protected $table = "zh_user_comments";
    protected $autoWriteTimestamp = true; //开启自动时间戳
    //定义时间戳字段名:默认为create_time和update_time，如果一致可省略
    //如果想关闭某个时间戳，将值设置为false即可:$create_time = false
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $dateFormat = 'Y年m月d日';

    //开启自动设置
    protected $auto = [];//无论是新增或者更新都要设置的字段
    //仅新增的有效
    protected $insert = ['create_time','status'=> 1];
    //仅更新的有效
    protected $update = ['update_time'];
}