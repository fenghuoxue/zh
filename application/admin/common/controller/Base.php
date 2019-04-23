<?php
/**
 * Created by PhpStorm.
 * User: 顺
 * Date: 2019/4/21
 * Time: 12:50
 */

namespace app\admin\common\controller;
use think\Controller;
use think\facade\Session;
class Base extends Controller
{
    //初始化方法
    public function initialize()
    {

    }
    /*
     * 检测用户是否登陆
     * 1.调用位置：后台入口：admin.php
     * */
    protected function isLogin()
    {
        if(!Session::get('user_id'))
        {
            return $this->error('请先登录','admin/admin/login');
        }
    }
}