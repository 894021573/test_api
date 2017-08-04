<?php

/**
 *
 * @needParse
 * [
 *      'title' => 'demo2控制器',
 *      'description' => 'demo2接口描述'
 * ]
 */
class Demo2
{
    /**
     * @needParse
     * [
     *      'title' => '上传文件',
     *      'description' => '上传文件',
     *      'request_uri' => 'test_request/upload_file.php',
     *      'method_type' => 'POST',
     *      'params' =>
     *      [
     *          ['name' => 'user_id','type' => '数组','require' => '是' , 'default' => '[1,2,3]' , 'description' => '用户ID'],
     *          ['name' => 'upload_file','type' => '文件','require' => '是' , 'default' => '' , 'description' => '文件上传'],
     *      ],
     *      'remark' => 'demo备注说明',
     *      'return' =>
     *      [
     *          'code' => '状态码',
     *          'message' => '返回信息',
     *          'data' => [
     *              ['user_id' => '用户ID'],
     *              ['content' => '文件内容'],
     *          ],
     *      ]
     * ]
     */
    public function uploadFile()
    {
        file_put_contents('dd.txt',var_export($_FILES,true));
        exit();

        if ($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            echo json_encode(['不是post请求']);
            exit();
        }

        if (empty($_POST['user_id']))
        {
            echo json_encode(['user_id不能为空']);
            exit();
        }

        $result = [
            'code' => 200,
            'message' => 'ok',
            'data' => [
                'user_id' => implode(',', $_POST['user_id']),
                'content' => file_get_contents($_FILES['upload_file']['tmp_name']),
            ],
        ];

        echo json_encode($result);
    }
}