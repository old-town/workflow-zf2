<?php
/**
 * @link    https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2;


use OldTown\Workflow\ZF2\Options\ModuleOptions;
use OldTown\Workflow\ZF2\Options\ModuleOptionsFactory;
use OldTown\Workflow\ZF2\Factory\BasicWorkflowFactory;
use OldTown\Workflow\Basic\BasicWorkflow;
use OldTown\Workflow\Spi\Memory\MemoryWorkflowStore;
use OldTown\Workflow\Loader\XmlWorkflowFactory;
use OldTown\Workflow\Util\DefaultVariableResolver;


return [
    'service_manager' => [
        'factories' => [
            ModuleOptions::class => ModuleOptionsFactory::class,
            BasicWorkflow::class       => BasicWorkflowFactory::class
        ],
        'aliases' => [
            'basicWorkflow' => BasicWorkflow::class
        ]
    ],
    'workflow_zf2'    => [
        'configuration' => [
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
        ],



        'workflows' => [

        ]
    ]
];