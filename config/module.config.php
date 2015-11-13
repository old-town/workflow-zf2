<?php
/**
 * @link https://github.com/old-town/workflow-zf2
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\ZF2;

use OldTown\Workflow\ZF2\Options\ModuleOptions;
use OldTown\Workflow\ZF2\Options\ModuleOptionsFactory;


return [
    'service_manager' => [
        'factories' => [
            ModuleOptions::class => ModuleOptionsFactory::class
        ]
    ]
];