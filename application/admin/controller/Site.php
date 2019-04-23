<?php
/**
 * Created by PhpStorm.
 * User: 顺
 * Date: 2019/4/23
 * Time: 17:53
 */

namespace app\admin\controller;
use app\admin\common\controller\Base;
use app\admin\common\model\Site as SiteModel;
use think\facade\Request;
use think\facade\Session;

class Site extends Base
{
    public function index()
    {
        //获取站点信息
        $siteInfo = SiteModel::get(['status' => 1]);

        //模板赋值
        $this->view->assign('siteInfo',$siteInfo);

        //渲染
        return $this->view->fetch('index');
    }

    //修改
    public function save()
    {
        $data = Request::param();

        if(SiteModel::update($data))
        {
            $this->success('更新成功','index');
        }else{
            $this->error('更新失败','index');
        }
    }
}