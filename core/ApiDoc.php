<?php

class ApiDoc
{
    private $configs;
    private $docParser;

    public function __construct()
    {
        $this->configs = require_once ROOT_DIR . 'config/config.php';

        require_once ROOT_DIR . 'core/DocParser.php';
        $this->docParser = new DocParser();
    }

    /**
     * 获取api接口文档信息列表
     */
    public function listApi()
    {
        $projectDoc = [];
        foreach ($this->configs['projects'] as $project => $projectDetail)
        {
            $apiDoc = [];
            foreach ($projectDetail['controllers'] as $controller)
            {
                $className = $controller['class_name']; // 类名
                $sourceFilePath = $projectDetail['un_processed_class'] . $controller['file_path']; // 类原路径

                if (!file_exists($sourceFilePath))
                {
                    die("文件[{$sourceFilePath}]不存在，请检查路径");
                }

                $newFilePath = $this->configs['processed_class'] . $project . '/' . $controller['file_path']; // 类新路径

                // 载入文件
                $includeFile = $this->getIncludeFile($sourceFilePath, $newFilePath, $className);
                if ($includeFile)
                {
                    require_once $includeFile;
                } else
                {
                    continue;
                }

                // 解析前检查
                if (!class_exists($className))
                {
                    die("文件[{$newFilePath}]内，类[{$className}]不存在，请检查类名");
                }
                $reflectionClass = new ReflectionClass($className);
                $classDoc = $reflectionClass->getDocComment(); // 类注释
                if (!$classDoc || strpos($classDoc, '@needParse') === false)
                {
                    die("解析类[{$className}]注释失败，请检查");
                }

                // 解析注释
                $apiDoc[] = $this->parseClassDoc($classDoc, $className, $sourceFilePath, $reflectionClass);

                // 是否保存简化类
                if ($this->configs['isSimplifyClassContent'])
                {
                    $classContent = $this->simplifyClassContent($reflectionClass, $className);
                    file_put_contents($newFilePath, $classContent);
                }
            }

            $projectDoc[$project] = ['name' => $projectDetail['name'], 'controllers' => $apiDoc];
        }

        return $projectDoc;
    }

    // 获取要载入的类文件
    public function getIncludeFile($sourceFilePath, $newFilePath, &$className)
    {
        if ($this->configs['isReadLocalClass'])
        {
            if (file_exists($newFilePath))
            {
                $className = explode('\\', $className);
                $className = end($className);

                return $newFilePath;
            } else
            {
                return false;
            }
        } else
        {
            // 过滤类内容
            $classContent = file_get_contents($sourceFilePath);
            $classContent = $this->filter($classContent);

            $this->mkDirs(dirname($newFilePath));
            if (!file_put_contents($newFilePath, $classContent))
            {
                die("创建或修改[{$newFilePath}]失败，请检查操作权限");
            }

            return $newFilePath;
        }
    }

    // 解析类注释，返回数组格式
    public function parseClassDoc($classDoc, $className, $sourceFilePath, ReflectionClass $reflectionClass)
    {
        $mockClassName = str_replace(['\\'], '_', $className); // 伪类名 下划线代替反斜杠

        // 需要解析
        if ($this->docParser->isNeedParse($classDoc))
        {
            $classDoc = $this->docParser->parse($classDoc);
            $classDoc = $classDoc['long_description'];
            $classDoc = '$classDoc =' . $classDoc . ';';
            eval($classDoc);
            $apiDoc[$mockClassName] = $classDoc;
        }

        $apiDoc[$mockClassName]['file_path'] = $sourceFilePath;

        // 解析接口类中方法的注释
        $apiDoc[$mockClassName]['methods'] = [];
        foreach ($reflectionClass->getMethods() as $index => $method)
        {
            $tempMethodDoc = $method->getDocComment(); // 方法注释
            $methodName = $method->getName(); // 方法名
            $this->docParser->clearParams();

            // 需要解析
            if ($this->docParser->isNeedParse($tempMethodDoc))
            {
                $tempMethodDoc = $this->docParser->parse($tempMethodDoc);
                $tempMethodDoc = $tempMethodDoc['long_description'];
                $tempMethodDoc = '$tempMethodDoc =' . $tempMethodDoc . ';';
                eval($tempMethodDoc);
                $apiDoc[$mockClassName]['methods'][$methodName] = $tempMethodDoc;
            }
        }

        return $apiDoc;
    }

    public function getConfig()
    {
        return $this->configs;
    }

    // 递归创建目录
    function mkDirs($dir)
    {
        if (!is_dir($dir))
        {
            if (!$this->mkDirs(dirname($dir)))
            {
                return false;
            }
            if (!mkdir($dir, 0777))
            {
                return false;
            }
        }
        return true;
    }

    // 过滤类中不需要的内容
    public function filter($fileContent)
    {
        $fileContent = $this->getClass($fileContent);
        $fileContent = $this->filterExtends($fileContent);

        return $fileContent;
    }

    // 获取纯粹的class内容
    public function getClass($fileContent)
    {
        if (($start = strpos($fileContent, 'class_start')) !== false)
        {
            $end = strpos($fileContent, 'class_end');
            if ($end === false)
            {
                die("缺少class_end标签");
            }
            $length = $end - $start;
            $fileContent = substr($fileContent, $start, $length);
            $fileContent = str_replace('class_start', '', $fileContent);
            return '<?php ' . $fileContent . "\r\n" . '?>';
        }

        return $fileContent;
    }

    // 过滤extends开始到第一个左花括号中间的字符
    public function filterExtends($fileContent)
    {
        if (($start = strpos($fileContent, 'extends')) !== false)
        {
            $end = strpos($fileContent, '{');
            $length = $end - $start;
            $fileContent = substr_replace($fileContent, '', $start, $length);
            return $fileContent;
        }

        return $fileContent;
    }

    // 简化类代码（简化后保存的类代码只包含类和方法的基本结构以及注释，没有任何业务代码，主要为了方便查看）
    public function simplifyClassContent(ReflectionClass $reflectionClass, $className)
    {
        $methodString = '';
        foreach ($reflectionClass->getMethods() as $index => $method)
        {
            $methodString .= "\r\n\t{$method->getDocComment()}\r\n\tpublic function {$method->getName()}(){}\r\n";
        }

        $className = explode('\\', $className);
        $className = end($className);

        $classDocComment = $reflectionClass->getDocComment();

        $classContent = <<<EOF
<?php
$classDocComment
class $className
{
        $methodString
}
?>
EOF;
        return $classContent;
    }
}
