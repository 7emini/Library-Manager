<?php
abstract class Base{

    public static function Run(){
        self::pageInit();
        self::dirInit();
        self::autoLoad();
        self::readConf();
        self::getRequest();
        self::distributeRequest();
    }

    /**
     * 1.开启session
     * 2.设置header
     */
    private static function pageInit(){
        header("Content-Type:text/html;charset:UTF-8");
        session_start();
    }
    
    /**
     * 定义路径常量
     */
    private static function dirInit(){
        //系统分隔符
        define("DS",DIRECTORY_SEPARATOR);
        //当前工作目录
        define("ROOT",getcwd().DS);
        // var_dump(ROOT);
    }

    /**
     * 配置自动加载规则
     */
    private static function autoLoad(){
        spl_autoload_register(function($className){
            //将目录反斜线替换成系统分隔符
            $className = str_replace("\\",DS,$className);
            //获取类文件路径名
            $files = array(
                "Controller.class.php",
                ".class.php",
            );
            //循环包含需要的文件
            foreach($files as $file){
                //拼接文件路径
                $fileName = ROOT . $className . $file;
                //判断文件是否存在
                if(file_exists($fileName)){
                    //包含文件
                    require_once($fileName);
                    break;
                }
            }
        });
    }

    /**
     * 读取配置文件
     */
    private static function readConf(){
        //将配置信息存入全局变量中
        $GLOBALS['conf'] = require_once("./Base/Conf.php");
    }

    /**
     * 获取请求信息
     */
    private static function getRequest(){
        $p = isset($_GET["p"]) ? $_GET['p'] : $GLOBALS['conf']['default_plantform'];
        $c = isset($_GET["c"]) ? $_GET['c'] : $GLOBALS['conf']['default_controller'];
        $a = isset($_GET["a"]) ? $_GET['a'] : $GLOBALS['conf']['default_action'];
        define("P",$p);
        define("C",$c);
        define("A",$a);
        //定义视图文件路径
        define("VIEWPATH",ROOT.P.DS."View".DS);
    }

    /**
     * 请求分发
     */
    private static function distributeRequest(){
        //匹配类和方法
        $className = "\\" . P . "\\Controller\\" . C;
        //  \Admin\Controller\Index
        $action = A;

        //判断类是否存在
        if(class_exists($className)){
            $Controller = new $className;
        }else{
            echo "c参数错误，没有对应的类";
            die();
        }

        //判断该类中的方法是否存在
        if(method_exists($Controller,$action)){
            $Controller->$action();
        }else{
            echo "a参数错误，没有对应的方法";
            die();
        }
    }

}