<?php

namespace ClosureLoader;

use ClosureLoader\Paths as p;

class Loader {

    private $version;
    private $basePath;
    private $compiled_path;
    private $src_path;
    private $dev = true;

    public function __construct(array $settings) 
    {        
        $this->src_path       = p::validate($settings['sources']);
        $this->compiled_path  = p::validate($settings['compiled']);
        $this->basePath = $settings["url_base"];
        $this->version = isset($settings['version'])
            ? intval($settings['version'])
            : 0;
        if(isset($settings['dev']))
            $this->dev = $settings['dev'];
    }

    public function script(string $path) 
    {
        return $this->dev 
            ? $this->fromSource($path)
            : $this->fromCompiled($path);
    }

    private function fromSource(string $path) 
    {
        $dependecies = new DependencyGraph($this->src_path . $path);
        $files = $dependecies->toArray();
        $rel_paths = p::relative($files, $this->basePath);
        return p::toScriptTag($rel_paths, $this->dev);
    }

    private function fromCompiled(string $path) 
    {
        $compiled_name = preg_replace("/\//", "$", $path);
        $compiled_abs = $this->compiled_path . $compiled_name;
        $rel_js = p::toRelative($compiled_abs, $this->basePath);
        return "
            <script src='$rel_js?v={$this->version}'></script>        
        ";
    }

}