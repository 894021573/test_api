<?php
return [
    'name' => '案例2', // 项目名称
    'un_processed_class' => $_SERVER['DOCUMENT_ROOT'] . '/test_request/class/demo2/',
    'host' => 'http://www.test_api.cn/', // 请求地址
    'controllers' =>
        [
            ['file_path' => 'Demo2.php', 'class_name' => 'Demo2'], // 控制器
            ['file_path' => 'Demo22.php', 'class_name' => 'Demo22'], // 控制器
        ],
];