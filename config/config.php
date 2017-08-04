<?php
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