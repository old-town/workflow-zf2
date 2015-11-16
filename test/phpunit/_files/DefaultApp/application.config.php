<?php

use \OldTown\Workflow\ZF2\PhpUnit\TestData\TestPaths;

return [
    'modules' => [
        'OldTown\\Workflow\\ZF2'
    ],
    'module_listener_options' => [
        'module_paths' => [
            'OldTown\\Workflow\\ZF2' => TestPaths::getPathToModule()
        ],
        'config_glob_paths' => []
    ]
];