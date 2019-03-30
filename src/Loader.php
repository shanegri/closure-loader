<?php

namespace ClosureLoader;

use ClosureLoader\Paths as p;

class Loader {

    private $uri;
    private $compiled_path;
    private $modules_path;
    private $src_path;
    private $dev = true;

    public function __construct(array $settings, $uri) {        
        $this->src_path       = p::validate($settings['sources']);
        $this->compiled_path  = p::validate($settings['compiled']);
        $this->modules_path   = p::validate($settings['modules']);

        if(isset($settings['dev']))
            $this->dev = $settings['dev'];

        $this->uri = $uri;
    }

    public function script($path) {
        return $this->dev 
            ? $this->fromSource($path)
            : $this->fromCompiled($path);
    }

    private function fromSource($path) {
        $dependecies = new DependencyGraph($this->src_path . $path);

        $files = $dependecies->toArray();

        $rel_paths = p::relative($files, $this->uri);

        return p::toScriptTag($rel_paths, $this->dev);
    }

    private function fromCompiled($path) {
        $src_js = $this->compiled_path . $path . '.js';
        $rel_js = p::toRelative($src_js, $this->uri);
        return "
            <script src='$rel_js'></script>        
        ";
    }





}