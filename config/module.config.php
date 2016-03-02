<?php
/**
 * @link    https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2;


$config = [
    'workflow_zf2'    => [
        'configurations' => [

        ],
        'managers' => [

        ],
        'manager_aliases' => [

        ]
    ]
];


return array_merge_recursive(
    include __DIR__ . '/router.config.php',
    include __DIR__ . '/serviceManager.config.php',
    $config
);