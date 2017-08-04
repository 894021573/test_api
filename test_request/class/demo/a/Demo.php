<?php
namespace project;

/**
 *
 * @needParse
 * [
 *      'title' => 'demo控制器',
 *      'description' => 'demo接口描述'
 * ]
 */
class Demo extends Controller
{
    /**
     * @needParse
     * [
     *      'title' => '获取姓名get',
     *      'description' => '获取一个用户姓名',
     *      'request_uri' => 'test_request/get_name.php',
     *      'params' =>
     *      [
     *          ['name' => 'user_id','type' => '整型','require' => '是' , 'default' => '' , 'description' => '用户ID'],
     *      ],
     *      'remark' => 'demo备注说明',
     *      'return' =>
     *      [
     *          'status' => '状态码',
     *          'message' => '返回信息',
     *          'data' => [
     *              ['user_id' => '用户ID'],
     *              ['name' => '用户名称'],
     *          ],
     *      ]
     * ]
     */
    public function getName()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET')
        {
            echo json_encode(['不是get请求']);
            exit();
        }

        if (empty($_GET['user_id']))
        {
            echo json_encode(['user_id不能为空']);
            exit();
        }

        $userID = $_GET['user_id'];

        $result = [
            'code' => 200,
            'message' => 'ok',
            'data' => [
                'user_id' => $userID,
                'name' => 'hello',
            ],
        ];

        echo json_encode($result);
    }

    /**
     * @needParse
     * [
     *      'title' => '修改姓名post',
     *      'description' => '修改姓名',
     *      'request_uri' => 'test_request/set_name.php',
     *      'params' =>
     *      [
     *          ['name' => 'user_id','type' => '整型','require' => '是' , 'default' => '' , 'description' => '用户ID'],
     *      ],
     *      'return' =>
     *      [
     *          'code' => '状态码',
     *          'message' => '返回信息',
     *          'data' =>
     *          [
     *              ['user_id' => '用户ID'],
     *              ['name' => '用户名称'],
     *          ],
     *      ]
     * ]
     */
    public function setName()
    {
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

        $userID = $_POST['user_id'];

        $result = [
            'code' => 200,
            'message' => 'ok',
            'data' => [
                'user_id' => $userID,
                'name' => 'world',
//                'file_content' => file_get_contents($_FILES['upload_file']['tmp_name']),
            ],
        ];

        echo json_encode($result);
    }

    /**
     * @needParse
     * [
     *      'title' => '获取多个用户姓名get',
     *      'description' => '获取多个用户姓名',
     *      'request_uri' => 'test_request/list_name.php',
     *      'method_type' => 'POST',
     *      'params' =>
     *      [
     *          ['name' => 'user_id','type' => '数组','require' => '是' , 'default' => '' , 'description' => '用户ID数组'],
     *      ],
     *      'return' =>
     *      [
     *          'code' => '状态码',
     *          'message' => '返回信息',
     *          'data' =>
     *          [
     *              ['user_id' => '用户ID','name' => '用户名称'],
     *          ],
     *      ]
     * ]
     */
    public function listName()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET')
        {
            echo json_encode(['不是get请求']);
            exit();
        }

        if (empty($_POST['user_id']))
        {
            echo json_encode(['user_id不能为空']);
            exit();
        }

        $userID = $_POST['user_id'];

        $data = [];
        foreach ($userID as $k => $v)
        {
            $data[] = ['user_id' => $v,'name' => 'hello_'.$k];
        }

        $result = [
            'code' => 200,
            'message' => 'ok',
            'data' => $data,
        ];

        echo json_encode($result);
    }
}