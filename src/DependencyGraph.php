<?php

namespace ClosureLoader;

use \ClosureLoader\Exceptions\CircularDependencyException;
use \ClosureLoader\Paths as p;
use JsonSerializable;

class DependencyGraph implements JsonSerializable {

    private $root = null;

    private $used_paths = [];

    public function __construct($path) {
        $root = new DependencyNode($path);
        $this->build($root);
    }

    /**
     * Fills a dependency graph starting at $node
     * 
     */
    private function build(DependencyNode &$node) {
        $this->used_paths[] = $node->path;
        $child_paths  = $node->getImportPaths();

        foreach($child_paths as $path) {

            if(!$this->contains($path)) {
                $new_node = new DependencyNode($path);
                $node->addChild($new_node);
                $this->build($new_node);
            } else {
                throw new CircularDependencyException($node, $path);
            }

        }

        return $node;
    }

    public function contains($path) {
        return in_array($path, $this->used_paths);
    }


    public function jsonSerialize() {
        return ["entry" => 
            $this->used_paths
        ];
    }

    public function toArray() {
        return $this->used_paths;
    }

}