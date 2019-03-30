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

    /**
     * Returns an array containing the absolute paths of all js files within the given folder and sub folders.
     * 
     * @param  absolutePath Starting dir to recursively find js files 
     * @param  depth Current recursion depth. Limited to 10 levels
     *
     * @return Array Absolute path of all js files
     */
    public static function folderJS($absolutePath, $depth = 0) {
        $absolutePath = static::validate($absolutePath);

        $files = scandir($absolutePath);
        $retVal = array();
        foreach($files as $i => $file) {
            if($file == "." || $file == '..') continue;

            if(is_dir($absolutePath . $file) && $depth < 10) {

                $retVal = array_merge($retVal, static::folderJS($absolutePath . $file .'/', $depth + 1));

            } else {

                if(pathinfo($absolutePath . $file, PATHINFO_EXTENSION) == "js") {
                    $retVal[] = $absolutePath . $file;
                }

            }

        }
        return $retVal;
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

    public function relative($paths, $uri) {
        $retVal = [];

        foreach($paths as $path) {
            $retVal[] = static::toRelative($path, $uri);
        }

        return $retVal;
    }

    public function toRelative($path, $uri) {
        $base = preg_replace( '/\//', '\/', $uri->getBasePath() );
        $matches = [];
        preg_match("/(.*($base.*))/", $path, $matches );
        return $matches[2];
    }


    
}