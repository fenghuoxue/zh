<?php
/**
 * Created by PhpStorm.
 * User: 顺
 * Date: 2019/4/18
 * Time: 15:38
 * 基础控制器
 * 必须继承自think\Controller.php
 */

namespace app\common\controller;
use think\Controller;
use think\facade\Session;
use app\common\model\ArtCate;
use app\common\model\Article;
use app\admin\common\model\Site;
use think\facade\Request;
class base extends Controller
{
    /*
     * 初始化方法
     * 创建常量，公共方法
     * 在所有的方法之前被调用*/

    protected function initialize()
    {
        //显示分类导航
        $this->showNav();

        //检测网站是否关闭
        $this->isOpen();

        //获取右侧数据
        $this->getHotArt();
    }

    //防止重复登陆
    public function logined()
    {
        if(Session::has('user_id'))
        {
            $this->error('客官，您已经登陆过了','index/index');
        }
    }
    //检查是否未登录:放在需要登录操作的方法的最前面，例如发布文章
    public function isLogin()
    {
        if(!Session::has('user_id'))
        {
            $this->error('客官，您是不是忘记登陆了--','user/login');
        }
    }

    //显示分类导航
    protected function showNav()
    {
        //1.查询分类表，获取所有分类信息
        $cateList = ArtCate::all(function ($query){
            $query->where('status',1)
                  ->order('sort','asc');
        });
        //2.将分类信息赋值给模板，nav.html
        $this->view->assign('cateList',$cateList);
    }

    //检测站点是否关闭
    public function isOpen()
    {
        //获取当前网站状态
        $isOpen = Site::where('status',1)
            ->value('is_open');

        //如果站点关闭，禁止访问前台
        if($isOpen == 0 && Request::module() == 'index')
        {
            $info = '<body style="background-color:#333"><h1 style="color: #EEEEEE;text-align: center;margin: 200px">网站维护中....</h1></body>';
            exit($info);   
        }
    }

    //检测站点是否可以注册
    public function isReg()
    {
        //获取当前注册状态
        $isReg = Site::where('status',1)
            ->value('is_reg');

        //如果注册关闭，返回首页
        if($isReg == 0)
        {
            $this->error('注册功能已关闭','index/index');
        }else{
            $this->error('注册功能已开启','index/index');
        }
    }

    //获取热门
    public function getHotArt()
    {
        $hotArtList = Article::where('status',1)
            ->order('pv','desc')
            ->limit(10)
            ->select();
        $this->view->assign('hotArtList',$hotArtList);
    }
}