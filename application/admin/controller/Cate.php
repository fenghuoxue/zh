<?php
/**
 * Created by PhpStorm.
 * User: 顺
 * Date: 2019/4/23
 * Time: 10:26
 */

namespace app\admin\controller;
use app\admin\common\controller\Base;
use app\admin\common\model\Cate as CateModel;
use think\facade\Request;
use think\facade\Session;

class Cate extends Base
{
    public function index()
    {
        //判断是否登陆
        $this->isLogin();
        //登陆成功后跳转分类界面
        return $this->redirect('cateList');
    }

    //分类列表
    public function cateList()
    {
        //检查用户是否登陆
        $this->isLogin();
        //获取所有分类列表
        $cateList = CateModel::all();

        //设置模板变量
        $this->view->assign('title','分类管理');
        $this->view->assign('empty','<span>没有分类</span>');
        $this->view->assign('cateList',$cateList);

        //渲染
        return $this->view->fetch('cateList');
    }

    //渲染编辑分类界面
    public function cateEdit()
    {
        //获取分类id
        $cateId = Request::param('id');

        //查询分类信息
        $cateInfo = CateModel::where('id',$cateId)
                ->find();
        //设置模板变量
        $this->view->assign('title','编辑分类');
        $this->view->assign('cateInfo',$cateInfo);

        //渲染模板
        return $this->view->fetch('cateEdit');
    }

    //执行更新操作
    public function doEdit()
    {
        //获取更新信息
        $data = Request::param();
        //更新操作时间
        $data['update_time'] = time();
        $id = $data['id'];
        //删除主键字段
        unset($data['id']);
        //执行更新
        if(CateModel::where('id',$id)->data($data)->update())
        {
            //成功
            return $this->success('更新成功','cateList');
        }else{
            $this->error('更新失败');
        }
    }

    //执行删除
    public function doDelete()
    {
        //获取要删除的主键ID
        $id = Request::param('id');
        //执行删除
        if(CateModel::where('id',$id)->delete())
        {
            return $this->success('删除成功','cateList');
        }else{
            $this->error('删除失败');
        }
    }

    //渲染添加界面
    public function cateAdd()
    {
       return $this->fetch('cateadd',['title' => '添加分类']);

    }

    //添加分类
    public function doAdd()
    {
        //获取提交数据
        $data = Request::param();
        //执行新增操作
        if(CateModel::create($data))
        {
            $this->success('添加成功','cateList');
        }else{
            $this->error('添加失败');
        }


    }
}