<?php
define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT'] . '/');
require_once ROOT_DIR . 'vendor/autoload.php';

// 1.获取参数
$url = $_POST['url'];
$methodType = strtoupper($_POST['method_type']);
$cookie = $_POST['cookie'];
$header = $_POST['header'];
$arrayParam = isset($_POST['array_param']) ? $_POST['array_param'] : [];
$fileParam = isset($_POST['file_param']) ? $_POST['file_param'] : [];

unset($_POST['url']);
unset($_POST['method_type']);
unset($_POST['cookie']);
unset($_POST['header']);
unset($_POST['array_param']);
unset($_POST['file_param']);

// 2.处理参数
$output = [];

foreach ($_POST as $k => $v)
{
    // 处理数组型参数
    if (in_array($k, $arrayParam))
    {
        if (!empty($v))
        {
            if (is_null(json_decode($v, true)))
            {
                echo json_encode(['test_error' => ['remark' => '测试系统专属返回值', 'error' => '参数格式错误：' . $k . '解码后不是数组']]);
                exit();
            } else
            {
                $_POST[$k] = json_decode($v, true);
            }
        }
    }

    // 处理文件型参数
    if (in_array($k, $fileParam) && !empty($v))
    {
        if ($methodType != 'POST')
        {
            echo json_encode(['test_error' => ['remark' => '测试系统专属返回值', 'error' => '上传文件请用POST请求']]);
            exit();
        }

        if (!is_null(json_decode($v, true)))
        {
            $files = json_decode($v, true);

            foreach ($files as $kFile => $file)
            {
                if (is_file($file))
                {
                    // 文件数据
                    $output[] = [
                        'name' => $file,
                        'contents' => fopen($file, 'r'),
                        'filename' => '', // 为空则使用原文件名
                    ];
                } else
                {
                    echo json_encode(['test_error' => ['remark' => '测试系统专属返回值', 'error' => $file . '文件不存在']]);
                    exit();
                }
            }
        } else
        {
            echo json_encode(['test_error' => ['remark' => '测试系统专属返回值', 'error' => $v . 'json格式有误']]);
            exit();
        }
    }
}

// 3.设置options
if ($methodType == 'GET') // GET请求数据
{
    $options['query'] = $_POST;
} else // POST请求数据
{
    // POST数据之多维数组处理
    $flatten = function ($array, $original_key = '') use (&$flatten)
    {
        $output = [];
        foreach ($array as $key => $value)
        {
            $new_key = $original_key;
            if (empty($original_key))
            {
                $new_key .= $key;
            } else
            {
                $new_key .= '[' . $key . ']';
            }

            if (is_array($value))
            {
                $output = array_merge($output, $flatten($value, $new_key));
            } else
            {
                $output[$new_key] = $value;
            }
        }
        return $output;
    };

    $flat_array = $flatten($_POST);

    foreach ($flat_array as $key => $value)
    {
        // 非文件数据
        $output[] = [
            'name' => $key,
            'contents' => $value
        ];
    }

    // 设置options
    $options['multipart'] = $output;
}

// 4.发起请求
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

try
{
    if (!empty($cookie))
    {
        $parseUrl = parse_url($url);
        $domain = $parseUrl['host'];
        // 过滤cookie空格
        $tempCookies = explode(';', str_replace([' '], '', $cookie));

        $cookies = [];
        foreach ($tempCookies as $cookieStr)
        {
            list($cookieKey, $cookieValue) = explode('=', $cookieStr);
            $cookies[$cookieKey] = $cookieValue;
        }

        $cookieJar = CookieJar::fromArray($cookies, $domain);

        $options['cookies'] = $cookieJar;
    }

    if (!empty($header))
    {
        if (is_null(json_decode($header, true)))
        {
            echo json_encode(['test_error' => ['remark' => '测试系统专属返回值', 'error' => '参数格式错误：Header头数据解码后不是数组']]);
            exit();
        } else
        {
            $options['headers'] = json_decode($header, true);
        }
    }

    // 返回调试地址
    $query = http_build_query($_POST);
    $debugUrl = $url . '?' . $query;

    $client = new Client();
    $response = $client->request($methodType, $url, $options);
    $body = $response->getBody();

    $result = json_decode($body, true);

    // 如果不是返回json，则直接输出$body。暂用于输出php错误信息
    if (is_null($result))
    {
//        $result['test_error'] = ['remark' => '测试系统专属返回值', 'url' => $body];
        echo $body;
    } else
    {
        $result['debug_url'] = ['remark' => '测试系统专属返回值', 'url' => $debugUrl, 'decode_url' => urldecode($debugUrl)];
    }

    echo json_encode($result);

} catch (Exception $e)
{
    echo json_encode(['test_error' => ['remark' => '测试系统专属返回值', 'error' => $e->getMessage()]]);
}


