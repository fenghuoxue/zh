<?php
/**
 * Created by PhpStorm.
 * User: 顺
 * Date: 2019/4/23
 * Time: 10:26
 */

namespace app\admin\controller;
use app\admin\common\controller\Base;
use app\admin\common\model\Article as ArticleModel;
use app\admin\common\model\Cate;
use think\facade\Request;
use think\facade\Session;
use think\Paginator;

class Article extends Base
{
    public function index()
    {
        //判断是否登陆
        $this->isLogin();
        //登陆成功后跳转文章管理界面
        return $this->redirect('artList');
    }

    //文章列表
    public function artList()
    {
        //检查用户是否登陆
        $this->isLogin();
        //获取用户id和级别
        $userId = Session::get('user_id');
        $isAdmin = Session::get('user_level');
        //根据用户级别获取文章信息
        if($isAdmin == 1)
        {
            //管理员获取全部文章
            $artList = ArticleModel::paginate(5,false,['query'=>request()->param()]);
        }else{
            $artList = ArticleModel::where('user_id',$userId)
                ->paginate(5,false,['query'=>request()->param()]);
        }

        //设置模板变量
        $this->view->assign('title','文章管理');
        $this->view->assign('empty','<span>没有文章</span>');
        $this->view->assign('artList',$artList);

        //渲染
        return $this->view->fetch('artList');
    }

    //渲染编辑分类界面
    public function artEdit()
    {
        //获取分类id
        $artId = Request::param('id');

        //获取栏目信息
        $cateList = Cate::all();

        $artInfo = ArticleModel::where('id',$artId)
            ->find();
        //设置模板变量
        $this->view->assign('title','编辑文章');
        $this->view->assign('cateList',$cateList);
        $this->view->assign('artInfo',$artInfo);

        //渲染模板
        return $this->view->fetch('artEdit');
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

        if($_FILES['title_img']['tmp_name']){
            //判断是否有文件上传
            $file = Request::file('title_img');
            //文件信息验证成功后，上传到服务器上的指定目录,以public为起始目录
            $info = $file->validate([
                'size' => 1000000,
                'ext'  => 'jpeg,jpg,gif,png',
            ])->move('uploads/');
            if ($info) {
                $data['title_img'] = $info->getSaveName();
            }
        }
        //执行更新
        if(ArticleModel::where('id',$id)->data($data)->update())
        {
            //成功
            return $this->success('更新成功','artList');
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
        if(ArticleModel::where('id',$id)->delete())
        {
            return $this->success('删除成功','artList');
        }else{
            $this->error('删除失败');
        }
    }

}