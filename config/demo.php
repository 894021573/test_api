<?php
// un_processed_class 和 controllers 下的 file_path 组成类的绝对路径
return [
    'name' => '案例', // 项目名称
    'un_processed_class' => $_SERVER['DOCUMENT_ROOT'] . '/test_request/class/demo/', // 需要被解析的类文件目录
    'host' => 'http://www.test_api.cn/', // 请求地址
    'controllers' =>
        [
            // 需要被解析的类文件和完全类名
            ['file_path' => 'a/Demo.php', 'class_name' => 'project\\Demo'], // 控制器
        ],
];