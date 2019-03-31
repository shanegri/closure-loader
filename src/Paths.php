<?php

namespace ClosureLoader;

use Exception;

class Paths {


    public static function validate($path) {

        $path = realpath($path) . '/';
    
        if( !$path ) 
            throw new Exception("Invalid path $path"); 

        return $path;
    }

    public static function endsWith( $str, $sub ) {
        return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
    }

    public static function toScriptTag(array $paths, $isDev) {
        $retVal = "";
        foreach($paths as $path) {
            $retVal .= "
                <script type='module' src='$path'></script>
            ";
        }
        return $retVal;
    }

    public function relative($paths, $basePath) {
        $retVal = [];

        foreach($paths as $path) {
            $retVal[] = static::toRelative($path, $basePath);
        }

        return $retVal;
    }

    public function toRelative($path, $basePath) {
        if(in_array($basePath, ["/", ""])) {
            $base = preg_replace( '/\//', '\/', getcwd() );
            return preg_replace("/$base/", "", $path);
        }

        $base = preg_replace( '/\//', '\/', $basePath );
        $matches = [];
        preg_match("/(.*($base.*))/", $path, $matches );
        return $matches[2];
    }


    
}