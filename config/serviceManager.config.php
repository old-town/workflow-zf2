<?php
/**
 * @link    https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2;

use OldTown\Workflow\ZF2\Options\ModuleOptions;
use OldTown\Workflow\ZF2\Options\ModuleOptionsFactory;
use OldTown\Workflow\ZF2\Factory\AbstractWorkflowFactory;
use OldTown\Workflow\Basic\BasicWorkflow;
use OldTown\Workflow\ZF2\Factory\BasicWorkflowFactory;
use OldTown\Workflow\ZF2\Factory\WorkflowServiceFactory;
use OldTown\Workflow\ZF2\ServiceEngine\Workflow;

return [
    'service_manager' => [
        'factories' => [
            ModuleOptions::class => ModuleOptionsFactory::class,
            BasicWorkflow::class => BasicWorkflowFactory::class,
            Workflow::class => WorkflowServiceFactory::class

        ],
        'abstract_factories' => [
            AbstractWorkflowFactory::class => AbstractWorkflowFactory::class
        ]
    ],
];

