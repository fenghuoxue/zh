<?php
/**
 * Created by PhpStorm.
 * User: 顺
 * Date: 2019/4/21
 * Time: 15:42
 */

namespace app\admin\controller;
use app\admin\common\controller\Base;
use app\admin\common\model\User;
use think\facade\Request;
use think\facade\Session;

class Admin extends Base
{
    //渲染登陆界面
    public function login()
    {
        $this->view->assign('title','后台登录');
        return $this->view->fetch('login');
    }

    //验证后台登陆
    public function checkLogin()
    {
        $data = Request::param();
        $map[] = ['email','=',$data['email']];
        $map[] = ['password','=',sha1($data['password'])];
        $result = User::where($map)
            ->find();
        if($result)
        {
            Session::set('user_id',$result['id']);
            Session::set('user_name',$result['name']);
            Session::set('user_level',$result['is_admin']);
            $this->success('登陆成功','admin/admin/adminList');
        }else{
            $this->error('登陆失败','admin/admin/login');
        }
    }

    //退出登录
    public function loginOut()
    {
        Session::clear();
        $this->success('退出成功','admin/admin/login');
    }

    //用户列表
    public function adminList()
    {
        //获取当前用户id和level
        $data['admin_id'] = Session::get('user_id');
        $data['admin_level'] = Session::get('user_level');

        //获取当前用户的信息
        $adminList = User::where('id',$data['admin_id'])
                ->select();

        //管理员获取全部信息
       if( $data['admin_level'] == 1)
       {
           $adminList = User::select();
       }

       //模板信息
        $this->view->assign('title','用户管理');
        $this->view->assign('empty','<span>没有数据</span>');
        $this->view->assign('adminList',$adminList);

        //渲染用户列表模板
        return $this->view->fetch('adminList');
    }

    //渲染编辑用户的界面
    public function adminEdit()
    {
        //获取要更新的用户主键
        $adminId = Request::param('id');

        //根据主键进行查询
        $adminInfo = User::where('id',$adminId)
            ->find();

        //设置编辑界面的模板变量
        $this->view->assign('title','编辑用户');
        $this->view->assign('adminInfo',$adminInfo);

        //渲染
        return $this->view->fetch('adminEdit');
    }
    //执行用户的信息更新
    public function doEdit()
    {
        //获取到用户提交的信息
        $data = Request::param();

        //取出主键
        $id = $data['id'];

        //判断用户密码是否为空，不为空加密
        $psw = $data['password'];
        if (empty($psw)) {
            //密码为空，踢出密码
            unset($data['password']);
        } else {
            //密码不为空，进行加密处理
            $data['password'] = sha1($data['password']);
        }
        //踢出主键
        unset($data['id']);

        //执行更新
        if (User::where('id', $id)->data($data)->update()) {
            return $this->success('更新成功', 'adminList');
        } else {
            $this->error('更新失败');
        }

    }

    //执行用户的删除操作
    public function doDelete()
    {
        //获取要删除的主键ID
        $id = Request::param('id');

        //执行删除
        if(User::where('id',$id)->delete())
        {
            return $this->success('删除成功','adminList');
        }else{
            $this->error('删除失败');
        }
    }
}