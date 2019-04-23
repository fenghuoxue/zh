<?php
/**
 * Created by PhpStorm.
 * User: 顺
 * Date: 2019/4/18
 * Time: 15:52
 * zh_user表的用户模型
 */

namespace app\common\model;
use think\Model;

class Article extends Model
{
    protected $pk = 'id';//指定主键
    protected $table = 'zh_article';//指定绑定表
    protected $autoWriteTimestamp = true; //开启自动时间戳
    //定义时间戳字段名:默认为create_time和update_time，如果一致可省略
    //如果想关闭某个时间戳，将值设置为false即可:$create_time = false
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $dateFormat = 'Y年m月d日';

   //开启自动设置
    protected $auto = [];//无论是新增或者更新都要设置的字段
    //仅新增的有效
    protected $insert = ['create_time','status'=> 1,'is_top'=> 0,'is_hot'=>0];
    //仅更新的有效
    protected $update = ['update_time'];
}