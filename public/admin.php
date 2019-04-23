<?php
/**
 * Created by PhpStorm.
 * User: 顺
 * Date: 2019/4/21
 * Time: 12:42
 */
//后台入口文件
namespace think;

// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';

// 支持事先使用静态方法设置Request对象和Config对象

// 执行应用并响应
Container::get('app')->run()->send();