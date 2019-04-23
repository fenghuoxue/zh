<?php
/**
 * Created by PhpStorm.
 * User: 顺
 * Date: 2019/4/21
 * Time: 12:35
 */

namespace app\admin\controller;

use app\admin\common\controller\Base;
class Index extends Base
{
    public function index()
    {
        //用户登录
        $this->isLogin();
        return $this->redirect('admin/adminList');
    }
}