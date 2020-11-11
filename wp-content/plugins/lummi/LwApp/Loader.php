<?php
/**
 * Autoloader for new classes and namespaces
 */

namespace LW;

class Loader
{
    private static $namespace = array();

    private function __construct()
    {
    }

    public static function registerAutoLoad(){
        spl_autoload_register(array('\LW\Loader','autoLoad'));
    }

    public static function autoLoad($class){
        self::loadClass($class);
    }

    public static function loadClass($class){
        foreach (self::$namespace as $key => $val){
            if(strpos($class, $key) === 0){
                $file = realpath(substr_replace(str_replace('\\', DIRECTORY_SEPARATOR, $class), $val, 0, strlen($key)).'.php');
                if($file && is_readable($file)){
                    include $file;
                }else{
                    throw new \Exception('File cannot be included:' . $file);
                }
                break;
            }
        }
    }

    public static function registerNamespace($namespace,$path){
        $namespace = trim($namespace);
        if(strlen($namespace) > 0){
            if(!$path){
                throw new \Exception('Ivalid path');
            }
            $_path = realpath($path);
            if($_path && is_dir($_path) && is_readable($_path)){
                self::$namespace[$namespace.'\\'] = $_path . DIRECTORY_SEPARATOR;
            }else{
                throw new \Exception('Namespace directory read error' . $path);
            }
        }else{
            throw new \Exception('Invalid namespace:' . $namespace);
        }
    }
}