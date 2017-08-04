<?php
/**
 * 接口测试入口文件
 */
require_once './core/common.php';
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <title>API测试</title>

    <!-- Bootstrap -->
    <link href="./assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="./assets/treeview/jquery.treeview.css"/>
    <link rel="stylesheet" href="./assets/treeview/screen.css"/>

    <script type="text/javascript" src="./assets/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="./assets/treeview/jquery.cookie.js"></script>
    <script type="text/javascript" src="./assets/treeview/jquery.treeview.js"></script>
    <script type="text/javascript" src="./assets/treeview/demo.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <!--<script src="https://cdn.bootcss.com/html5shiv/3.7.3/html5shiv.min.js"></script>-->
    <!--<script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>-->
    <![endif]-->

</head>
<body>
<div id="main">
    <div class="row">
        <div class="col-md-2">
            <div id="treecontrol">
                <a title="Collapse the entire tree below" href="#"><img src="./assets/images/minus.gif"/> 折叠 </a>
                <a title="Expand the entire tree below" href="#"><img src="./assets/images/plus.gif"/> 展开 </a>
                <a title="Toggle the tree below, opening closed branches, closing open branches" href="#"> 切换 </a>
            </div>
            <ul id="black" class="treeview-black">
                <?php foreach ($projectDocs as $projectName => $projectDoc): ?>
                    <li>
                        <span>项目名：<?php echo $projectDoc['name']; ?></span>
                        <ul>
                            <?php foreach ($projectDoc['controllers'] as $classIndex => $singleClassDoc): ?>
                                <li>
                                    <?php
                                    $controller = key($singleClassDoc);
                                    $singleClassDoc = current($singleClassDoc);
                                    ?>
                                    <span title="<?php echo $singleClassDoc['description']?>">接口组：<?php echo $singleClassDoc['title'] ?></span>
                                    <?php foreach ($singleClassDoc['methods'] as $methodIndex => $method): ?>
                                        <ul>
                                            <li class="api" data-id="<?php echo $controller . '_' . $methodIndex; ?>"
                                                data-project-name="<?php echo $projectName; ?>"
                                                data-index="<?php echo $classIndex; ?>"
                                                data-controller="<?php echo $controller; ?>"
                                                data-method="<?php echo $methodIndex; ?>"
                                                data-title="<?php echo isset($method['title']) ? $method['title'] : '' ?>">
                                                <span><a><?php echo isset($method['title']) ? $method['title'] : '' ?></a></span>
                                            </li>
                                        </ul>
                                    <?php endforeach; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-md-10">
            <ul id="api_tab" class="nav nav-tabs">
                <!-- tab页标题 -->
            </ul>
            <div id="api_tab_content" class="tab-content" style="height: 880px">
                <!-- tab页内容 -->
            </div>
        </div>
    </div>
</div>

<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="./assets/bootstrap/js/bootstrap.min.js"></script>
<script>
    $(function () {
        // 打开tab页
        $(".api").on("click", function () {
            var $idName = $(this).data("id");
            var $title = $(this).data("title");
            var $projectName = $(this).data("project-name");
            var $index = $(this).data("index");
            var $controller = $(this).data("controller");
            var $method = $(this).data("method");

            if ($("#li_" + $idName).length > 0) { // tab页存在，则切换
                $("#li_" + $idName).siblings().removeClass("active");
                $("#li_" + $idName).addClass("active");

                $("#div_" + $idName).siblings().removeClass("in").removeClass("active");
                $("#div_" + $idName).addClass("in").addClass("active");
            } else {
                // 新增tab页
                $("#api_tab").append("<li data-id='" + $idName + "' id='li_" + $idName + "'><a style='display:inline-block;' href='#div_" + $idName + "' data-toggle='tab'>" + $title + "<span class='close' style='margin-left:10px;margin-top:-5px;z-index:999;'> x</span></a> </li>");

                $("#li_" + $idName).siblings().removeClass("active");
                $("#li_" + $idName).addClass("active"); // 显示对应tab页

                // 新增tabContent页
                $("#api_tab_content").append("<div class='tab-pane fade' id='div_" + $idName + "' style='height: 100%'>" +
                    "<iframe id='zt_text_" + $idName + "' src='./api_detail.php?project_name=" + $projectName + "&index="+ $index +"&controller=" + $controller + "&method=" + $method + "' width='100%' height='100%' frameborder='0'></iframe>" +
                    "</div>");
                $("#div_" + $idName).siblings().removeClass("in").removeClass("active");
                $("#div_" + $idName).addClass("in").addClass("active"); // 显示对应tabContent页
            }

            // 关闭tab
            $(".close").on("click", function () {
                var $currentIdName = 'div_' + $(this).parent().parent().data("id"); // 当前tab对应的div元素的id值
                var $prevIdName = $(this).parent().parent().prev().attr("id"); // 前一个tab对应的li元素的id值
                var isVisible = $("#" + $currentIdName).is(":visible"); // 当前tab对应的div元素是否可见

                $(this).parent().parent().remove(); // 移除当前tab对应的li
                $("#" + $currentIdName).remove(); // 移除当前tab对应的div

                // 如果当前tab对应的div元素可见，则切换到前一个tab
                if (isVisible) {
                    $("#" + $prevIdName + " a:last").tab("show");
                }
            })
        });

        // 默认打开第一项
        $("#black li:first ul:first li:first ul:first li:first").trigger("click");

    })
</script>
</body>
</html>