<?php

use OldTown\Workflow\Basic\BasicWorkflow;
use OldTown\Workflow\Spi\Memory\MemoryWorkflowStore;
use OldTown\Workflow\Loader\ArrayWorkflowFactory;
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
                    'name' => ArrayWorkflowFactory::class,
                    'options' => [
                        'reload' => true,
                        'workflows' => [
                            'test' => [
                                'location' => __DIR__ . '/../../../../../../../../config/workflow/example.xml'
                            ]
                        ]
                    ]
                ],
                'resolver' => DefaultVariableResolver::class,
            ]
        ],



        'managers' => [
            'test_create_manager' => [
                'configuration' => 'default',
                'name' => BasicWorkflow::class
            ]
        ]
    ]
];