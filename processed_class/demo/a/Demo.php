<?php
/**
 *
 * @needParse
 * [
 *      'title' => 'demo控制器',
 *      'description' => 'demo接口描述'
 * ]
 */
class Demo
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
	public function getName(){}

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
	public function setName(){}

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
	public function listName(){}

}
?>