<?php

namespace ClosureLoader;

use Exception;

class Paths {

    public static function validate($path) 
    {
        $path = realpath($path) . '/';
        if( !$path ) 
            throw new Exception("Invalid path $path"); 

        return $path;
    }

    public static function endsWith( $str, $sub ) 
    {
        return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
    }

    public static function toScriptTag(array $paths, $isDev) 
    {
        $retVal = "";
        foreach($paths as $path) {
            $retVal .= "<script type='module' src='$path'></script>";
        }
        return $retVal;
    }

    public static function relative(array $paths, $basePath) 
    {
        $retVal = [];
        foreach($paths as $path) {
            $retVal[] = static::toRelative($path, $basePath);
        }
        return $retVal;
    }

    public static function toRelative($path, $basePath) 
    {
        if(in_array($basePath, ["/", ""])) {
            $base = preg_replace( '/\//', '\/', getcwd() );
            return preg_replace("/$base/", "", $path);
        }
        $base = static::pathToRE($basePath);
        $matches = [];
        preg_match("/(.*($base.*))/", $path, $matches );
        if( count($matches) != 0 ) {
            return $matches[2];
        } else {
            $root = preg_replace("/$base/", "", getcwd());
            return preg_replace("/".static::pathToRE($root)."/", "", $path);
        }
    }

    private static function pathToRE($path) 
    {
        return preg_replace( '/\//', '\/', $path );
    }



    
}