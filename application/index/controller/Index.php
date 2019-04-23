<?php
namespace app\index\controller;
use app\common\controller\Base;
use app\common\model\ArtCate;
use app\common\model\Article;
use app\common\model\Comment;
use think\facade\Request;
use think\Db;
use think\facade\Session;//导入公共控制器
class Index extends Base
{
    //首页方法
    public function index()
    {
        //设置全局查询条件
        $map = [];//将所有的查询条件封装到这个数组中
        //条件1
        $map[] = ['status', '=', 1];

        //实现搜索功能
        $keywords = Request::param('keywords');
        if(!empty($keywords))
        {
            //条件2
            $map[] = ['title','like', '%'.$keywords.'%'];
        }

        //分类信息显示
        $cateId = Request::param('cate_id');
        //如果存在这个分类id
        if(isset($cateId))
        {
            //条件3
            $map[] = ['cate_id','=',$cateId];

            $res = ArtCate::get($cateId);
            $artList = Db::table('zh_article')
                ->where($map)
                ->order('create_time','desc')
                ->paginate(3,false,['query'=>request()->param()]);

            $this->view->assign('cateName',$res->name);
        }else{
            $this->view->assign('cateName','全部文章');
            $artList = Db::table('zh_article')
                ->where($map)
                ->order('create_time','desc')
                ->paginate(3,false,['query'=>request()->param()]);
        }
        $this->view->assign('empty','<h3>没有模板</h3>');
        $this->view->assign('artList',$artList);
        return $this->fetch('index',['name'=>'社区问答']);
    }
    //添加文章界面
    public function insert()
    {
        //1登陆才能发布文章
        $this->isLogin();
        //2.设置页面标题
        $this->view->assign('title','发布文章');
        //3.获取一下栏目的信息
        $cateList = ArtCate::all();
        if(count($cateList) >0)
        {
            //将查询到的栏目信息赋值给模板
            $this->assign('cateList',$cateList);
        }else{
            $this->error('请先添加栏目','index/index');
        }
        //4.发布界面渲染
        return $this->view->fetch('insert');
    }

    //保存文章
    public function save()
    {
        //判断提交类型
        if(Request::isPost())
        {
            //1.获取用户提交的文章信息
            $data = Request::post();
            //获取上传图片信息
            //提交时在浏览器存储的临时文件名称
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
                } else {
                    $this->error($file->getError());
                }
                $res = $this->validate($data, 'app\common\validate\Article');
                if($res)
                {
                    //将数据写到数据表中
                    if (Article::create($data)) {
                        $this->success('文章发布成功', 'index/index');
                    } else {
                        $this->error('文章发布失败');
                    }
                }else{
                    $this->error($res,'index/insert');
                }
            }else{
                $this->error('标题图片为空');
            }
        }else{
            $this->error('请求类型错误');
        }
    }

    //详情页
    public function detail()
    {
        $artId = Request::param('id');
        $art = Article::get(function ($query) use ($artId){
           $query->where('id',$artId)
                 ->setInc('pv');
        });
        if(!is_null($art))
        {
            $this->view->assign('art',$art);
        }
        //添加评论
        $this->view->assign('commentList',Comment::all(function ($query) use ($artId){
            $query->where('status',1)
                ->where('article_id',$artId)
                ->order('create_time','desc');
        }));

        //判断是否登陆
        $user_id = Session::get('user_id');
        if(!empty($user_id)) {
            //用户已登录，判断该用户是否对改文章点赞或收藏
            $map[] = ['user_id', '=', $user_id];
            $map[] = ['article_id', '=', $artId];
            $fav   = Db::table('zh_user_fav')
                ->where($map)
                ->find();
            if (!is_null($fav)) {
                //已点赞
                $this->view->assign('fav', 1);
            } else {
                $this->view->assign('fav', 0);
            }

            $like = Db::table('zh_user_like')
                ->where($map)
                ->find();
            if (!is_null($like)) {
                //已点赞
                $this->view->assign('like', 1);
            } else {
                $this->view->assign('like', 0);
            }
        }
        $this->view->assign('title','详情页');
        return $this->view->fetch('detail');
    }

    //收藏
    public function fav()
    {
        if (!Request::isAjax()) {
            return ['status' => -1, 'message' => '请求类型错误'];
        } else {
            //获取从前端传递过来的数据
            $data = Request::param();

            //二次检测用户是否登陆
            if (empty($data['session_id'])) {
                //未登录
                return ['status' => -1, 'message' => '请登录后再收藏'];
            }

            $map[] = ['user_id', '=', $data['session_id']];
            $map[] = ['article_id', '=', $data['article_id']];
            $fav   = Db::table('zh_user_fav')
                ->where($map)
                ->find();
            if (is_null($fav)) {
                //未收藏，执行插入操作
                Db::table('zh_user_fav')
                    ->data(['user_id' => $data['session_id'], 'article_id' => $data['article_id']])
                    ->insert();
                return ['status' => 1, 'message' => '收藏成功'];
            } else {
                Db::table('zh_user_fav')
                    ->where($map)
                    ->delete();
                return ['status' => 0, 'message' => '取消收藏成功'];
            }
        }
    }

    //点赞
    public function like()
    {
        if (!Request::isAjax()) {
            return ['status' => -1, 'message' => '请求类型错误'];
        } else {
            //获取从前端传递过来的数据
            $data = Request::param();

            //二次检测用户是否登陆
            if (empty($data['session_id'])) {
                //未登录
                return ['status' => -1, 'message' => '请登录后再收藏'];
            }

            $map[] = ['user_id', '=', $data['session_id']];
            $map[] = ['article_id', '=', $data['article_id']];
            $like   = Db::table('zh_user_like')
                ->where($map)
                ->find();
            if (is_null($like)) {
                //未收藏，执行插入操作
                Db::table('zh_user_like')
                    ->data(['user_id' => $data['session_id'], 'article_id' => $data['article_id']])
                    ->insert();
                return ['status' => 1, 'message' => '点赞成功'];
            } else {
                Db::table('zh_user_like')
                    ->where($map)
                    ->delete();
                return ['status' => 0, 'message' => '取消点赞成功'];
            }
        }
    }

    public function insertComment()
    {
        if (Request::isAjax()) {
            //获取评论
            $data = Request::param();
            //将用户留言存到表中
            if (Comment::create($data,true)) {
                return ['status' => 1, 'message' => '评论发表成功'];
            } else {
                return ['status' => 0, 'message' => '评论发表失败'];
            }
        }
    }

}
