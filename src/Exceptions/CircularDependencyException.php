<?php

namespace ClosureLoader\Exceptions;

use \ClosureLoader\DependencyNode;
use Exception;

class CircularDependencyException extends Exception {

    public function __construct(DependencyNode $node, string $path) {
        parent::__construct("File '$node->path' contains circular dependency with '$path'");
    }

}