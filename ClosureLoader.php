<?php

require_once __DIR__ . "/src/Loader.php";
require_once __DIR__ . "/src/Paths.php";
require_once __DIR__ . "/src/DependencyNode.php";
require_once __DIR__ . "/src/DependencyGraph.php";

// Use this file when not using composer

use ClosureLoader\Loader;

class ClosureLoader extends Loader {

    public function __construct(array $settings) 
    {
        parent::__construct($settings);
    }

}


