<?php
/**
 * Created by PhpStorm.
 * User: 顺
 * Date: 2019/4/18
 * Time: 16:05
 * 测试专用
 */

namespace app\index\controller;

use app\common\controller\Base;
use app\common\model\User;
class Test extends Base
{
    //测试用户的验证器
    public function test()
    {
//        $data = [
//            'name' => 'zhou1',
//            'email' => 'zhou@163.com',
//            'mobile' => '18976543211',
//            'password' => '123abc'
//        ];
//
//        $rule = 'app\common\validate\User';
//        return $this->validate($data,$rule);
        dump(User::get(1));
    }
}