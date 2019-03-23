<?php

/**
 * Returns a string containing HTML JS script tags loaded from the 
 * given file paths.
 * 
 * 
 * NOTE: If dev is set to true, your browser MUST support ES6 module imports.
 *       DO NOT set isDev = true globally. Not all browsers support imports.
 *       Always limit isDev to your user. EX: isDev = Auth::getAuthUser() == <your ubit>
 * 
 * @param  isDev If true, src files will be loaded as modules. False, compiled files will be loaded.
 * @param  currentPage 
 * @param  appPath Path of app. Typically just __DIR__
 * @param  modulesPath 
 * @param  pagesPath
 * @param  compiledPath
 * @param  version (optional) Appends ?v=$version to end of script to force browser to re-cache 
 *
 * @throws Exception If missing required params
 * @return String Contains html js script tags
 */
function loadJS(array $params) {
    $required_params = array(
        "isDev", "currentPage", "appPath", "modulesPath", "pagesPath", "compiledPath"
    );
    foreach($required_params as $i => $param) {
        if(!array_key_exists($param, $params))
            throw new Exception("loadJS missing param: $param");
    }

    extract($params);

    $modulesAbsPath     = $appPath .'/'. $modulesPath;
    $currentPageAbsPath = $appPath .'/'. $pagesPath . $currentPage . '/';
    $compiledAbsPath    = $appPath .'/'. $compiledPath;

    $scriptSrc = array();

    if (!$isDev && file_exists($compiledAbsPath  . $currentPage . '.js')) {

        //Load single compiled js file 
        $scriptSrc[] = $compiledAbsPath  . $currentPage . '.js';

    } else if ($isDev && file_exists($currentPageAbsPath . '/index.js')) {

        //Load modules
        $scriptSrc = array_merge($scriptSrc, folderJS($modulesAbsPath));

        //Load page js
        $scriptSrc = array_merge($scriptSrc, folderJS($currentPageAbsPath));

    }

    //Generate and return script HTML
    $scriptTags_v = "";
    $version = isset($version) ? "?v=".intval($version) : "";

    foreach($scriptSrc as $i => $src) {
        $srcPath = substr($src, 11); //Removes /htdocs/www
        $type = $isDev ? "module" : "application/javascript";
        $scriptTags_v .= ("<script type='$type' src='$srcPath$version'></script>");
    }

    return $scriptTags_v;
}

/**
 * Returns an array containing the absolute paths of all js files within the given folder and sub folders.
 * 
 * @param  absolutePath Starting dir to recursively find js files 
 * @param  depth Current recursion depth. Limited to 10 levels
 *
 * @return Array Absolute path of all js files
 */
function folderJS($absolutePath, $depth = 0) {
    $files = scandir($absolutePath);
    $retVal = array();
    foreach($files as $i => $file) {
        if($file == "." || $file == '..') continue;

        if(is_dir($absolutePath . $file) && $depth < 10) {

            $retVal = array_merge($retVal, folderJS($absolutePath . $file .'/', $depth + 1));

        } else {

            if(pathinfo($absolutePath . $file, PATHINFO_EXTENSION) == "js") {
                $retVal[] = $absolutePath . $file;
            }

        }

    }
    return $retVal;
}
