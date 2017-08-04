<?php
header('Content-type:text/html;charset=utf-8');

define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT'] . '/');
require_once ROOT_DIR . 'core/ApiDoc.php';

$apiDoc = new ApiDoc();

// 提取注释
$projectDocs = $apiDoc->listApi();