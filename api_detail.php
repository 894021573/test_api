<?php
/**
 * 接口详情页
 */
require_once './core/common.php';

$projectDoc = $projectDocs[$_GET['project_name']]; // 当前项目信息

$classDocs = $projectDoc['controllers'][$_GET['index']]; // 当前项目所有控制器信息
$config = $apiDoc->getConfig();

$currentProjectConfig = $config['projects'][$_GET['project_name']]; // 当前项目配置信息
$projectUrl = $currentProjectConfig['host']; // 当前项目请求url

if (!isset($classDocs[$_GET['controller']]) || !isset($classDocs[$_GET['controller']]['methods'][$_GET['method']]))
{
    die('未传入对应的控制器或方法');
}

$controller = $classDocs[$_GET['controller']]; // 当前控制器信息
$method = $classDocs[$_GET['controller']]['methods'][$_GET['method']]; // 当前方法信息
$apiUrl = isset($method['request_uri']) ? $projectUrl . $method['request_uri'] : ''; // 接口请求url
$methodType = !empty($method['method_type']) ? strtoupper($method['method_type']) : 'GET'; // 请求类型

$ajaxUrl = $config['ajax_url']; // guzzle.php 请求url

?>
<html>
<head>
    <meta charset="utf-8">
    <title>API接口文档</title>
    <link href="./assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="./assets/css/style.css" rel="stylesheet" type="text/css">
    <script src="./assets/js/jquery-3.2.1.min.js" type="text/javascript"></script>
    <script src="./assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <link href="./assets/json/json.css" rel="stylesheet" type="text/css">

    <script src="./assets/json/c.js" type="text/javascript"></script>
    <script src="./assets/js/clipboard.js-master/dist/clipboard.min.js" type="text/javascript"></script>
    <link href="./assets/json/s.css" type="text/css" rel="stylesheet">
</head>
<body>

