<?php

namespace ClosureLoader;

use \ClosureLoader\Paths as p;
use JsonSerializable;

class DependencyGraph implements JsonSerializable {

    private $root = null;
    private $used_paths = [];

    public function __construct(string $path) 
    {
        $root = new DependencyNode($path);
        $this->build($root);
    }

    /**
     * Fills a dependency graph starting at $node
     * 
     * @param DependencyNode $node Head of depedency graph to build
     * @return DependencyNode $node Head of build dependency graph
     */
    private function build(DependencyNode &$node) 
    {
        $this->used_paths[] = $node->path;
        $child_paths  = $node->getImportPaths();
        foreach($child_paths as $path) {
            if(!$this->contains($path)) {
                $new_node = new DependencyNode($path);
                $node->addChild($new_node);
                $this->build($new_node);
            } 
        }
        return $node;
    }

    public function contains(string $path) 
    {
        return in_array($path, $this->used_paths);
    }

    public function jsonSerialize() 
    {
        return ["entry" => $this->used_paths];
    }

    public function toArray() 
    {
        return $this->used_paths;
    }

}