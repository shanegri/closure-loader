<?php

namespace ClosureLoader;

use JsonSerializable;

class DependencyNode implements JsonSerializable {

    public $path;
    private $folder;

    public $children = [];

    public function __construct($path) {
        $this->path = $path;
        $this->folder = dirname($path) . '/';
    }

    public function addChild(DependencyNode $child) {
        $this->children[] = $child;
    }

    //Assumes path starts with ./
    public function getImportPaths() {
        $contents = file_get_contents($this->path);
        $matches = [];
        preg_match_all("/(import.*from\s+(('.*')|(\".*\")))/", $contents, $matches);
        $folder = $this->folder;

        return array_map( function($match) use ($folder) {
            $rel_path = substr($match, 1, strlen($match) - 2);
            return realpath($this->folder . $rel_path);
        }, $matches[2]);
    }

    public function jsonSerialize() {
        return $this->path;
    }

}