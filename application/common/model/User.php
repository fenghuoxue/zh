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

class User extends Model
{
    protected $pk = 'id';//指定主键
    protected $table = 'zh_user';//指定绑定表
    protected $autoWriteTimestamp = true; //开启自动时间戳
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $dateFormat = 'Y年m月d日';

    //获取器 (get字段名Attr)
    public function getStatusAttr($value)
    {
        $status = ['1' => '启用', '0' => '禁用'];
        return $status[$value];
    }
    public function getIsAdminAttr($value)
    {
        $isAdmin = ['1' => '管理员', '0' => '注册用户'];
        return $isAdmin[$value];
    }
    //修改器 (set字段名Attr)
    public function setPasswordAttr($value)
    {
        return sha1($value);
    }
}