<?php
/**
 * Created by PhpStorm.
 * User: 顺
 * Date: 2019/4/18
 * Time: 15:54
 * zh_user表的验证器
 */
/*
 *chsAlphaNum:仅允许汉字、字母或数字
 *  'unique' => 'zh_user' 该字段必须在zh_user表中是唯一的
 *alphaNum//仅允许数字和字母
 * 'confirm' => 'confirm' 自动与password_confirm字段进行相等验证
 * */
namespace app\common\validate;
use think\Validate;

class Article extends Validate
{
    protected $rule = [
        'title|标题' => 'require|length:5,20|chsAlphaNum',
        'title_img|文章图片' => 'require',
        'content|文章内容'=> 'require',
        'user_id|作者' => 'require',
        'cate_id|栏目名称' => 'require'
    ];

}