<div class="container" style="margin-left: 0;background-color: #eee">
    <p>接口名：<?php echo isset($controller['title']) ? $controller['title'] : ''; ?><?php echo "----------"?><?php echo isset($method['description']) ? $method['description'] : ''; ?></p>
    <p class="text-primary">路径：<?php echo isset($controller['file_path']) ? $controller['file_path'] : ''; ?></p>
    <p class="text-success">请求URL：<?php echo "<span class='text-danger'>【{$methodType}】</span>" . '    ' ."<span id='url'>$apiUrl</span>"; ?>&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-danger btn-sm" id="copy_url" data-clipboard-action="copy" data-clipboard-target="#url">点击复制</button></p>

    <ul id="myTab" class="nav nav-tabs">
        <li id="tab_info" class="active"><a href="#info" data-toggle="tab">接口详情</a></li>
        <li id="tab_test"><a href="#test" data-toggle="tab">在线测试</a></li>
    </ul>
    <div class="tab-content">
        <!-- 接口信息 -->
        <div class="tab-pane fade in active" id="info">
            <br>
            <div class="panel panel-primary" style="border-color: #00A881">
                <div class="panel-heading" style="border-color: #00A881;background-color: #00A881">
                    <h3 class="panel-title">接口参数</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <th>参数名字</th>
                            <th>类型</th>
                            <th>是否必须</th>
                            <th>默认值</th>
                            <th>描述</th>
                        </tr>
                        <?php $params = isset($method['params']) ? $method['params'] : [] ?>
                        <?php foreach ($params as $param): ?>
                            <tr>
                                <td><?php echo isset($param['name']) ? $param['name'] : ''; ?></td>
                                <td><?php echo isset($param['type']) ? $param['type'] : ''; ?></td>
                                <td><?php echo isset($param['require']) ? $param['require'] : ''; ?></td>
                                <td><?php echo isset($param['default']) ? $param['default'] : ''; ?></td>
                                <td><?php echo isset($param['description']) ? $param['description'] : ''; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
        </div>
        <!-- 接口信息 -->

        <!-- 接口测试 -->
        <div class="tab-pane fade in" id="test">
            <br>
            <!--head-->
            <div class="panel panel-primary" style="border-color: #00A881">
                <div class="panel-heading" style="border-color: #00A881;background-color: #00A881">
                    <h3 class="panel-title">接口参数</h3>
                </div>

                <div class="panel-body">
                    <form id="api_form" class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="url">接口地址</label>
                            <div class="col-sm-6">
                                <input class="form-control" type="text" name="url" id="url"
                                       value="<?php echo $apiUrl; ?>">
                            </div>
                            <div class="col-sm-4">
                                <button type="button" id="send" class="btn btn-success"
                                        data-loading-text="Loading..." autocomplete="off">发送请求
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="method_type">请求方式</label>
                            <div class="col-sm-6">
                                <select class="form-control" name="method_type" id="method_type">
                                    <option value="GET" <?php if ($methodType == 'GET')
                                    {
                                        echo 'selected';
                                    } ?>>GET
                                    </option>
                                    <option value="POST" <?php if ($methodType == 'POST')
                                    {
                                        echo 'selected';
                                    } ?>>POST
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-4"></div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="cookie">Cookie</label>
                            <div class="col-sm-6">
                                <textarea class="form-control" type="text" style="height: 100px"
                                          name="cookie" id="cookie"></textarea>
                                <p class="bg-warning">Cookie格式：a=1;b=2;c=3</p>
                            </div>
                            <div class="col-sm-4"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="header">Header</label>
                            <div class="col-sm-6">
                                <textarea class="form-control" type="text" style="height: 100px"
                                          name="header" id="header"></textarea>
                                <p class="bg-warning">
                                    Header数据先用数组表示，然后转json格式，如：{"User-Agent":"testing\/1.0","Accept":"application\/json","X-Foo":["Bar","Baz"]}</p>
                            </div>
                            <div class="col-sm-4"></div>
                        </div>
                        <?php $params = is_array($params) ? $params : [];
                        foreach ($params as $index => $param): ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo isset($param['name']) ? $param['name'] : '' ?></label>
                                <div class="col-sm-6">
                                    <input class="form-control" type="text"
                                           name="<?php echo isset($param['name']) ? $param['name'] : '' ?>"
                                           value="<?php echo isset($param['default']) ? $param['default'] : '' ?>"
                                           placeholder="类型：<?php echo $param['type'] . '---描述：' . $param['description'] ?>"/>
                                    <?php if ($param['type'] == 'array' || $param['type'] == '数组'): ?>
                                        <p class="bg-warning">数组型参数（支持多维数组）需转为json字符串：比如数组包含1,2,3，则转为json：[1,2,3]</p>
                                        <input type="hidden" name="array_param[]"
                                               value="<?php echo isset($param['name']) ? $param['name'] : '' ?>">
                                    <?php endif; ?>
                                    <?php if ($param['type'] == 'file' || $param['type'] == '文件'): ?>
                                        <p class="bg-warning">文件上传：将包含一个或多个文件绝对路径的数组转为json字符串</p>
                                        <input type="hidden" name="file_param[]"
                                               value="<?php echo isset($param['name']) ? $param['name'] : '' ?>">
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </form>
                </div>
            </div>

            <!--head-->
        </div>
        <!--接口测试-->
    </div>

    <div class="panel panel-primary" style="border-color: #00A881">
        <div class="panel-heading" style="border-color: #00A881;background-color: #00A881">
            <h3 class="panel-title">接口备注</h3>
        </div>
        <div class="panel-body">
            <?php echo isset($method['remark']) ? $method['remark'] : '' ?>
        </div>
    </div>

    <div class="panel panel-primary" style="border-color: #00A881;">
        <div class="panel-heading" style="border-color: #00A881;background-color: #00A881">
            <h3 class="panel-title">返回结果</h3>
        </div>
        <div class="panel-body" id="span_result">
            <div class="HeadersRow">
                <h3 id="HeaderSubTitle">JSON:</h3>
                <textarea id="RawJson"><?php echo isset($method['return']) ? json_encode($method['return']) : ''; ?>
                    </textarea>
            </div>
            <div id="ControlsRow">
                <input id="format_json" type="Button" value="格式化" onclick="Process()"/>
                <span id="TabSizeHolder">
                    缩进量
                        <select id="TabSize" onchange="TabSizeChanged()">
                            <option value="1">1</option>
                            <option value="2" selected="true">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                        </select>
                    </span>
                <label for="QuoteKeys">
                    <input type="checkbox" id="QuoteKeys" onclick="QuoteKeysClicked()" checked="true"/>
                    引号
                </label>&nbsp;
                <a href="javascript:void(0);" onclick="SelectAllClicked()">全选</a>
                &nbsp;
                <span id="CollapsibleViewHolder">
                  <label for="CollapsibleView">
                      <input type="checkbox" id="CollapsibleView" onclick="CollapsibleViewClicked()" checked="true"/>显示控制
                  </label>
                    </span>
                <span id="CollapsibleViewDetail">
                        <a href="javascript:void(0);" onclick="ExpandAllClicked()">展开</a>
                        <a href="javascript:void(0);" onclick="CollapseAllClicked()">叠起</a>
                        <a href="javascript:void(0);" onclick="CollapseLevel(3)">2级</a>
                        <a href="javascript:void(0);" onclick="CollapseLevel(4)">3级</a>
                        <a href="javascript:void(0);" onclick="CollapseLevel(5)">4级</a>
                        <a href="javascript:void(0);" onclick="CollapseLevel(6)">5级</a>
                        <a href="javascript:void(0);" onclick="CollapseLevel(7)">6级</a>
                        <a href="javascript:void(0);" onclick="CollapseLevel(8)">7级</a>
                        <a href="javascript:void(0);" onclick="CollapseLevel(9)">8级</a>
                    </span>
            </div>
            <div id="Canvas" class="Canvas"></div>
        </div>
    </div>

    <br>
    <div role="alert" class="alert alert-info">
        <strong>提示：此文档是由系统自动生成，如发现错误或疑问请告知开发人员及时修改</strong>
        <p>© Powered By 洪涛 </p>
    </div>
</div>

<!--<script type="text/javascript" src="./assets/json/m.js"></script>-->
<script type="text/javascript">
    $(function () {
        var clipboard = new Clipboard('#copy_url');
        clipboard.on('success', function (e) {
            console.log(e);
            alert("复制成功！")
        });
        clipboard.on('error', function (e) {
            console.log(e);
            alert("复制失败！请手动复制")
        });


        if ($("#test").is(":hidden")) {
            $("#RawJson").hide();
            $("#format_json").hide();
        }

        $("#tab_info").on("click", function () {
            $("#RawJson").hide();
            $("#format_json").hide();
        });

        $("#tab_test").on("click", function () {
            $("#RawJson").show();
            $("#format_json").show();
        });

        $("#format_json").trigger("click"); // 格式化json数据

        // 发送请求
        $("#send").on("click", function () {
            var $button = $(this);
            $.ajax({
                data: $("#api_form").serialize(),
                type: 'POST',
                dataType: 'html',
                url: '<?php echo $ajaxUrl?>',
                beforeSend: function () {
                    $button.button('loading');
                },
                success: function (data) {
                    $("#RawJson").val(data);
                    $("#format_json").trigger("click"); // 格式化json数据
                },
                complete: function () {
                    $button.button('reset');
                },
                error: function () {
                    alert('请求失败,请检查接口地址是否正确');
                }
            });
        })
    })

</script>
</body>
</html>
