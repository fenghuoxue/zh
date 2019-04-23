<?php
/**
 * Created by PhpStorm.
 * User: 顺
 * Date: 2019/4/18
 * Time: 22:14
 */

namespace app\index\controller;
use app\common\controller\Base;
use app\common\model\User as UserModel;
use think\facade\Request;
use think\facade\Session;
class User extends Base
{
    public function register()
    {

        $this->assign('title','用户注册');
        //检测是否可以注册
        $this->isReg();
        return $this->fetch();
    }

    //处理用户提交的注册信息
    public function insert()
    {
        //检测是否可以注册
        $this->isReg();
        if(Request::isAjax())
        {
            //验证数据
            $data = Request::post();//要验证的数据
            $rule = 'app\common\validate\User';//自定义的验证规则
            //开始验证
            $res = $this->validate($data,$rule);
            if(true !== $res)
            {
                //验证失败
                return ['status' => -1,'message' => $res];
            }else{
                $user = UserModel::create($data);
                if($user)
                {
                    Session::set('user_id',$user->id);
                    Session::set('user_name',$user->name);
                    return ['status' => 1,'message' => '注册成功'];
                }else{
                    return ['status' => 0,'message' => '注册失败，请检查！'];
                }
            }
        }
    }

    //用户登录
    public function login()
    {
        $this->logined();
        return $this->view->fetch('login',['title'=>'用户登录']);
    }
    //用户登陆验证与查询
    public function loginCheck()
    {
        if(Request::isAjax())
        {
            //验证数据
            $data = Request::post();//要验证的数据
            $rule = [
                'email|邮箱' =>'require|email',
                'password|密码' => 'require|alphaNum'
            ];//自定义的验证规则
            //开始验证
            $res = $this->validate($data,$rule);
            if(true !== $res)
            {
                //验证失败
                return ['status' => -1,'message' => $res];
            }else{
                //执行查询
                $result = UserModel::get(function ($query) use ($data){
                    $query->where('email',$data['email'])
                          ->where('password',sha1($data['password']));
                });
                if(null == $result)
                {
                    //查询结果为空，登陆错误
                    return ['status' => -1,'message' => '邮箱或密码错误！'];
                }else{
                    Session::set('user_id',$result->id);
                    Session::set('user_name',$result->name);
                    return ['status' => 1,'message' => '登陆成功！'];
                }

//                $result = Db::table('zh_user')
//                    ->where('email',$data['email'])
//                    ->find();
//                if($result)
//                {
//                    //结果不为空，检测密码是否匹配
//                    if(sha1($data['password']) == $result['password'])
//                    {
//                        //密码相等，登陆成功，Session赋值
//                        Session::set('user_id',$result['id']);
//                        Session::set('user_name',$result['name']);
//                        return ['status' => 1,'message' => '登陆成功！'];
//                    }else{
//                        return ['status' => 0,'message' => '邮箱或密码错误'];
//                    }
//                }else{
//                    return ['status' => 0,'message' => '邮箱或密码错误'];
//                }
            }
        }else{
            $this->error('请求类型错误','login');
        }
    }

    //用户退出登录
    public function loginOut()
    {
        //方法一
//        Session::delete('user_id');
//        Session::delete('user_name');
        //方法二
        Session::clear();

//        Session::destroy(); 该方法不能用在这里
        $this->success('退出登陆成功','index/index');
    }
}