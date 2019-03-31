<?php

namespace ClosureLoader;

use ClosureLoader\Paths as p;

class Loader {

    private $version;

    private $basePath;
    private $compiled_path;
    private $src_path;
    private $dev = true;

    public function __construct(array $settings) {        
        $this->src_path       = p::validate($settings['sources']);
        $this->compiled_path  = p::validate($settings['compiled']);
        
        $this->basePath = $settings["base"];

        $this->version = isset($settings['version'])
            ? intval($settings['version'])
            : 0;

        if(isset($settings['dev']))
            $this->dev = $settings['dev'];

    }

    public function script($path) {
        return $this->dev 
            ? $this->fromSource($path)
            : $this->fromCompiled($path);
    }

    private function fromSource($path) {
        $dependecies = new DependencyGraph($this->src_path . $path);

        $files = $dependecies->toArray();
        
        $basePath = $this->basePath;
        $isDev = $this->dev;

        $rel_paths = p::relative($files, $basePath);

        return p::toScriptTag($rel_paths, $isDev);
    }

    private function fromCompiled($path) {
        $compiled_name = preg_replace("/\//", "$", $path);
        $compiled_abs = $this->compiled_path . $compiled_name;

        $basePath = $this->basePath;

        $rel_js = p::toRelative($compiled_abs, $basePath);
        return "
            <script src='$rel_js?v={$this->version}'></script>        
        ";
    }





}