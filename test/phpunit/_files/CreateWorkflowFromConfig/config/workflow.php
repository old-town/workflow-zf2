<?php


use OldTown\Workflow\Spi\Memory\MemoryWorkflowStore;
use OldTown\Workflow\Loader\XmlWorkflowFactory;
use OldTown\Workflow\Util\DefaultVariableResolver;

return [
    'workflow_zf2'    => [
        'configurations' => [
            'default' => [
                'persistence' => [
                    'name' => MemoryWorkflowStore::class,
                    'options' => [

                    ]
                ],
                'factory' => [
                    'name' => XmlWorkflowFactory::class,
                    'options' => [

                    ]
                ],
                'resolver' => [
                    'name' => DefaultVariableResolver::class,
                    'options' => [

                    ]
                ]
            ]
        ],



        'managers' => [
            'test_create_manager' => [
                'configuration' => 'default',
                'workflows' => [

                ]
            ]
        ]
    ]
];