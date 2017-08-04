# 接口文档自动生成系统
## php 版本 5.5以上
## 功能：
* 根据文件注释自动生成接口文档,方便书写和维护
* 可在线测试
* 支持多项目接口配置（只需配置类文件的绝对路径和完整类名即可）
## 实现说明：
* 程序读取待解析的类文件代码，处理成可直接包含的类代码，载入，并存储在本地
* 需解析的注释格式写法同php数组
* 测试请求使用了Guzzle库
# 使用说明
## 配置文件 
###### config.php
设置对应的项目路径
```
$demo = require ROOT_DIR . 'config/demo.php';
$demo2 = require ROOT_DIR . 'config/demo2.php';

return [
    // 接口项目
    'projects' =>
        [
            // 项目一
            'demo' => $demo,

            // 项目二
             'demo2' => $demo2,
        ],

    // 接口被处理后的存放目录（一般不用改）
    'processed_class' => $_SERVER['DOCUMENT_ROOT'] . '/processed_class/',

    // 访问guzzle.php文件的url（一般不用改）
    'ajax_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/core/guzzle.php',
    
    // 是否简化类（简化后保存的类代码只包含类和方法的基本结构以及注释，没有任何业务代码，主要为了方便查看）
    'isSimplifyClassContent' => true,
    
    // 是否读取本地保存的类
    'isReadLocalClass' => false,
];
```
###### demo.php
项目具体配置
```
// un_processed_class 和 controllers 下的 file_path 组成类的绝对路径
return [
    'name' => '案例', // 项目名称
    'un_processed_class' => $_SERVER['DOCUMENT_ROOT'] . '/processed_class/class/demo/', // 需要被解析的类文件目录
    'host' => 'http://www.test_api.cn/', // 请求地址
    'controllers' =>
        [
            ['file_path' => 'a/Demo.php', 'class_name' => 'project\\Demo'], // 控制器
        ],
];
```
## 添加注释
类注释中添加@needParse，表示需要解析该注释，必须放在类定义上面，否则PHP反射机制无法获取到该注释
### 类注释
如在类代码和注释外有其余代码，则需用class_start 和 class_end 包含之，用于过滤掉其余代码；否则，这两个标签可省略。

```
// class_start
/**
 *
 * @needParse
 * [
 *      'title' => 'demo控制器',
 *      'description' => 'demo接口描述'
 * ]
 */
 class test{}
 
 // class_end

```
### 方法注释
对应配置文件下的 host 和 request_uri 组成完整的接口访问url
除了params外，其他都是一目了然，不再说明
```
    /**
     * @needParse
     * [
     *      'title' => '获取姓名get',
     *      'description' => '获取一个用户姓名',
     *      'request_uri => 'test_request/get_name.php',
     *      'method_type' => 'POST',
     *      'params' =>
     *      [
     *          ['name' => 'user_id','type' => '整型','require' => '是' , 'default' => '' , 'description' => '用户ID'],
     *      ],
     *      'remark' => 'demo备注说明',
     *      'return' =>
     *      [
     *          'code' => '状态码',
     *          'message' => '返回信息',
     *          'data' => [
     *              ['user_id' => '用户ID'],
     *              ['name' => '用户名称'],
     *          ],
     *      ]
     * ]
     */
```
# params说明

参数类型：数组，文件，其他（都相当于字符串，包括乱写的type）

数组类型：['name' => 'user_id','type' => '数组','require' => '是' , 'default' => '' , 'description' => '用户ID'],其中type的值为[数组]或[array]均可，传值方式为需传数组的json字符串

文件类型：['name' => 'upload_file','type' => '文件','require' => '是' , 'default' => '' , 'description' => ''],其中type的值为[文件]或[file]均可，传值方式为将包含一个或多个文件绝对路径的数组转为json字符串

# 访问：
http://666666/index.php
